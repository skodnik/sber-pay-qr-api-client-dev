<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient\Tests\Feature;

use DateTimeImmutable;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Vlsv\SberPayQrApiClient\Model\RequestCreation;
use Vlsv\SberPayQrApiClient\Model\ResponseCreation;
use Vlsv\SberPayQrApiClient\Tests\TestCaseBase;

class ResponseCreationTest extends TestCaseBase
{
    public function samplesDataProvider(): array
    {
        return [
            'sber' => ['ResponseOrderCreateQrRs.json'],
            'sbp' => ['ResponseOrderCreateQrRsSBP.json'],
        ];
}
    /** @dataProvider samplesDataProvider */
    public function test_deserializeSerialize(string $sampleFilename): void
    {
        $json = file_get_contents($this->samplesDirPath . '/' . $sampleFilename);

        /** @var RequestCreation $requestCreation */
        $requestCreation = $this->serializer->deserialize(
            $json,
            ResponseCreation::class,
            JsonEncoder::FORMAT
        );

        self::assertInstanceOf(ResponseCreation::class, $requestCreation);
        self::assertInstanceOf(DateTimeImmutable::class, $requestCreation->getRqTm());

        self::assertJsonStringEqualsJsonString(
            $json,
            $this->serializer->serialize($requestCreation, JsonEncoder::FORMAT)
        );
    }
}
