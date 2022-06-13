<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Product\ProductTranslation;
use App\Model\Product\ProductInterface;
use Asdoria\SyliusImportPlugin\Serializer\Callback\ChannelsCallbackTrait;
use Asdoria\SyliusImportPlugin\Serializer\Callback\ProductAttributeValuesCallbackTrait;
use Asdoria\SyliusImportPlugin\Serializer\Callback\ProductImagesCallbackTrait;
use Asdoria\SyliusImportPlugin\Serializer\Callback\ProductVariantsCallbackTrait;
use Asdoria\SyliusImportPlugin\Traits\DefaultLocaleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Product\Model\ProductTranslationInterface;

/**
 * Class ProductSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ProductSerializer extends BaseSerializer
{

    use DefaultLocaleTrait;
    use ProductImagesCallbackTrait;
    use ProductVariantsCallbackTrait;
    use ProductAttributeValuesCallbackTrait;
    use ChannelsCallbackTrait;

    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [
            'translations' => $this->translationsCallback(),
            'attributes'   => $this->productAttributeValuesCallback(),
            'images'       => $this->productImagesCallback(),
            'variants'     => $this->productVariantsCallback(),
            'channels'     => $this->channelsCallback(),
        ];
    }

    /**
     * @return \Closure
     */
    protected function translationsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ProductTranslation::class);

        return function ($value, ProductInterface $object, $key, $data) use ($serializer): Collection {
            if (!is_array($value)) $value = json_decode($value, true);
            $context      = $this->getSerializerContext($object, $key);
            $translations = $object->getTranslations();
            foreach ($value as $item) {
                if(empty($item['locale'])) $item['locale'] = $this->getDefaultLocale();
                /** @var ProductTranslationInterface $trans */
                $trans   = $serializer->deserialize($item, null, $context);
                $isNew = $translations->filter(fn($translation) => $translation->getLocale() === $trans->getLocale())->isEmpty();
                if ($isNew) {
                    $this->getEntityManager()->persist($trans);
                    $trans->setTranslatable($object);
                    $translations->add($trans);
                }
            }
            return $translations;
        };
    }
}
