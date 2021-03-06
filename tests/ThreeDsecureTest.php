<?php

namespace SafeCharge\Tests;

use SafeCharge\Api\Service\Payments\ThreeDsecure;

class ThreeDsecureTest extends \PHPUnit_Framework_TestCase
{
    private $_service;

    public function __construct()
    {
        $this->_service = new ThreeDsecure(TestCaseHelper::getClient());
    }

    /**
     * @return mixed
     * @throws \Exception
     * @throws \SafeCharge\Api\Exception\ConnectionException
     * @throws \SafeCharge\Api\Exception\ResponseException
     * @throws \SafeCharge\Api\Exception\ValidationException
     */
    public function testDynamic3d()
    {
        TestCaseHelper::setSessionToken(null);
        $params             = $this->getExampleData();
        $params['cardData'] = SimpleData::getCarData('5150906102780140');

        $response = $this->_service->dynamic3D($params);
        $this->assertContains('orderId', $response);
        $this->assertContains('paRequest', $response);
        return $response['orderId'];
    }

    /**
     * @depends testDynamic3d
     * @param $orderId
     * @throws \Exception
     * @throws \SafeCharge\Api\Exception\ConnectionException
     * @throws \SafeCharge\Api\Exception\ResponseException
     * @throws \SafeCharge\Api\Exception\ValidationException
     */
    public function testPayment3d($orderId)
    {
        $params = $this->getExampleData();
        unset($params['isDynamic3D']);
        $params['orderId']           = $orderId;
        $params['isPartialApproval'] = "0";
        $params['paResponse']        = "";
        $params['transactionType']   = "Sale";
        $params['cardData']          = SimpleData::getCarData();
        $response                    = $this->_service->payment3D($params);
        $this->assertContains('orderId', $response);
        $this->assertEquals('SUCCESS', $response['status']);
    }

    /**
     * @return array
     * @throws \Exception
     * @throws \SafeCharge\Api\Exception\ConnectionException
     * @throws \SafeCharge\Api\Exception\ResponseException
     * @throws \SafeCharge\Api\Exception\ValidationException
     */
    public function getExampleData()
    {
        $params = [
            'sessionToken'      => TestCaseHelper::getSessionToken(),
            // "orderId"           => "",
            'userTokenId'       => TestCaseHelper::getUserTokenId(),
            'clientUniqueId'    => '12345',
            'clientRequestId'   => '1484759782197',
            'isDynamic3D'       => '0',
            'currency'          => SimpleData::getCurrency(),
            'amount'            => "5000",
            'amountDetails'     => SimpleData::getAmountDetails(),
            'items'             => [
                [
                    "id"       => "1",
                    "name"     => "name",
                    "price"    => "5000",
                    "quantity" => "1"
                ]
            ],
            'deviceDetails'     => SimpleData::getDeviceDetails(),
            'userDetails'       => SimpleData::getUserDetails(),
            'shippingAddress'   => SimpleData::getShippingAddress(),
            'billingAddress'    => SimpleData::getBillingAddress(),
            'dynamicDescriptor' => SimpleData::getDynamicDescriptor(),
            'merchantDetails'   => SimpleData::getMerchantDetails(),
            'addendums'         => SimpleData::getAddEndUms(),
            'cardData'          => [],
//        'userPaymentOption' => SimpleData::getUserPaymentOption(),
            'urlDetails'        => SimpleData::getUrlDetails(true)
        ];

        return $params;
    }
}
