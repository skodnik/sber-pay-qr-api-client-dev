<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient\Tests\Feature;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Vlsv\SberPayQrApiClient\Model\RequestCreation;
use Vlsv\SberPayQrApiClient\Model\RequestStatus;
use Vlsv\SberPayQrApiClient\Tests\TestCaseBase;

class RequestStatusTest extends TestCaseBase
{
    public function test_deserializeSerialize(): void
    {
        $json = file_get_contents($this->samplesDirPath . '/RequestStatus.json');

        /** @var RequestCreation $requestCreation */
        $requestCreation = $this->serializer->deserialize(
            $json,
            RequestStatus::class,
            JsonEncoder::FORMAT
        );

        self::assertInstanceOf(RequestStatus::class, $requestCreation);

        self::assertJsonStringEqualsJsonString(
            $json,
            $this->serializer->serialize($requestCreation, JsonEncoder::FORMAT)
        );
    }
}
