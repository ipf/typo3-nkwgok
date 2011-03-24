<?php
/**
 * Typo3 Scheduler task to automatically run our three scheduler tasks
 * in the correct order:
 * 1. Load LKL data from Opac
 * 2. Convert History CSV Data to XML
 * 3. Import all the XML to the Typo3 Database
 *
 * 2011 Sven-S. Porst <porst@sub.uni-goettingen.de>
 */


require_once(t3lib_extMgm::extPath('nkwgok') . 'lib/class.tx_nkwgok_loadfromopac.php');
require_once(t3lib_extMgm::extPath('nkwgok') . 'lib/class.tx_nkwgok_loadhistory.php');
require_once(t3lib_extMgm::extPath('nkwgok') . 'lib/class.tx_nkwgok_loadxml.php');

/**
 * Class tx_nkwgok_importAll provides task procedures
 *
 * @author		Sven-S. Porst <porst@sub.uni-goettingen.de>
 * @package		TYPO3
 * @subpackage	tx_nkwgok
 */
class tx_nkwgok_importAll extends tx_scheduler_Task {

	/**
	 * Function executed from the Scheduler.
	 * @return	boolean	TRUE if success, otherwise FALSE
	 */
	public function execute() {
		$loadFromOpacTask = new tx_nkwgok_loadFromOpac;
		$success = $loadFromOpacTask->execute();
		if (!$success) {
			t3lib_div::devLog('importALL Scheduler Task: could not load Opac data. Stopping.' , 'nkwgok', 3);
		}
		else {
			$loadHistoryTask = new tx_nkwgok_loadHistory;
			$success = $loadHistoryTask->execute();
			if (!$success) {
				t3lib_div::devLog('importAll Scheduler Task: could not convert History CSV. Stopping.' , 'nkwgok', 3);
			}
			else {
				$loadxmlTask = new tx_nkwgok_loadxml;
				$success = $loadxmlTask->execute();
				if (!$success) {
					t3lib_div::devLog('importAll Scheduler Task: could not import XML to Typo3 database.' , 'nkwgok', 3);
				}
			}
		}

		return $success;
	}

}



if (defined('TYPO3_MODE')
		&& $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nkwgok/lib/class.tx_nkwgok_importall.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nkwgok/lib/class.tx_nkwgok_importall.php']);
}
?>