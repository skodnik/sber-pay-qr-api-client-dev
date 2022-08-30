<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient;

class ClientConfig
{
    protected string $dateTimeFormat = 'Y-m-d\TH:i:s\Z';
    protected string $host = 'https://api.sberbank.ru:8443/prod/qr/order/v3';

    public function getDateTimeFormat(): string
    {
        return $this->dateTimeFormat;
    }

    public function setDateTimeFormat(string $dateTimeFormat): ClientConfig
    {
        $this->dateTimeFormat = $dateTimeFormat;

        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): ClientConfig
    {
        $this->host = $host;

        return $this;
    }
}
