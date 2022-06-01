<?php
declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Serializer\Callback;

use App\Entity\Product\ProductAttribute;
use App\Entity\Product\ProductAttributeValue;
use App\Model\Product\ProductInterface;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Asdoria\SyliusImportPlugin\Traits\DefaultLocaleTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Attribute\Model\AttributeValue;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 *
 */
trait ProductAttributeValuesCallbackTrait
{
    /**
     * @return \Closure
     */
    protected function productAttributeValuesCallback(): \Closure
    {
        /** @var RepositoryInterface $attributeRepository */
        /** @var SerializerInterface $serializer */
        $attributeRepository = $this->getEntityManager()->getRepository(ProductAttribute::class);
        $serializer = $this->getSerializerByClass(ProductAttributeValue::class);
        return function ($value, ProductInterface $object, $key, $data) use ($attributeRepository, $serializer): Collection {
            $context    = $this->getSerializerContext($object, $key);
            $attributes = !$object->getAttributes()->isEmpty() ? $object->getAttributes() : new ArrayCollection();
            foreach ($data as $i => $v) {
                $attr = $attributeRepository->findOneByCode($i);
                if (!$attr instanceof ProductAttributeInterface) continue;
                if ($object->getAttributeByCodeAndLocale($attr->getCode(), $this->getDefaultLocale())) continue;

                /** @var ProductAttributeValueInterface $attrValue */
                $attrValue = $serializer->deserialize([
                    'attribute' => $i,
                    'value' => $v,
                    'locale' => $this->getDefaultLocale(),
                ], null, $context);
                $attrValue->setProduct($object);
                $attrValue->setAttribute($attr);
                $attributes->add($attrValue);
            }
            return $attributes;
        };
    }

    abstract public function getEntityManager(): ?EntityManagerInterface;
    abstract protected function getSerializerByClass(string $className): ?SerializerInterface;
    abstract public function getDefaultLocale(): ?string;
}
