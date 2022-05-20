<?php
declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Serializer\Callback;

/**
 * 
 */
trait DateTimeCallbackTrait
{
    /**
     * @return \Closure
     */
    protected function dateTimeCallback(): \Closure
    {
        return function ($value, $object, $key): ?\DateTime {
            return !empty($value) ? new \DateTime($value) : null;
        };
    }
}
