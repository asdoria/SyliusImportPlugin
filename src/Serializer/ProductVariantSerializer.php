<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Product\ProductVariantTranslation;
use App\Model\Product\ProductAttributeInterface;
use Asdoria\SyliusImportPlugin\Serializer\Callback\ChannelPricingsCallbackTrait;
use Asdoria\SyliusImportPlugin\Traits\DefaultLocaleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;

/**
 * Class ProductVariantSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ProductVariantSerializer extends BaseSerializer
{
    use DefaultLocaleTrait;
    use ChannelPricingsCallbackTrait;
    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [
            'translations'  => $this->translationsCallback(),
            'channelPricings' => $this->channelPricingsCallback(),
        ];
    }

    /**
     * @return \Closure
     */
    protected function translationsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ProductVariantTranslation::class);

        return function ($value, ProductVariantInterface $object, $key, $data) use ($serializer): Collection {
            if (empty($value)) $value = [[ 'locale' => $this->getDefaultLocale() ]];
            if (!is_array($value)) $value = json_decode($value, true);
            $context      = $this->getSerializerContext($object, $key);
            $translations = $object->getTranslations();
            foreach ($value as $item) {
                /** @var ProductAttributeTranslationInterface $trans */
                $trans = $serializer->deserialize($item, null, $context);
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
