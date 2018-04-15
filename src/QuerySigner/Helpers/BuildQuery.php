<?php
/**
 *
 * @author Michal Semelka <m.semelka@gmail.com>
 * @copyright 2018 Michal Semelka
 */

namespace THSCZ\QuerySigner\Helpers;

use THSCZ\QuerySigner\Entities\QueryString;
use THSCZ\QuerySigner\Exceptions\BaseException;
use THSCZ\QuerySigner\Exceptions\QS_Helper_EmptyQueryException;
use THSCZ\QuerySigner\Exceptions\QS_Helper_WrongIncludesException;
use THSCZ\QuerySigner\Exceptions\QS_Helper_EmptyHashException;
use THSCZ\QuerySigner\Options\Switches;

/**
 * Helper class maps query string from $_SERVER['QUERY_STRING'] to QueryString entity
 * Output of build method is used in \Abstracts\BaseQuerySigner\createValidate() and validate() methods
 * Class BuildQuery
 * @package THSCZ\QuerySigner\Helpers
 */
class BuildQuery implements Switches {

    /**
     * @var bool
     */
    private $hasAtLeastOneQueryParam = FALSE;

    /**
     * Based on $_SERVER['QUERY_STRING'] in $query create query string in format value1.value2...valuex.hash
     * Some query params can be omitted with $option Switches::SIGNER_HELPER_BUILD_SPECIFIC_QUERY and
     * list of names of params in $includes
     * Example:
     *  $_SERVER['QUERY_STRING'] = $query = id=123&page=2&hash=xxxx
     *  $options = ['id']
     *  $this->query = 123.xxxx
     * @param $query
     * @param string $option
     * @param array $includes
     * @return QueryString $queryImploded
     */
	public function build($query, $option = Switches::SIGNER_HELPER_BUILD_ALL_QUERY, $includes = []) {

		try {
			$queryArray = $this->parseQuery($query);
			if ($option == Switches::SIGNER_HELPER_BUILD_SPECIFIC_QUERY) {
				$queryArray = $this->filterQuery($queryArray, $includes);
			}
			$queryImploded = $this->implodeQuery($queryArray);
		} catch (BaseException $e) {
			$e->getDevMessage();
			$queryImploded = new QueryString(['']);
		}

		return $queryImploded;

	}


	/**
     * Create from query string array
	 * @param $query
	 * @return array $result
	 * @throws QS_Helper_EmptyQueryException
	 */
	private function parseQuery($query) {

		if (empty($query)) {
			throw new QS_Helper_EmptyQueryException('Empty query string');
		}

		parse_str($query, $result);

		$this->savedQueryArray = $result;

		return $result;

	}

	/**
     * Clear from array params that user not defined in $includes
	 * @param array $queryArray
	 * @param array $includes
	 * @throws QS_Helper_EmptyQueryException | QS_Helper_WrongIncludesException
	 */
	private function filterQuery(Array $queryArray, Array $includes) {

		if (empty($queryArray)) {
			throw new QS_Helper_EmptyQueryException('Empty query array');
		}

        $includesHasWrongtypes = FALSE;

		foreach ($includes as $include) {
		    if (is_int($include) || is_string($include)) {
                continue;
            } else {
                $includesHasWrongtypes = TRUE;
                break;
            }
        }

		if (empty($includes) || $includesHasWrongtypes) {
			throw new QS_Helper_WrongIncludesException('$includes array is probable empty or contains wrong datatypes');
		}

		$result = array_intersect_key($queryArray, array_flip($includes));

		if (empty($result)) {
			throw new QS_Helper_WrongIncludesException('Specified $_GET parameters are not present in query string');
		}

		return $result;

	}

	/**
     * Create QueryString entity from query array
	 * @param array $queryArray
     * @return QueryString
	 * @throws QS_Helper_EmptyQueryException
	 */
	private function implodeQuery(Array $queryArray) {

		if (empty($queryArray)) {
			throw new QS_Helper_EmptyQueryException('Empty query string');
		}

		$this->hasAtLeastOneQueryParam = TRUE;

		return new QueryString($queryArray);

	}

	/**
     * Cut hash part from string
	 * @param $queryImploded
	 * @param $hash
	 * @return mixed
	 * @throws QS_Helper_EmptyHashException
	 * @throws QS_Helper_EmptyQueryException
	 */
	public function unsetHashFromQuery($queryImploded, $hash) {

		if (!$this->hasAtLeastOneQueryParam) {
			throw new QS_Helper_EmptyQueryException('Unable to unset hash from query. Empty query string');
		}

		if (empty($hash)) {
			throw new QS_Helper_EmptyHashException('Unable to unset hash from query. Empty hash string');
		}

		return str_replace('.'.$hash, '', $queryImploded);
	}
}