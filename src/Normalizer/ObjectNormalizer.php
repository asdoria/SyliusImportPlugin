<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Normalizer;

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
    public const CUSTOM_CALLBACKS = 'import_callbacks';

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
            $callback = $this->defaultContext[self::CUSTOM_CALLBACKS][$attribute] ?? null;
            if ($callback) {
                $value = call_user_func($callback, $value, $object, $attribute, $this->data);
            }

            $this->propertyAccessor->setValue($object, $attribute, $value);
        } catch (NoSuchPropertyException $exception) {
            // Properties not found are ignored
        }
    }

    /**
     * Normalizes the given data to an array. It's particularly useful during
     * the denormalization process.
     *
     * @param object|array $data
     *
     * @return array
     */
    protected function prepareForDenormalization($data)
    {
        $result = parent::prepareForDenormalization($data);

        $this->data = $result;

        return $this->data;
    }
}
