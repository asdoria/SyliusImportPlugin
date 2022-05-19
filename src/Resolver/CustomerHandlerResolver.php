<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Resolver;

use App\Import\Normalizer\ObjectNormalizer;
use App\Traits\DispatcherTrait;
use Asdoria\SyliusImportPlugin\Message\ImportNotificationInterface;
use Asdoria\SyliusImportPlugin\Registry\ServiceRegistry;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Class CustomerHandlerResolver
 * @package Asdoria\SyliusImportPlugin\Resolver
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class CustomerHandlerResolver implements  HandlerResolverInterface
{
    use DispatcherTrait;
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
        $context  = [ObjectNormalizer::LOGGER => $this->logger];
        $serializer->deserialize($message->getData(), $message->getEntityClass(), $context);
    }

}
