<?php

declare(strict_types=1);

namespace Vlsv\SberPayQrApiClient\Model;

enum RegistryType: string
{
    case QUANTITY = 'QUANTITY';
    case REGISTRY = 'REGISTRY';
}
