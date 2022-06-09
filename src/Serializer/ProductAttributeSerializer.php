<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Product\ProductAttributeTranslation;
use Asdoria\SyliusImportPlugin\Traits\DefaultLocaleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Sylius\Component\Attribute\AttributeType\CheckboxAttributeType;
use Sylius\Component\Attribute\AttributeType\IntegerAttributeType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
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
            'configuration' => $this->configurationCallback(),
            'translations'  => $this->translationsCallback(),
            'storageType'   => $this->storageTypeCallback(),
        ];
    }

    /**
     * @return \Closure
     */
    protected function storageTypeCallback(): \Closure
    {
        return function ($value, $object, $key, $data): string {
            switch ($value) {
                case SelectAttributeType::TYPE:
                    return AttributeValueInterface::STORAGE_JSON;
                case IntegerAttributeType::TYPE:
                    return AttributeValueInterface::STORAGE_INTEGER;
                case CheckboxAttributeType::TYPE:
                    return AttributeValueInterface::STORAGE_BOOLEAN;
                default:
                    return AttributeValueInterface::STORAGE_TEXT;
            }
        };
    }

    /**
     * @return \Closure
     */
    protected function configurationCallback(): \Closure
    {
        return function ($value, ProductAttributeInterface $object, $key, $data): array {
            if ($object->getStorageType() !== AttributeValueInterface::STORAGE_JSON) return [];
            if (!is_array($value)) $value = json_decode($value, true);

            if (empty($value)) return [];

            $configuration = array_reduce($value, function ($carry, $item) {
                if (empty(array_filter($item))) return $carry;
                $key                             = $item['key'] ?? $this->getUniqueKey();
                $value                           = $item['value'] ?? $item;
                $locale                          = $item['locale'] ?? $this->getDefaultLocale();
                if (is_int($key)) $key = sprintf('v-%s', $key);
                $carry['choices'][$key][$locale] = $value;
                return $carry;
            }, []);

            $configuration["multiple"] = false;
            $configuration["min"] = null;
            $configuration["max"] = null;

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
            if (!is_array($value)) $value = json_decode($value, true);
            $context      = $this->getSerializerContext($object, $key);
            $translations = $object->getTranslations();
            foreach ($value as $item) {
                /** @var ProductAttributeTranslationInterface $trans */
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

    private function getUniqueKey(): string
    {
        return Uuid::uuid1()->toString();
    }
}
