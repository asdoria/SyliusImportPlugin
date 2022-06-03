<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer;


use App\Entity\Product\Product;
use App\Entity\Product\ProductTaxon;
use App\Entity\Taxonomy\Taxon;
use App\Entity\Taxonomy\TaxonTranslation;
use Asdoria\SyliusImportPlugin\Traits\DefaultLocaleTrait;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductTranslationInterface;

/**
 * Class TaxonSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class TaxonSerializer extends BaseSerializer
{
    use DefaultLocaleTrait;

    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [
            'translations' => $this->translationsCallback(),
            'productTaxons' => $this->productTaxonsCallback(),
            'parent' => $this->parentCallback(),
        ];
    }

    /**
     * @return \Closure
     */
    protected function parentCallback(): \Closure
    {
        $taxonRepository = $this->getEntityManager()->getRepository(Taxon::class);
        return function ($value, TaxonInterface $object, $key, $data) use ($taxonRepository): ?TaxonInterface {
            $parent = $taxonRepository->findOneByCode($value);
            if (!$parent instanceof TaxonInterface) return null;
            return $parent;
        };
    }

    /**
     * @return \Closure
     */
    protected function translationsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(TaxonTranslation::class);

        return function ($value, TaxonInterface $object, $key, $data) use ($serializer): Collection {
            if (!is_array($value)) $value = json_decode($value, true);
            $context      = $this->getSerializerContext($object, $key);
            $translations = $object->getTranslations();
            foreach ($value as $item) {
                if(empty($item['locale'])) $item['locale'] = $this->getDefaultLocale();
                /** @var ProductTranslationInterface $trans */
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

    /**
     * @return \Closure
     */
    protected function productTaxonsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ProductTaxon::class);
        $productRepository = $this->getEntityManager()->getRepository(Product::class);
        return function ($value, TaxonInterface $object, $key, $data) use ($serializer, $productRepository): void {
            if (!is_array($value)) $value = json_decode($value, true);

            foreach ($value as $code) {
                $product = $productRepository->findOneByCode($code);
                if (
                    !$product instanceof ProductInterface ||
                    $product->hasTaxon($object)
                ) continue;
                /** @var ProductTaxonInterface $productTaxon */
                $productTaxon = $serializer->deserialize([]);
                $productTaxon->setProduct($product);
                $productTaxon->setTaxon($object);
                $this->getEntityManager()->persist($productTaxon);
            }
        };
    }
}
