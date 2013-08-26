<?php
/**
 * wordpressWorker Class
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
 * Class wordpressWorker
 * @author Bastian Bringenberg <mail@bastian-bringenberg.de>
 * @link https://github.com/bbnetz/FastBackup
 *
 */
class wordpress_worker extends basic_worker{

	/**
	 * function run
	 * doing all the work to backup a wordpress instance
	 *
	 * @return void
	 */
	public function run() {
		$db = $this->fetchDatabase();
		$this->saveMySQL($db['user'], $db['pass'], $db['db'], $db['host']);
		$this->saveFiles($this->instancePath, 'WBB');
		$this->writeFinalTar();
	}

	/**
	 * function fetchDatabase
	 * includes wordpress' wp-config.php to get all database informations
	 *
	 * @return array $db the required DB informations array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
	 */
	protected function fetchDatabase() {
		$db = array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
		$file = $this->instancePath.DIRECTORY_SEPARATOR.'wp-config.php';
		if(!file_exists($file))
			die('No MediaWiki found');
		$string = file_get_contents($file);
		$string = $this->cleanUpComments($string);
		$string = $this->secureCode($string);
		eval($string);
		$db['user'] = DB_USER;
		$db['pass'] = DB_PASSWORD;
		$db['db'] = DB_NAME;
		$db['host'] = DB_HOST;
		return $db;
	}

}