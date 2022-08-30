<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient\Tests\Feature;

use DateTimeImmutable;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Vlsv\SberPayQrApiClient\Model\OrderState;
use Vlsv\SberPayQrApiClient\Model\RequestCreation;
use Vlsv\SberPayQrApiClient\Model\ResponseStatus;
use Vlsv\SberPayQrApiClient\Tests\TestCaseBase;

class ResponseStatusTest extends TestCaseBase
{
    public function test_deserializeSerialize(): void
    {
        $json = file_get_contents($this->samplesDirPath . '/ResponseStatus.json');

        /** @var RequestCreation $responseStatus */
        $responseStatus = $this->serializer->deserialize(
            $json,
            ResponseStatus::class,
            JsonEncoder::FORMAT
        );

        self::assertInstanceOf(ResponseStatus::class, $responseStatus);
        self::assertInstanceOf(DateTimeImmutable::class, $responseStatus->getRqTm());
        self::assertInstanceOf(OrderState::class, $responseStatus->getOrderState());

        self::assertJsonStringEqualsJsonString(
            $json,
            $this->serializer->serialize($responseStatus, JsonEncoder::FORMAT)
        );
    }
}
