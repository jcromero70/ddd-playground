<?php

namespace Tests\Leos\UI\RestBundle\Controller\Wallet;

use Lakion\ApiTestCase\JsonApiTestCase;
use Tests\Leos\UI\RestBundle\Controller\Security\SecurityTrait;

/**
 * Class RollbackControllerTest
 * 
 * @package Leos\UI\RestBundle\Controller\Wallet
 */
class RollbackControllerTest extends JsonApiTestCase
{
    use SecurityTrait;

    private $databaseLoaded = false;

    public function setUp()
    {
        $_SERVER['IS_DOCTRINE_ORM_SUPPORTED'] = true;

        if (!$this->client) {

            $this->setUpClient();
        }

        if (!$this->databaseLoaded) {

            $this->setUpDatabase();
            $this->databaseLoaded = true;
        }

        $this->expectedResponsesPath = $this->client->getContainer()->getParameter('kernel.root_dir') . "/../tests/UI/Responses/Wallet";
        $this->dataFixturesPath = $this->client->getContainer()->getParameter('kernel.root_dir') . "/../tests/UI/Behat/Context/Fixtures";
    }

    /**
     * @group functional
     */
    public function testRollbackDepositAction()
    {
        $this->loginClient('jorge', 'iyoque123');

        $this->client->request('POST', '/api/v1/wallet.json');

        $response = $this->client->getResponse();
        self::assertEquals(201, $response->getStatusCode());

        $this->client->request('POST', $response->headers->get('location') . '/deposit.json', [
            'real' => 100,
            'provider' => 'paypal'
        ]);

        $response = $this->client->getResponse();

        $this->client->request('POST', '/api/v1/rollback/deposit.json', [
            'deposit' => json_decode($response->getContent(), true)['id']
        ]);

        $response = $this->client->getResponse();
        self::assertEquals(202, $response->getStatusCode());
    }

    /**
     * @group functional
     */
    public function testRollbackDepositNotFoundAction()
    {
        $this->loginClient('jorge', 'iyoque123');

        $this->client->request('POST', '/api/v1/rollback/deposit.json', [
            'deposit' => '0cb00000-646e-11e6-a5a2-0000ac1b0000'
        ]);

        $response = $this->client->getResponse();
        self::assertEquals(404, $response->getStatusCode());
    }
    /**
     * @group functional
     */
    public function testRollbackWithdrawalNotFoundAction()
    {
        $this->loginClient('jorge', 'iyoque123');

        $this->client->request('POST', '/api/v1/rollback/withdrawal.json', [
            'withdrawal' => '0cb00000-646e-11e6-a5a2-0000ac1b0000'
        ]);

        $response = $this->client->getResponse();
        self::assertEquals(404, $response->getStatusCode());
    }

    /**
     * @group functional
     */
    public function testRollbackWithdrawalAction()
    {
        $this->loginClient('jorge', 'iyoque123');

        $this->client->request('POST', '/api/v1/wallet.json', [
            'currency' => 'EUR'
        ]);

        $response = $this->client->getResponse();
        self::assertEquals(201, $response->getStatusCode());

        $this->client->request('POST', $response->headers->get('location') . '/deposit.json', [
            'real' => 50,
            'provider' => 'paypal'
        ]);

        $this->client->request('POST', $response->headers->get('location') . '/withdrawal.json', [
            'real' => 5,
            'provider' => 'paypal'
        ]);

        $response = $this->client->getResponse();

        self::assertResponse($response, "withdrawal", 202);

        $this->client->request('POST', '/api/v1/rollback/withdrawal.json', [
            'withdrawal' => json_decode($response->getContent(), true)['id']
        ]);

        $response = $this->client->getResponse();
        self::assertEquals(202, $response->getStatusCode());
    }
    /**
     * @group functional
     */
    public function testRollbackDepositGivenAWithdrawalAction()
    {
        $this->loginClient('jorge', 'iyoque123');

        $this->client->request('POST', '/api/v1/wallet.json', [
            'currency' => 'EUR'
        ]);

        $response = $this->client->getResponse();
        self::assertEquals(201, $response->getStatusCode());

        $this->client->request('POST', $response->headers->get('location') . '/deposit.json', [
            'real' => 50,
            'provider' => 'paypal'
        ]);

        $this->client->request('POST', $response->headers->get('location') . '/withdrawal.json', [
            'real' => 5,
            'provider' => 'paypal'
        ]);

        $response = $this->client->getResponse();

        self::assertResponse($response, "withdrawal", 202);

        $this->client->request('POST', '/api/v1/rollback/deposit.json', [
            'withdrawal' => json_decode($response->getContent(), true)['id']
        ]);

        $response = $this->client->getResponse();
        self::assertEquals(400, $response->getStatusCode());
    }
}
