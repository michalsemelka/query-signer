<?php
/**
 *
 * @author Michal Semelka <m.semelka@gmail.com>
 * @copyright 2018 Michal Semelka
 */

namespace THSCZ\QuerySigner\Exceptions;


/**
 * Class BaseException
 * @package THSCZ\QuerySigner\Exceptions
 */
class BaseException extends \Exception {

    /**
     * Print warning notice to page
     */
    public function getDevMessage() {

		echo '<br />' . 'WARNING! ' . $this->getMessage() . ' exception at line ' . $this->getLine() . ' in file ' . $this->getFile() . '<br />';

	}

}