<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient\Tests\Feature;

use DateTimeImmutable;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Vlsv\SberPayQrApiClient\Model\ResponseRegistry;
use Vlsv\SberPayQrApiClient\Model\ResponseRegistryRegistryData;
use Vlsv\SberPayQrApiClient\Model\ResponseRegistryRegistryDataOrderParams;
use Vlsv\SberPayQrApiClient\Model\ResponseRegistryRegistryDataOrderParamsOrderOperationParams;
use Vlsv\SberPayQrApiClient\Model\ResponseRegistryRegistryDataOrderParamsOrderOperationParamsOrderOperationParam;
use Vlsv\SberPayQrApiClient\Model\ResponseRegistryRegistryDataOrderParamsOrderParam;
use Vlsv\SberPayQrApiClient\Tests\TestCaseBase;

class ResponseRegistryTest extends TestCaseBase
{
    public function test_deserializeSerialize(): void
    {
        $json = file_get_contents($this->samplesDirPath . '/ResponseRegistry.json');

        /** @var ResponseRegistry $responseRegistry */
        $responseRegistry = $this->serializer->deserialize(
            $json,
            ResponseRegistry::class,
            JsonEncoder::FORMAT
        );

        self::assertInstanceOf(ResponseRegistry::class, $responseRegistry);
        self::assertInstanceOf(DateTimeImmutable::class, $responseRegistry->getRqTm());
        self::assertInstanceOf(ResponseRegistryRegistryData::class, $responseRegistry->getRegistryData());
        self::assertInstanceOf(
            ResponseRegistryRegistryDataOrderParams::class,
            $responseRegistry->getRegistryData()->getOrderParams()
        );

        foreach (
            $responseRegistry->getRegistryData()->getOrderParams()->getOrderParam() as $dataOrderParamsOrderParam
        ) {
            self::assertInstanceOf(
                ResponseRegistryRegistryDataOrderParamsOrderParam::class,
                $dataOrderParamsOrderParam
            );
            self::assertInstanceOf(DateTimeImmutable::class, $dataOrderParamsOrderParam->getOrderCreateDate());
            self::assertInstanceOf(
                ResponseRegistryRegistryDataOrderParamsOrderOperationParams::class,
                $dataOrderParamsOrderParam->getOrderOperationParams()
            );

            foreach (
                $dataOrderParamsOrderParam->getOrderOperationParams()->getOrderOperationParam(
                ) as $dataOrderParamsOrderOperationParamsOrderOperationParam
            ) {
                self::assertInstanceOf(
                    ResponseRegistryRegistryDataOrderParamsOrderOperationParamsOrderOperationParam::class,
                    $dataOrderParamsOrderOperationParamsOrderOperationParam
                );
            }
        }
        self::assertJsonStringEqualsJsonString(
            $json,
            $this->serializer->serialize($responseRegistry, JsonEncoder::FORMAT)
        );
    }
}
