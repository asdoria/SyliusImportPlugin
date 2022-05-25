<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Addressing\Address;
use App\Entity\Customer\Customer;
use App\Entity\Customer\CustomerGroup;
use Asdoria\SyliusImportPlugin\Serializer\Callback\DateTimeCallbackTrait;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;

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
            'addresses'  => $this->addressesCallback(),
            'group'  => $this->groupCallback(),
        ];
    }


    /**
     * @return \Closure
     */
    protected function addressesCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(Address::class);

        return function ($value, CustomerInterface $object, $key, $data) use ($serializer): Collection {
            $context   = $this->getSerializerContext($object, $key);
            $addresses = new ArrayCollection();
            foreach ($value as $item) {
                /** @var AddressInterface $address */
                $address = $serializer->deserialize($item, null, $context);
                $this->getEntityManager()->persist($address);
                $address->setCustomer($object);
                $addresses->add($address);
            }
            return $addresses;
        };
    }


    /**
     * @return \Closure
     */
    protected function groupCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(CustomerGroup::class);

        return function ($value, $object, $key, $data) use ($serializer) : ?CustomerGroupInterface {
            $customerGroup = $this->getEntityManager()
                ->getRepository(CustomerGroup::class)->findOneByName($value);

            if ($customerGroup instanceof CustomerGroupInterface) return $customerGroup;

            $context = $this->getSerializerContext($object, $key);

            $value = ['name' => $value, 'code' => $this->camelCaseToSnakeCase($value)];
            /** @var CustomerGroupInterface $customerGroup */
            $customerGroup = $serializer->deserialize($value, null, $context);
            $this->getEntityManager()->persist($customerGroup);
            return $customerGroup;
        };
    }
}
