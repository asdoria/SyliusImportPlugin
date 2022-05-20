<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\User\ShopUser;
use Asdoria\SyliusImportPlugin\Serializer\Callback\DateTimeCallbackTrait;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

/**
 * Class CustomerSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class CustomerSerializer extends BaseSerializer
{
    use DateTimeCallbackTrait;

    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [
            'createdAt' => $this->dateTimeCallback(),
            'updatedAt' => $this->dateTimeCallback(),
            'birthday'  => $this->dateTimeCallback(),
        ];
    }
}
