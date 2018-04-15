<?php
/**
 *
 * @author Michal Semelka <m.semelka@gmail.com>
 * @copyright 2018 Michal Semelka
 */

namespace THSCZ\QuerySigner;


use THSCZ\QuerySigner\Entities\QueryString;
use THSCZ\QuerySigner\Exceptions\BaseException;
use THSCZ\QuerySigner\Abstracts\BaseQuerySigner;
use THSCZ\QuerySigner\Options\Base;
use THSCZ\QuerySigner\Options\Switches;

/**
 * Create sign and validate with it params obtained from $_GET.
 *      Examples:
 *          on sign page user want to sign values that will be transfered via $_GET
 *          /order?id=1234
 *          creates sign via $this->create(['1234'])->sign();
 *          and add returned hash to link like /order?id=1234&hash=xxxxx
 *          (note: name of param "hash" can be any)
 *
 *          on validation page calls $this–>createValidate($_SERVER['QUERY_STRING'])->validate();
 *          value of hash is automatically cut from query string. It returns true/false.
 *
 *          if user wants to specify which params wants to sign, he can do it this way:
 *          lets say that he wants to sign only "id" value
 *          /order?id=1234&otherparam=42
 *          creates sign via $this->create(['1234'])->sign();
 *          on validation page calls
 *              $this–>createValidate($_SERVER['QUERY_STRING'],
 *                   Switches::SIGNER_HELPER_BUILD_SPECIFIC_QUERY
 *                   ['id'])
 *                       ->validate();
 * Class GETQuerySigner
 * @package THSCZ\QuerySigner
 */
class GETQuerySigner extends BaseQuerySigner implements Base, Switches {

    /**
     * Create QueryString entity from user defined values
     * @param array $params
     * @return $this
     */
    function create(Array $params) {
        $this->querySign = new QueryString($params);
        return $this;
    }

    /**
     * Create and return control hash based on array of values in $this->querySign and $salt
	 * @param string $salt
	 * @return string
	 */
	function sign($salt = self::SIGNER_SALT) {

	    try {
	        $queryString = $this->querySign->getQuery();
        } catch(BaseException $e) {
	        $e->getDevMessage();
            $queryString = rand();
        }

		return sha1($queryString.'.'.$this->getTimestamp().'.'.$salt);
	}

    /**
     * Based on $_SERVER['QUERY_STRING'] in $query creates QueryString entity that has format value1.value2...valuex.hash
     * Some query params can be omitted with $option Switches::SIGNER_HELPER_BUILD_SPECIFIC_QUERY and
     * list of names of params in $includes
     * Example:
     *  $_SERVER['QUERY_STRING'] = $query = id=123&page=2&hash=xxxx
     *  $options = ['id']
     *  $this->query = 123.xxxx
     * @param string $query
     * @param string $option
     * @param array $includes
     * @return $this
     */
    function createValidate($query, $option = Switches::SIGNER_HELPER_BUILD_ALL_QUERY, $includes = []) {
        $this->queryValidate = $this->getQueryHelper()->build($query, $option, $includes);

        return $this;
    }

	/**
     * Validate obtained $hash from $_GET with hash created from QueryString entinty created by createValidate() method
     * @param $hash
	 * @param string $salt
	 * @return bool
	 */
	function validate($hash, $salt = self::SIGNER_SALT) {

		try {
            $queryString = $this->queryValidate->getQuery();
			$paramsWithoutHash = $this->getQueryHelper()->unsetHashFromQuery($queryString, $hash);
		} catch (BaseException $e) {
			$e->getDevMessage();
			$paramsWithoutHash = rand();
		}

		$controlHash = sha1($paramsWithoutHash.'.'.$this->getTimestamp().'.'.$salt);

		if (empty($hash) || !($this->queryValidate instanceof QueryString)) {
			return FALSE;
		}

		return hash_equals($hash, $controlHash);

	}
}