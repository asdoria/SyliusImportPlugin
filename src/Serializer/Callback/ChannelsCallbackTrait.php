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
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 *
 */
trait ChannelsCallbackTrait
{
    /**
     * @return \Closure
     */
    protected function channelsCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $channels = $this->getEntityManager()->getRepository(Channel::class)->findAll();

        return function ($value,ChannelsAwareInterface $object, $key, $data) use ($channels): Collection {
            return new ArrayCollection($channels);
        };
    }

    abstract public function getEntityManager(): ?EntityManagerInterface;
}
