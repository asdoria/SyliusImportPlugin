<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\MessageHandler;


use Asdoria\SyliusImportPlugin\Message\ImportNotificationInterface;
use Asdoria\SyliusImportPlugin\Registry\Model\ServiceRegistryInterface;
use Asdoria\SyliusImportPlugin\Resolver\HandlerResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareTrait;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class ImportMessageHandler
 * @package Asdoria\SyliusImportPlugin\MessageHandler
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ImportMessageHandler implements MessageHandlerInterface
{
    protected ServiceRegistryInterface $formConfigurationHandleRegistry;
    protected EntityManagerInterface $entityManager;
    use LoggerAwareTrait;

    public function __construct(
        EntityManagerInterface $entityManager,
        ServiceRegistryInterface $importRegistry
    )
    {
        $this->entityManager = $entityManager;
        $this->importRegistry = $importRegistry;
    }

    public function __invoke(ImportNotificationInterface $message)
    {

        $this->logger->critical('start ImportMessageHandler');
        // ... do some work - like sending an SMS message!
        $importHandle = $this->importRegistry->get($message->getEntityClass());
        if(!$importHandle instanceof HandlerResolverInterface) {
            return;
        }

        $repository = $this->entityManager->getRepository($message->getEntityClass());
        if(!$repository instanceof RepositoryInterface) {
            return;
        }

        $entityId = $message->getDataByKey('id');
        $entity   = !empty($entityId) ? $repository->find($entityId) : null;
        if(!$entity instanceof ResourceInterface) {
            $class = $message->getEntityClass();
            $entity = new $class;
            $this->entityManager->persist($entity);
        }

        $importHandle->handle($entity, $message);
    }
}
