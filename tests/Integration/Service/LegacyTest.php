<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Base\Tests\Integration\Service;

use oxField;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Service\Legacy as LegacyService;
use OxidEsales\TestingLibrary\UnitTestCase;

class LegacyTest extends UnitTestCase
{
    /** @var LegacyService */
    private $legacyService;

    public function setUp(): void
    {
        parent::setUp();
        $containerFactory = new TestContainerFactory();
        $container        = $containerFactory->create();
        $container->compile();
        $this->legacyService = $container->get(LegacyService::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->cleanUpTable('oxuser');
    }

    public function testValidLogin(): void
    {
        $works = false;
        $this->legacyService->login('admin', 'admin');
        $works = true;
        $this->assertTrue($works);
    }

    public function testInvalidLogin(): void
    {
        $this->expectException(InvalidLogin::class);
        $this->legacyService->login(
            'admin',
            'wrongpassword'
        );
    }

    private function createUser($dbusergroup): void
    {
        // Needed to get the permissions for setting the user group
        $this->legacyService->login('admin', 'admin');

        $oUser = oxNew(User::class);
        $oUser->setId('_testUser');
        $oUser->oxuser__oxusername = new oxField('testuser', oxField::T_RAW);
        $oUser->oxuser__oxrights   = new oxField($dbusergroup, oxField::T_RAW);
        $oUser->createUser();
    }
}
