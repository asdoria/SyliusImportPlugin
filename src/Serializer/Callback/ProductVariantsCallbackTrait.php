<?php
declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Serializer\Callback;

use App\Entity\Product\ProductVariant;
use App\Model\Product\ProductInterface;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 *
 */
trait ProductVariantsCallbackTrait
{
    /**
     * @return \Closure
     */
    protected function productVariantsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ProductVariant::class);

        return function ($value, ProductInterface $object, $key, $data) use ($serializer): Collection {
            if (!is_array($value)) $value = json_decode($value, true);
            $context  = $this->getSerializerContext($object, $key);
            $variants = $object->hasVariants() ? $object->getVariants() :new ArrayCollection();
            foreach ($value as $item) {
                /** @var ProductVariantInterface $variant */
                $variant = $serializer->deserialize($item, null, $context);
                $isNew = $variants->filter(fn($item) => $item->getCode() === $variant->getCode())->isEmpty();
                if ($isNew) {
                    $this->getEntityManager()->persist($variant);
                    $variant->setProduct($object);
                    $variants->add($variant);
                }
            }
            return $variants;
        };
    }

    abstract public function getEntityManager(): ?EntityManagerInterface;
    abstract protected function getSerializerByClass(string $className): ?SerializerInterface;
}
