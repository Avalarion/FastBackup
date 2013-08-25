<?php
/**
 * wbbWorker Class
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
 * Class wbbWorker
 * @author Bastian Bringenberg <mail@bastian-bringenberg.de>
 * @link https://github.com/bbnetz/FastBackup
 *
 */
class wbb_worker extends basic_worker{

	/**
	 * function run
	 * doing all the work to backup a WBB instance
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
	 * includes wcf's config.inc.php to get all database informations
	 *
	 * @return array $db the required DB informations array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
	 */
	protected function fetchDatabase() {
		$db = array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
		$file = $this->instancePath.DIRECTORY_SEPARATOR.'wcf'.DIRECTORY_SEPARATOR.'config.inc.php';
		if(!file_exists($file)) {
			die('No WBB found');
		} else {
			require_once($file);
			$db['user'] = $dbUser;
			$db['pass'] = $dbPassword;
			$db['db'] = $dbName;
			$db['host'] = $dbHost;
		}
		return $db;
	}

}