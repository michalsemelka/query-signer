<?php
/**
 *
 * @author Michal Semelka <m.semelka@gmail.com>
 * @copyright 2018 Michal Semelka
 */

namespace THSCZ\QuerySigner\Entities;


/**
 * Entity class that holds values that will be be signed or validated
 * Class QueryString
 * @package THSCZ\QuerySigner\Entities
 */

use THSCZ\QuerySigner\Exceptions\QS_Entity_EmptyQueryString;

/**
 * Class QueryString
 * @package THSCZ\QuerySigner\Entities
 */
class QueryString {

    /**
     * @var string
     */
    private $query;

    /**
     * QueryString constructor.
     * @param array $query
     */
    public function __construct(Array $query) {
        $this->query = $this->createQuery($query);
    }

    /**
     * @return string
     * @throws QS_Entity_EmptyQueryString
     */
    public function getQuery() {

        if (empty($this->query) && $this->query !== '') {
            throw new QS_Entity_EmptyQueryString('Values probably weren\'t set via create() or createValidate() methods!');
        }

        return $this->query;
    }

    /**
     * @param $query
     * @return string
     */
    public function createQuery($query) {
        return implode('.', $query);
    }


}