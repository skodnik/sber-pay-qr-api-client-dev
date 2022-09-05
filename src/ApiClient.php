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
use Vlsv\SberPayQrApiClient\Model\RequestInterface;
use Vlsv\SberPayQrApiClient\Model\RequestStatus;
use Vlsv\SberPayQrApiClient\Model\ResponseCreation;
use Vlsv\SberPayQrApiClient\Model\ResponseStatus;

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
        $requestCreation
            ->setRqUid($rqUID ?: $this->getRqUID())
            ->setRqTm(new DateTimeImmutable());

        $response = $this->makeRequest(
            accessToken: $accessToken,
            resourcePath: '/creation',
            requestObject: $requestCreation,
        );

        /** @var ResponseCreation $requestCreation */
        $requestCreation = $this->serializer->deserialize(
            data: $response->getBody()->getContents(),
            type: ResponseCreation::class,
            format: JsonEncoder::FORMAT
        );

        return $requestCreation;
    }

    /**
     * Запрос статуса заказа.
     * Клиент запрашивает информацию по ранее созданному заказу по Уникальному идентификатору запроса (ранее
     * сформированному в АС Сбербанка) и по номеру заказа в CRM Клиента.
     * В ответ получает данные по заказу с детализацией по финансовым операциям.
     * @throws ApiException
     */
    public function status(
        string $accessToken,
        RequestStatus $requestStatus,
        string $rqUID = '',
    ): ResponseStatus {
        $requestStatus
            ->setRqUid($rqUID ?: $this->getRqUID())
            ->setRqTm(new DateTimeImmutable());

        $response = $this->makeRequest(
            accessToken: $accessToken,
            resourcePath: '/status',
            requestObject: $requestStatus,
        );

        /** @var ResponseStatus $responseStatus */
        $responseStatus = $this->serializer->deserialize(
            data: $response->getBody()->getContents(),
            type: ResponseStatus::class,
            format: JsonEncoder::FORMAT
        );

        return $responseStatus;
    }

    /**
     * @throws ApiException
     */
    private function makeRequest(
        string $accessToken,
        string $resourcePath,
        RequestInterface $requestObject,
    ): ResponseInterface {
        $request = new Request('POST', $this->config->getHost() . $resourcePath);
        $requestOptions = [
            'headers' => [
                'Authorization' => $accessToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'RqUID' => $requestObject->getRqUid(),
            ],
            'body' => $this->serializer->serialize(
                data: $requestObject,
                format: JsonEncoder::FORMAT,
            ),
        ];

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

    /**
     * @throws Exception
     */
    public function getRqUID(): string
    {
        return bin2hex(random_bytes(16));
    }
}
