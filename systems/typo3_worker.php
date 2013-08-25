<?php
/**
 * typo3Worker Class
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
 * Class typo3Worker
 * @author Bastian Bringenberg <mail@bastian-bringenberg.de>
 * @link https://github.com/bbnetz/FastBackup
 *
 * @info Needs to be lowercase, even if TYPO3 is written uppercase only
 */
class typo3_worker extends basic_worker{

	/**
	 * function run
	 * doing the main work of backuping a TYPO3
	 * 
	 * @return void
	 */
	public function run() {
		$db = $this->fetchDatabase($this->instancePath);
		$this->saveMySQL($db['user'], $db['pass'], $db['db'], $db['host']);
		$this->saveFiles($this->instancePath, 'TYPO3');
		$this->writeFinalTar();
	}

	/**
	 * function fetchDatabase
	 * fetches all required database informations
	 * separates between 4.* and 6.* 
	 *
	 * @return array $db informations for a database array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
	 */
	protected function fetchDatabase() {
		$file = $this->instancePath.DIRECTORY_SEPARATOR.'typo3conf'.DIRECTORY_SEPARATOR.'localconf.php';
		if(!file_exists($file)) {
			// >= 6.0 possible
			$db = $this->fetchDatabase_TYPO3_6($this->instancePath);
		} else {
			// <= 6.0
			$db = $this->fetchDatabase_TYPO3_4($file);
		}
		return $db;
	}

	/**
	 * function fetchDatabase_TYPO3_4
	 * fetching informations for a TYPO3 4.* instance
	 *
	 * @param string $file the localconf.php from a 4.* isntance
	 * @return $db informations for a database array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
	 */
	protected function fetchDatabase_TYPO3_4($file) {
		$db = array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
		require_once($file);
		$db['user'] = $typo_db_username;
		$db['pass'] = $typo_db_password;
		$db['db']  = $typo_db;
		$db['host'] = $typo_db_host;
		return $db;
	}

	/**
	 * function fetchDatabase_TYPO3_6
	 * fetch informations for a TYPO3 6.* instance
	 * 
	 * @param string $file the Path to a typo3conf path
	 * @return $db informations for a database array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
	 */
	protected function fetchDatabase_TYPO3_6($file) {
		$db = array('user' => '', 'pass' => '', 'db' => '', 'host' => '');
		$additional = $file.DIRECTORY_SEPARATOR.'typo3conf'.DIRECTORY_SEPARATOR.'AdditionalConfiguration.php';
		$local = $file.DIRECTORY_SEPARATOR.'typo3conf'.DIRECTORY_SEPARATOR.'LocalConfiguration.php';
		if(file_exists($additional)) {
			require_once($additional);
			$db['user'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['username'];
			$db['pass'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['password'];
			$db['db']  = $GLOBALS['TYPO3_CONF_VARS']['DB']['database'];
			$db['host'] = $GLOBALS['TYPO3_CONF_VARS']['DB']['host'];
		} elseif(file_exists($local)) {
			$helper = $this->fetchLocalConfiguration($local);
			$db['user'] = $helper['DB']['username'];
			$db['pass'] = $helper['DB']['password'];
			$db['db']  = $helper['DB']['database'];
			$db['host'] = $helper['DB']['host'];
		} else {
			die('No TYPO3 Installation found.');
		}		
		return $db;
	}

	/**
	 * function fetchLocalConfiguration
	 * fetches the local configuration from a 6.* instance
	 *
	 * @return array configuration array from TYPO3 6.*
	 */
	protected function fetchLocalConfiguration($local) {
		return include($local);
	}


}