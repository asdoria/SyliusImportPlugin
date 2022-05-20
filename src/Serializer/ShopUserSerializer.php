<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Customer\Customer;
use App\Entity\User\ShopUser;
use Asdoria\SyliusImportPlugin\Serializer\Callback\DateTimeCallbackTrait;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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

        return function ($value, $object, $key, $data) use ($serializer) : ?CustomerInterface {
            if(!is_array($value)) $value = json_decode($value, true);

            if(empty($value)) return null;

            $context = $this->getSerializerContext($object, $key);

            /** @var CustomerInterface $customer */
            $customer = $serializer->deserialize($value, null, $context);
            $customer->setUser($object);

            return $customer;
        };
    }
}
