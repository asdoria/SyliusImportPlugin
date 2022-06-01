<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Resolver;

use Asdoria\SyliusImportPlugin\Message\ImportNotificationInterface;
use Asdoria\SyliusImportPlugin\Registry\ServiceRegistry;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Asdoria\SyliusImportPlugin\Traits\ServiceRegistryTrait;
use Psr\Log\LoggerAwareTrait;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class HandlerResolver
 * @package Asdoria\SyliusImportPlugin\Resolver
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class HandlerResolver implements  HandlerResolverInterface
{
    use LoggerAwareTrait;
    use ServiceRegistryTrait;

    /**
     * @param ResourceInterface           $resource
     * @param ImportNotificationInterface $message
     *
     * @return ResourceInterface|null
     */
    public function handle(ImportNotificationInterface $message, ?ResourceInterface $resource = null): ?ResourceInterface {

        $serializer = $this->serviceRegistry->get($message->getEntityClass());

        if (!$serializer instanceof SerializerInterface)  return null;

        $context = [];

        $serializer->setConfiguration($message->getConfiguration());

        if ($message->getConfiguration()->isUpdater() && $resource instanceof ResourceInterface)
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $resource;

        $entity = $serializer->deserialize($message->getData(), $message->getEntityClass(), $context);

        return $entity;
    }
}
