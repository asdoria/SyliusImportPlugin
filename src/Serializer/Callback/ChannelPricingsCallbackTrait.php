<?php
declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Serializer\Callback;

use App\Entity\Channel\Channel;
use App\Entity\Channel\ChannelPricing;
use App\Entity\Product\ProductVariant;
use App\Model\Product\ProductInterface;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 *
 */
trait ChannelPricingsCallbackTrait
{
    /**
     * @return \Closure
     */
    protected function channelPricingsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ChannelPricing::class);
        $channels = $this->getEntityManager()->getRepository(Channel::class)->findAll();
        $defaultChannel = (new ArrayCollection($channels))->first();

        return function ($value, ProductVariantInterface $object, $key, $data) use ($serializer, $defaultChannel): Collection {
            if (!is_array($value)) $value = json_decode($value, true);
            $context  = $this->getSerializerContext($object, $key);
            $channelPricings = $object->getChannelPricings()->isEmpty() ?
            $object->getChannelPricings() :new ArrayCollection();
            /** @var ChannelPricingInterface $channelPricing */
            $channelPricing = $serializer->deserialize([
                'price' => $value,
                'channelCode' => $defaultChannel->getCode()
            ], null, $context);

            $isNew = $channelPricings->filter(fn($item) => $item->getChannelCode() === $channelPricing->getChannelCode())->isEmpty();
            if ($isNew) {
                $this->getEntityManager()->persist($channelPricing);
                $channelPricing->setProductVariant($object);
                $channelPricings->add($channelPricing);
            }

            return $channelPricings;
        };
    }

    abstract public function getEntityManager(): ?EntityManagerInterface;
    abstract protected function getSerializerByClass(string $className): ?SerializerInterface;
}
