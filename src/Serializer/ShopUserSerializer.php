<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Customer\Customer;
use App\Entity\User\ShopUser;
use Asdoria\SyliusImportPlugin\Serializer\Callback\DateTimeCallbackTrait;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

/**
 * Class ShopUserSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ShopUserSerializer extends BaseSerializer
{
    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [
            'customer'  => $this->customerCallback()
        ];
    }

    /**
     * @return \Closure
     */
    protected function customerCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(Customer::class);

        return function ($value, $object, $key, $context, $data) use ($serializer) : ?CustomerInterface {
            if(!is_array($value)) $value = json_decode($value, true);

            if(empty($value)) return null;

            /** @var CustomerInterface $customer */
            $customer = $serializer->deserialize($value);
            $customer->setUser($object);

            return $shopUser;
        };
    }
}
