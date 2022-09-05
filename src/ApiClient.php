<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient;

use DateTimeImmutable;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Vlsv\SberPayQrApiClient\Exception\ApiException;
use Vlsv\SberPayQrApiClient\Model\RequestCreation;
use Vlsv\SberPayQrApiClient\Model\ResponseCreation;

class ApiClient
{
    public function __construct(
        protected ClientConfig $config,
        protected Serializer $serializer,
        protected Client $client = new Client(),
    ) {
    }

    /**
     * Создание заказа.
     * Клиент направляет запрос на формирование заказа в АС Сбербанка.
     * В ответ получает присвоенный Идентификатор заказа в АС Сбербанк (впоследствии используется в качестве ключа для
     * инициации других операций с заказом), ссылку для генерации QR кода.
     * Scope: order.create
     *
     * @see https://api.developer.sber.ru/product/PlatiQR/doc/v1/8024874223
     *
     * @throws Exception
     */
    public function creation(
        string $accessToken,
        RequestCreation $requestCreation,
        string $rqUID = '',
    ): ResponseCreation {
        $requestCreation = $requestCreation
            ->setRqUid($rqUID ?: $this->getRqUID())
            ->setRqTm(new DateTimeImmutable());

        $request = new Request('POST', $this->config->getHost() . '/creation');

        $requestOptions = [
            'headers' => [
                'Authorization' => $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'RqUID' => $requestCreation->getRqUid(),
            ],
            'body' => $this->serializer->serialize($requestCreation, JsonEncoder::FORMAT),
        ];

        $response = $this->makeRequest($request, $requestOptions);

        /** @var ResponseCreation $requestCreation */
        $requestCreation = $this->serializer->deserialize(
            data: $response->getBody()->getContents(),
            type: ResponseCreation::class,
            format: JsonEncoder::FORMAT
        );

        return $requestCreation;
    }

    /**
     * @throws Exception
     */
    public function getRqUID(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * @throws ApiException
     */
    private function makeRequest(Request $request, array $requestOptions): ResponseInterface
    {
        try {
            $response = $this->client->send($request, $requestOptions);
        } catch (GuzzleException $exception) {
            throw new ApiException(
                '[' . $exception->getCode() . '] ' . $exception->getMessage(),
                $exception->getCode(),
                $exception,
            );
        }

        if ($response->getStatusCode() !== 200) {
            throw new ApiException(
                '[' . $response->getStatusCode() . '] ' . 'Unknown response',
                $response->getStatusCode(),
            );
        }

        return $response;
    }
}
