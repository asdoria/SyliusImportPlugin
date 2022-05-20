<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Resolver;

use Asdoria\SyliusImportPlugin\Message\ImportNotificationInterface;
use Asdoria\SyliusImportPlugin\Registry\ServiceRegistry;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
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
    protected ServiceRegistry $serializerServiceRegistry;
    /**
     * @param ServiceRegistry $serializerServiceRegistry
     */
    public function __construct(ServiceRegistry $serializerServiceRegistry) {
        $this->serializerServiceRegistry = $serializerServiceRegistry;
    }
    /**
     * @param ResourceInterface           $resource
     * @param ImportNotificationInterface $message
     */
    public function handle(ResourceInterface $resource, ImportNotificationInterface $message): void {

        $serializer = $this->serializerServiceRegistry->get($message->getEntityClass());

        if(!$serializer instanceof SerializerInterface) {
            return;
        }
        $context  = [
            AbstractNormalizer::OBJECT_TO_POPULATE => $resource
        ];

        $serializer->deserialize($message->getData(), $message->getEntityClass(), $context);
    }

}
