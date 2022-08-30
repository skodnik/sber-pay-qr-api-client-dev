<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient\Tests\Unit;

use DateTimeImmutable;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Vlsv\SberPayQrApiClient\Exception\ApiException;
use Vlsv\SberPayQrApiClient\Model\RequestCreation;
use Vlsv\SberPayQrApiClient\Model\RequestCreationOrderParamsType;
use Vlsv\SberPayQrApiClient\Tests\TestCaseBase;

class ApiClientTest extends TestCaseBase
{
    public function test_getRquid()
    {
        self::assertIsString($this->apiClient->getRqUID());
        self::assertEquals(32, strlen($this->apiClient->getRqUID()));
    }

    /**
     * @throws Exception
     */
    public function test_creation()
    {
        $orderParamsType = (new RequestCreationOrderParamsType())
            ->setPositionName('test_position_name')
            ->setPositionCount(1)
            ->setPositionDescription('test_position_description')
            ->setPositionSum(190000);

        $requestCreation = (new RequestCreation())
            ->setMemberId('skazka')
            ->setOrderCreateDate(new DateTimeImmutable())
            ->setOrderParamsType([$orderParamsType]);

        try {
            $this->apiClient->creation(
                accessToken: 'order.create',
                requestCreation: $requestCreation
            );

            self::fail();
        } catch (ApiException $exception) {
            self::assertEquals(403, $exception->getCode());
        } catch (GuzzleException $exception) {
            self::fail();
        }
    }
}
