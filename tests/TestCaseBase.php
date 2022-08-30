<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Vlsv\SberPayQrApiClient\ApiClient;
use Vlsv\SberPayQrApiClient\ClientConfig;

class TestCaseBase extends TestCase
{
    protected Serializer $serializer;
    protected string $samplesDirPath = __DIR__ . '/../var/samples';
    protected ApiClient $apiClient;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $metadataAwareNameConverter = new MetadataAwareNameConverter(
            $classMetadataFactory,
            new CamelCaseToSnakeCaseNameConverter()
        );

        $normalizers = [
            new BackedEnumNormalizer(),
            new ArrayDenormalizer(),
            new DateTimeNormalizer([
                DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:s\Z',
            ]),
            new PropertyNormalizer(
                classMetadataFactory: $classMetadataFactory,
                nameConverter: $metadataAwareNameConverter,
                propertyTypeExtractor: new PropertyInfoExtractor(
                    typeExtractors: [
                        new PhpDocExtractor(),
                        new ReflectionExtractor(),
                    ]
                ),
            ),

        ];
        $encoders = [new JsonEncoder()];

        $this->serializer = new Serializer($normalizers, $encoders);

        $config = new ClientConfig();
        $this->apiClient = new ApiClient(
            config: $config,
            serializer: $this->serializer
        );
    }
}
