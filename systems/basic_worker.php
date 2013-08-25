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
 * @author Bastian Bringenberg <mail@bastian-bringenberg.de>
 * @link https://github.com/bbnetz/FastBackup
 *
 */
abstract class basic_worker {

	protected $instancePath = '';

	protected $backupFilename = '';

	protected $tmpDir = '';

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
	}

	abstract public function run();

	protected function getTmpDir() {
		$tmp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'bb_backup-'.time().DIRECTORY_SEPARATOR;
		if(file_exists($this->tmpDir))
			$tmp = $this->getTmpDir();
		mkdir($tmp);
		return $tmp;
	}

	protected function writeFinalTar() {
		echo 'Final TAR created.'.PHP_EOL;
		exec('tar cfz '.$this->backupFilename.' '.$this->tmpDir.'*');
	}

	protected function saveMySQL($user, $pass, $db, $host='localhost') {
		echo 'MySQL File created:'.PHP_EOL;
		echo '  User:     '.$user.PHP_EOL;
		echo '  Database: '.$db.PHP_EOL;
		echo '  Host:     '.$host.PHP_EOL;
		exec('mysqldump -u'.$user.' -p'.$pass.' -h'.$host.' '.$db.' > '.$this->tmpDir.'mysql_'.$user.'_'.$db.'sql');
	}

	protected function saveFiles($path, $title) {
		if(!file_exists($path)) 
			die('Location from '.$path.' is not existing.');
		echo 'Location saved: '.$path.PHP_EOL;
		$tmpFilename = $this->tmpDir.'files_'.$title.'.tar.gz';
		exec('tar cfz '.$tmpFilename.' '.$path);
	}

}