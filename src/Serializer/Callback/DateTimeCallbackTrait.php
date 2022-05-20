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
            $datetime = !empty($value) ? new \DateTime($value) : null;
            $isValid = checkdate(
                intval($datetime->format('m')),
                intval($datetime->format('d')),
                intval($datetime->format('Y'))
            );

            return $isValid ? $datetime: null;
        };
    }
}
