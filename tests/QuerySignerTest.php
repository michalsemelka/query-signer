<?php

namespace THSCZ\QuerySigner\Test;


use THSCZ\QuerySigner\GETQuerySigner;

// TODO
class QuerySignerTest extends \PHPUnit_Framework_TestCase {


    /**
     * @param array $queryData
     *
     * @dataProvider providerTestBaseFunction
     */
    public function testBaseFunction($queryData) {

        $signer = new GETQuerySigner();

        $hash = $signer->create(array_values($queryData))->sign();

        $queryData['hash'] = $hash;

        $result = $signer->createValidate(http_build_query($queryData))->validate($hash);

        $this->assertTrue($result);
    }

    public function testSpecificParams() {

        $queryData = Array(
            'id' => 134,
            'page' => 2
        );

        $signer = new GETQuerySigner();

        $hash = $signer->create([$queryData['id']])->sign();

        $queryData['hash'] = $hash;

        $result = $signer->createValidate(http_build_query($queryData),
            $signer::SIGNER_HELPER_BUILD_SPECIFIC_QUERY,
            ['id'])->validate($hash);

        $this->assertTrue($result);
    }

    public function testMissingParams() {
        $queryData = Array(
            'id' => 134,
            'page' => 2
        );
        $queryData = null;

        $signer = new GETQuerySigner();

        $hash = $signer->create([$queryData['id']])->sign();

//        $hash = null;

        $includes = Array('id');
//        $includes = Array(null);

        $queryData['hash'] = $hash;
//        $queryData = Array(null);

        $buildQuery = http_build_query($queryData);
//        $buildQuery = null;

        $result = $signer->createValidate($buildQuery,
            $signer::SIGNER_HELPER_BUILD_SPECIFIC_QUERY,
            $includes)->validate($hash);

        $this->assertFalse($result);
    }

    public function providerTestBaseFunction()
    {
        return array(
            array(
                Array(
                    'id' => 134,
                    'page' => 2
                ),
                Array(
                    'logon' => ''
                ),
                Array(
                    'param' => '',
                    'page' => 234,
                    'length' => 24,
                    'source' => 'campaign_42',
                ),
                Array(''),
            )
        );
    }
}