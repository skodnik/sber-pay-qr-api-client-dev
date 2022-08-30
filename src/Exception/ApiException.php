<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient\Exception;

use Exception;
use stdClass;

class ApiException extends Exception
{
    protected stdClass|string|null $responseObject = null;

    public function __construct(
        string $message = '',
        int $code = 0,
    ) {
        parent::__construct($message, $code);
    }

    public function setResponseObject(string|stdClass|null $obj): void
    {
        $this->responseObject = $obj;
    }

    public function getResponseObject(): string|stdClass|null
    {
        return $this->responseObject;
    }
}
