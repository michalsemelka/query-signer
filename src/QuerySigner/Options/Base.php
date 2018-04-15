<?php
/**
 *
 * @author Michal Semelka <m.semelka@gmail.com>
 * @copyright 2018 Michal Semelka
 */

namespace THSCZ\QuerySigner\Options;


/**
 * Interface Base
 * @package THSCZ\QuerySigner\Options
 */
interface Base {

    /**
     * Internal salt used by default
     */
    const SIGNER_SALT = 'prettyflyforsalting';

    /**
     * Name of session to where is all corresponded data saved
     */
    const SIGNER_SESSION_NAME = 'querysigner';

}