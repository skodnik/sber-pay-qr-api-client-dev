<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient\Tests\Integration;

use DateTimeImmutable;
use Exception;
use Vlsv\SberPayQrApiClient\Entity\ApiEndpoint;
use Vlsv\SberPayQrApiClient\Entity\RequestCreation;
use Vlsv\SberPayQrApiClient\Entity\RequestCreationOrderParamsType;
use Vlsv\SberPayQrApiClient\Entity\RequestStatus;
use Vlsv\SberPayQrApiClient\Exception\ApiException;
use Vlsv\SberPayQrApiClient\Tests\TestCaseBase;

class ApiClientTest extends TestCaseBase
{
    /**
     * @throws Exception
     */
    public function test_creationCatchApiException()
    {
        $orderParamsType = (new RequestCreationOrderParamsType())
            ->setPositionName('test_position_name')
            ->setPositionCount(1)
            ->setPositionDescription('test_position_description')
            ->setPositionSum(190000);

        $requestCreation = (new RequestCreation())
            ->setMemberId('some_member_id')
            ->setOrderCreateDate(new DateTimeImmutable())
            ->setOrderParamsType([$orderParamsType]);

        try {
            $this->apiClient->makeRequest(
                accessToken: 'order.create',
                apiEndpoint: ApiEndpoint::CREATION,
                requestObject: $requestCreation,
            );

            self::fail();
        } catch (ApiException $exception) {
            self::assertEquals(403, $exception->getCode());
        }
    }

    /**
     * @throws Exception
     */
    public function test_statusCatchApiException()
    {
        $requestStatus = (new RequestStatus())
            ->setOrderId('5cf8cb8b-37e3-42e9-8f69-306fa72e106f')
            ->setTid('83457dda-332c-46f4-b928-8d4bd9ee3afe');

        try {
            $this->apiClient->makeRequest(
                accessToken: 'order.status',
                apiEndpoint: ApiEndpoint::STATUS,
                requestObject: $requestStatus,
            );

            self::fail();
        } catch (ApiException $exception) {
            self::assertEquals(403, $exception->getCode());
        }
    }
}
