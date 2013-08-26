<?php
/**
 * basicWorker Class
 * By Bastian Bringenberg <mail@bastian-bringenberg.de>
 *
 * #########
 * # USAGE #
 * #########
 *
 * See Readme File
 *
 * ###########
 * # Licence #
 * ###########
 *
 * See License File
 *
 * ##############
 * # Repository #
 * ##############
 *
 * Fork me on GitHub
 * https://github.com/bbnetz/FastBackup
 *
 *
 */

/**
 * Class basicWorker
 * @abstract
 * @author Bastian Bringenberg <mail@bastian-bringenberg.de>
 * @link https://github.com/bbnetz/FastBackup
 *
 */
abstract class basic_worker {

	/**
	 * @var string $instancePath the path to the installed service
	 */
	protected $instancePath = '';

	/**
	 * @var string $backupFilename the path to the backupFile
	 */
	protected $backupFilename = '';

	/**
	 * @var string $tmpDir the location where everything 
	 */
	protected $tmpDir = '';

	/**
	 * function __construct
	 * Constructor for all workers
	 *
	 * @param string $instancePath the path to the installed service
	 * @param string $backupFilename the path to the backupfile
	 * @return void
	 */
	public function __construct($instancePath, $backupFilename) {
		$this->instancePath = $instancePath;
		$this->backupFilename = $backupFilename;
		$this->tmpDir = $this->getTmpDir();
	}

	/**
	 * function __destruct
	 * fires when object is removed from memory
	 *
	 * @see http://stackoverflow.com/questions/1407338/a-recursive-remove-directory-function-for-php
	 * @return void
	 */
	public function __destruct() {
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->tmpDir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path)
    		$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
    	rmdir($this->tmpDir);
	}

	/**
	 * function run
	 *  
	 *
	 * @return void
	 */
	abstract public function run();

	/**
	 * function getTmpDir
	 * checks for a good temp dir
	 *
	 * @return void
	 */
	protected function getTmpDir() {
		$tmp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'bb_backup-'.time().DIRECTORY_SEPARATOR;
		if(file_exists($this->tmpDir))
			$tmp = $this->getTmpDir();
		mkdir($tmp);
		return $tmp;
	}

	/**
	 * function writeFinalTar
	 * writes the backup file itself
	 *
	 * @return void
	 */
	protected function writeFinalTar() {
		echo 'Final TAR created.'.PHP_EOL;
		exec('tar cfz '.$this->backupFilename.' '.$this->tmpDir.'*');
	}

	/**
	 * function saveMySQL
	 * Gets informations for a mysql connection and saves in temp directory
	 *
	 * @param string $user the database user
	 * @param string $pass the database password
	 * @param string $db the database itself
	 * @param string $host the database host
	 * @return void 
	 */
	protected function saveMySQL($user, $pass, $db, $host='localhost') {
		echo 'MySQL File created:'.PHP_EOL;
		echo '  User:     '.$user.PHP_EOL;
		echo '  Database: '.$db.PHP_EOL;
		echo '  Host:     '.$host.PHP_EOL;
		exec('mysqldump -u'.$user.' -p'.$pass.' -h'.$host.' '.$db.' > '.$this->tmpDir.'mysql_'.$user.'_'.$db.'.sql');
	}

	/**
	 * function saveFiles
	 * creates a tarball of given files under temp directory
	 *
	 * @param string $path the origin to backup
	 * @param string $title the title for the tar file
	 * @return void
	 */
	protected function saveFiles($path, $title) {
		if(!file_exists($path)) 
			die('Location from '.$path.' is not existing.');
		echo 'Location saved: '.$path.PHP_EOL;
		$tmpFilename = $this->tmpDir.'files_'.$title.'.tar.gz';
		exec('tar cfz '.$tmpFilename.' '.$path);
	}

	/**
	 * function cleanUpComments
	 * cleaning up PHP files from comments so that only real used informations remain
	 *
	 * @param string $string the php files content as string
	 * @return string the cleaned up PHP File
	 */
	protected function cleanUpComments($string) {
		$string = preg_replace('|\#.*|', '', $string);
		$string = preg_replace('/\/\/.*/', '', $string);
		$string = preg_replace('|\/\*\*.*?\*\/|s', '', $string);
		return $string;
	}

	/**
	 * function fetchSingleValue
	 * fetches single attribute from $fileContent
	 *
	 * @param string $singleValue the value to search for
	 * @param string $fileContent the PHP scripts content to fetch the value from
	 * @return string the last found $singleValue in $fileContent
	 */
	protected function fetchSingleValue($singleValue, $fileContent) {
		preg_match_all('/\$'.$singleValue.'\s*=\s*(.*?);/', $fileContent, $tmp);
		return str_replace(array('"', "'"), array('', ''), $tmp[1][count($tmp[1])-1]);
	}

	/**
	 * function fetchSingleConstant
	 * fetches single attribute from $fileContent's Constatnt
	 *
	 * @param string $singleValue the constant to search for
	 * @param string $fileContent the PHP scripts content to fetch the value from
	 * @return string the last found $singleValue in $fileContent
	 */
	protected function fetchSingleConstant($singleValue, $fileContent) {
		preg_match_all('/define\\s*\\(\\s*[\\\'\\"]'.$singleValue.'[\\\'\\"]\\s*,\\s*[\\\'\\"](.*?)[\\\'\\"]/', $fileContent, $tmp);
		return str_replace(array('"', "'"), array('', ''), $tmp[1][count($tmp[1])-1]);
	}

	/**
	 * function secureCode
	 * removes insecure lines from code to ensure
	 *
	 * @todo remove file Arguments?
	 * @todo enable some requires
	 * @param string $string the code to secure
	 * @return string the cleaned code
	 */
	protected function secureCode($string) {
		$string = str_replace(array('<?php', '<?'), array('', ''), $string);
		$string = preg_replace('/require_once\s*\(.*?;/', '', $string);
		$string = preg_replace('/require\s*\(.*?;/', '', $string);
		$string = preg_replace('/include_once\s*\(.*?;/', '', $string);
		$string = preg_replace('/include\s*\(.*?;/', '', $string);
		$string = preg_replace('/eval\s*\(.*?;/', '', $string);
		return $string;
	}
}
