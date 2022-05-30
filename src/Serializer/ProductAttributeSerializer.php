<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Product\ProductAttributeTranslation;
use App\Model\Product\ProductAttributeInterface;
use Asdoria\SyliusImportPlugin\Traits\DefaultLocaleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;

/**
 * Class ProductAttributeSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ProductAttributeSerializer extends BaseSerializer
{

    use DefaultLocaleTrait;
    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [
            'configuration'  => $this->configurationCallback(),
            'translations'  => $this->translationsCallback(),
            'storageType'  => $this->storageTypeCallback(),
        ];
    }

    /**
     * @return \Closure
     */
    protected function storageTypeCallback(): \Closure
    {
        return function ($value, $object, $key, $data): string {
            return AttributeValueInterface::STORAGE_JSON;
        };
    }
    /**
     * @return \Closure
     */
    protected function configurationCallback(): \Closure
    {
        return function ($value, $object, $key, $data) : array{
            if(!is_array($value)) $value = json_decode($value, true);

            if(empty($value)) return [];

            $configuration = array_reduce($value, function ($carry, $item) {
                $carry['choices'][$this->getUniqueKey()][$this->getDefaultLocale()] = $item;
                return $carry;
            }, []);

            return $configuration;
        };
    }


    /**
     * @return \Closure
     */
    protected function translationsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ProductAttributeTranslation::class);

        return function ($value, ProductAttributeInterface $object, $key, $data) use ($serializer): Collection {
            if(!is_array($value)) $value = json_decode($value, true);
            $context   = $this->getSerializerContext($object, $key);
            $translations = new ArrayCollection();
            foreach ($value as $item) {
                /** @var ProductAttributeTranslationInterface $trans */
                $trans = $serializer->deserialize($item, null, $context);
                $this->getEntityManager()->persist($trans);
                $trans->setTranslatable($object);
                $translations->add($trans);
            }
            return $translations;
        };
    }

    private function getUniqueKey(): string
    {
        return Uuid::uuid1()->toString();
    }
}
