<?php
/**
 * mediawikiWorker Class
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
 * Class mediawikiWorker
 * @author Bastian Bringenberg <mail@bastian-bringenberg.de>
 * @link https://github.com/bbnetz/FastBackup
 *
 */
class mediawiki_worker extends basic_worker{

	/**
	 * function run
	 * doing all the work to backup a mediawiki instance
	 *
	 * @return void
	 */
	public function run() {
		echo "run";
		$db = $this->fetchDatabase();
		$this->saveMySQL($db['user'], $db['pass'], $db['db'], $db['host']);
		$this->saveFiles($this->instancePath, 'WBB');
		$this->writeFinalTar();
	}

	/**
	 * function fetchDatabase
	 * includes mediawikis's LocalSettings.php to get all database informations
	 *
	 * @return array $db the required DB informations array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
	 */
	protected function fetchDatabase() {
		$db = array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
		$file = $this->instancePath.DIRECTORY_SEPARATOR.'LocalSettings.php';
		if(!file_exists($file))
			die('No MediaWiki found');
		$string = file_get_contents($file);
		$string = $this->cleanUpComments($string);
		$db['user'] = $this->fetchSingleValue('wgDBuser', $string);
		$db['pass'] = $this->fetchSingleValue('wgDBpassword', $string);
		$db['db'] = $this->fetchSingleValue('wgDBname', $string);
		$db['host'] = $this->fetchSingleValue('wgDBserver', $string);
		return $db;
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

}