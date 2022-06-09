<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Product\ProductAttribute;
use App\Entity\Product\ProductAttributeTranslation;
use App\Model\Product\ProductAttributeInterface;
use Asdoria\SyliusImportPlugin\Traits\DefaultLocaleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;

/**
 * Class ProductAttributeSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ProductAttributeValueSerializer extends BaseSerializer
{
    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [
            'attribute' => $this->attributeCallback(),
            'value' => $this->valueCallback(),
        ];
    }

    /**
     * @return \Closure
     */
    protected function attributeCallback(): \Closure
    {
        return function ($value, ProductAttributeValueInterface $object, $key, $data): AttributeInterface {
            return $this->getEntityManager()->getRepository(ProductAttribute::class)->findOneByCode($value);
        };
    }
    /**
     * @return \Closure
     */
    protected function valueCallback(): \Closure
    {
        return function ($value, ProductAttributeValueInterface $object, $key, $data) {
            $attr    = $object->getAttribute();
            $isJson  = $attr->getStorageType() === AttributeValueInterface::STORAGE_JSON;
            $choices = $attr->getConfiguration()['choices'] ?? [];
            if (in_array($value, array_keys($choices))) return [$value];
            if (in_array(sprintf('v-%s', $value), array_keys($choices))) return [sprintf('v-%s', $value)];
            if ($isJson) return null;

            return $value;
        };
    }
}
