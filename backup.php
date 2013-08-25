#!/usr/bin/php
<?php
/**
 * FastBackup Class
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
 * Class FastBackup
 * @author Bastian Bringenberg <mail@bastian-bringenberg.de>
 * @link https://github.com/bbnetz/FastBackup
 *
 */
class FastBackup {

	/**
	 * function run
	 * Builds Worker and runs it.
	 *
	 * @param array $argv the cli params
	 * @return void
	 */
	public function run($argv) {
		if(count($argv) < 4)
			die('Script needs at least 4 parameters.');
		$className = strtolower($argv[1]).'_worker';
		$phpFile = __DIR__.DIRECTORY_SEPARATOR.'systems'.DIRECTORY_SEPARATOR.$className.'.php';
		$instancePath = realpath($argv[2]);
		$backupFilename = $argv[3];
		if(!file_exists($phpFile))
			die('Backup Service not available! Please check for missspelling.');
		if(file_exists($backupFilename))
			die('Backup file already exists. Aborted!');
		if(!file_exists($instancePath))
			die('Instances Path does not exist. Aborted!');
		require_once(__DIR__.DIRECTORY_SEPARATOR.'systems'.DIRECTORY_SEPARATOR.'basic_worker.php');
		require_once($phpFile);
		$worker = new $className($instancePath, $backupFilename);
		$worker->run();
	}

}

$run = new FastBackup();
$run->run($argv);

?>