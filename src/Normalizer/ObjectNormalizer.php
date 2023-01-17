<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Normalizer;

use Asdoria\SyliusImportPlugin\Converter\Model\NameConverterInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer as BaseObjectNormalizer;

/**
 * Class ObjectNormalizer
 * @package App\Import\Normalizer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ObjectNormalizer extends BaseObjectNormalizer
{
    public const IMPORT_CALLBACKS = 'import_callbacks';

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * {@inheritdoc}
     */
    protected function setAttributeValue($object, $attribute, $value, $format = null, array $context = [])
    {
        try {
            $callback = $this->defaultContext[self::IMPORT_CALLBACKS][$attribute] ?? null;
            if ($callback) {
                $value = call_user_func($callback, $value, $object, $attribute, $this->data);
            }

            $this->propertyAccessor->setValue($object, $attribute, $value);
        } catch (NoSuchPropertyException $exception) {
            // Properties not found are ignored
        }
    }

    /**
     * @return array
     */
    public function getExtraAttributes(): array
    {
        /** @var NameConverterInterface $nameConverter */
        $nameConverter = $this->nameConverter;
        if (!$nameConverter instanceof NameConverterInterface) return [];

        return $nameConverter->getExtraAttributes();
    }

    /**
     * @return array
     */
    public function getResetKeys(): array
    {
        /** @var NameConverterInterface $nameConverter */
        $nameConverter = $this->nameConverter;
        if (!$nameConverter instanceof NameConverterInterface) return [];
        return $nameConverter->getResets();
    }

    /**
     * @return array
     */
    public function getIgnoreKeys(): array
    {
        /** @var NameConverterInterface $nameConverter */
        $nameConverter = $this->nameConverter;
        if (!$nameConverter instanceof NameConverterInterface) return [];
        return $nameConverter->getIgnores();
    }
    /**
     * @return array
     */
    public function getPriorityKeys(): array
    {
        /** @var NameConverterInterface $nameConverter */
        $nameConverter = $this->nameConverter;
        if (!$nameConverter instanceof NameConverterInterface) return [];
        return $nameConverter->getPriorities();
    }

    /**
     * Normalizes the given data to an array. It's particularly useful during
     * the denormalization process.
     *
     * @param object|array $data
     *
     * @return array
     */
    protected function prepareForDenormalization(mixed $data): array
    {
        $result = (array) $data;

        foreach ($this->getIgnoreKeys() as $key => $val) {
            unset($result[$key]);
        }

        foreach ($this->getResetKeys() as $key => $val) {
            if (isset($result[$key]) && is_array($result[$key])) {
                $result = array_merge($result, $result[$key] ?? []);
                unset($result[$key]);
            }
        }

        foreach ($this->getExtraAttributes() as $key => $val) {
            if (isset($result[$key])) {
                continue;
            }
            $result[$key] =  $result[$val] ?? $val;
        }

        $this->data = array_merge($this->getPriorityKeys(), $result);

        return $this->data;
    }
}
