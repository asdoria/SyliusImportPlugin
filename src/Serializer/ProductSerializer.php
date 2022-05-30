<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Product\ProductAttribute;
use App\Entity\Product\ProductAttributeValue;
use App\Entity\Product\ProductImage;
use App\Entity\Product\ProductTranslation;
use App\Entity\Product\ProductVariant;
use App\Model\Product\ProductInterface;
use Asdoria\SyliusImportPlugin\Traits\DefaultLocaleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductTranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Class ProductSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ProductSerializer extends BaseSerializer
{

    use DefaultLocaleTrait;
    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [
            'translations'  => $this->translationsCallback(),
            'attributes'    => $this->attributesCallback(),
            'images'        => $this->imagesCallback(),
            'variants'        => $this->variantsCallback(),
        ];
    }

    /**
     * @return \Closure
     */
    protected function variantsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ProductVariant::class);

        return function ($value, ProductInterface $object, $key, $data) use ($serializer): Collection {
            if(!is_array($value)) $value = json_decode($value, true);
            $context   = $this->getSerializerContext($object, $key);
            $variants = new ArrayCollection();
            foreach ($value as $item) {
                /** @var ProductVariantInterface $variant */
                $variant = $serializer->deserialize($item, null, $context);
                $this->getEntityManager()->persist($variant);
                $variant->setProduct($object);
                $variants->add($variant);
            }
            return $variants;
        };
    }


    /**
     * @return \Closure
     */
    protected function imagesCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ProductImage::class);

        return function ($value, ProductInterface $object, $key, $data) use ($serializer): Collection {
            if(!is_array($value)) $value = json_decode($value, true);
            $context   = $this->getSerializerContext($object, $key);
            $images = new ArrayCollection();
            foreach ($value as $item) {
                /** @var ProductImageInterface $img */
                $img = $serializer->deserialize($item, null, $context);
                $this->getEntityManager()->persist($img);
                $img->setOwner($object);
                $images->add($img);
            }
            return $images;
        };
    }

    /**
     * @return \Closure
     */
    protected function translationsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ProductTranslation::class);

        return function ($value, ProductInterface $object, $key, $data) use ($serializer): Collection {
            if(!is_array($value)) $value = json_decode($value, true);
            $context   = $this->getSerializerContext($object, $key);
            $translations = new ArrayCollection();
            foreach ($value as $item) {
                /** @var ProductTranslationInterface $trans */
                $trans = $serializer->deserialize($item, null, $context);
                $this->getEntityManager()->persist($trans);
                $trans->setTranslatable($object);
                $translations->add($trans);
            }
            return $translations;
        };
    }
    /**
     * @return \Closure
     */
    protected function attributesCallback(): \Closure
    {
        /** @var RepositoryInterface $attributeRepository */
        /** @var SerializerInterface $serializer */
        $attributeRepository = $this->getEntityManager()->getRepository(ProductAttribute::class);
        $serializer = $this->getSerializerByClass(ProductAttributeValue::class);
        return function ($value, ProductInterface $object, $key, $data) use ($attributeRepository): Collection {
            $context    = $this->getSerializerContext($object, $key);
            $attributes = new ArrayCollection();
            foreach ($data as $i => $v) {
                $attr = $attributeRepository->findOneByCode($i);
                if(!$attr instanceof ProductAttributeInterface) continue;
                $toto = '';
                $attrValue = $serializer->deserialize([
                    'code' => $i,
                    'value' => $value,
                    'locale' => $this->getDefaultLocale(),
                    'product' => $object
                ], null, $context);
                $attributes->add($attrValue);
            }
            return $attributes;
        };
    }
}
