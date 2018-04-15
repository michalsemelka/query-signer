<?php
/**
 *
 * @author Michal Semelka <m.semelka@gmail.com>
 * @copyright 2018 Michal Semelka
 */

namespace THSCZ\QuerySigner\Options;


/**
 * Interface Switches
 * @package THSCZ\QuerySigner\Options
 */
interface Switches {

    /**
     * Option to use internal defined salt
     */
    const SIGNER_USE_INTERNAL_SALT = 'SIGNER_USE_INTERNAL_SALT';

    /**
     * Option to build all query params for control hash
     */
    const SIGNER_HELPER_BUILD_ALL_QUERY = 'SIGNER_HELPER_BUILD_ALL_QUERY';

    /**
     * Option to select query params for control hash
     */
    const SIGNER_HELPER_BUILD_SPECIFIC_QUERY = 'SIGNER_HELPER_BUILD_SPECIFIC_QUERY';

}