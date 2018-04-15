<?php
/**
 *
 * @author Michal Semelka <m.semelka@gmail.com>
 * @copyright 2018 Michal Semelka
 */

namespace THSCZ\QuerySigner\Abstracts;


use THSCZ\QuerySigner\Entities\QueryString;
use THSCZ\QuerySigner\Options\Base;
use THSCZ\QuerySigner\Helpers\BuildQuery;
use THSCZ\QuerySigner\Options\Switches;


/**
 * Base class of QuerySigner.
 * Every QuerySigner class must implements methods create(), sing(), createValidate() and validate()
 *  create(...params)           ->  setter of values that should be signed with hash
 *  sign(...params)             –>  sign values specifed in create() and returns hash
 *  createValidate(...params)   ->  setter of values that should validate
 *  validate(...params)         ->  validate obtained hash with hash created from values setted by createValidate()
 * Class BaseQuerySigner
 * @package THSCZ\QuerySigner\Abstracts
 */
abstract class BaseQuerySigner implements Base, Switches {

	/**
	 * Holds timestamp for expiration of signed query strings
	 * @var integer
	 */
	private $timestamp;


    /**
     * Store string of values for signing
     * @var QueryString
     */
    protected $querySign;

	/**
	 * Store string of values for validation
	 * @var QueryString
	 */
	protected $queryValidate;

	/**
     * Instance of \Helpers\BuildQuery
	 * @var BuildQuery
	 */
	private $helper;

	/**
	 * BaseQuerySigner constructor.
	 */
	public function __construct() {
//		if (session_status() == PHP_SESSION_NONE) {
//			@session_start();
//		}

		$this->timestamp = $this->getTimeStamp();
		$this->helper = new BuildQuery();
	}


	/**
	 * Create expiration date for signed query params
	 * @return int
	 */
	protected function getTimestamp() {

		$now = strtotime('now');
		$expiration = strtotime('now + 24 hours');

		if (empty($_SESSION[Base::SIGNER_SESSION_NAME]['timestamp'])) {
			return $_SESSION[Base::SIGNER_SESSION_NAME]['timestamp'] = $expiration;
		} elseif ($now == $_SESSION[Base::SIGNER_SESSION_NAME]['timestamp']) {
			return $_SESSION[Base::SIGNER_SESSION_NAME]['timestamp'] = $expiration;
		} else {
			return $_SESSION[Base::SIGNER_SESSION_NAME]['timestamp'];
		}

	}

	/**
	 * Returns helper that builds query string
	 * @return BuildQuery
	 */
	protected function getQueryHelper() {
		return $this->helper;
	}

	/**
	 * Unset all session connected with QuerySigner
	 */
	public function flushSigns() {
		unset($_SESSION[Base::SIGNER_SESSION_NAME]);
	}

    /**
     * @param array $params
     * @return $this
     */
    abstract function create(Array $params);

    /**
     * @param $query
     * @param string $option
     * @param array $includes
     * @return $this
     */
    abstract function createValidate($query, $option, $includes);

	/**
	 * @param $salt
	 * @return string
	 */
	abstract function sign($salt);

	/**
	 * @param $hash
	 * @param $salt
	 * @return boolean
	 */
	abstract function validate($hash, $salt);

}