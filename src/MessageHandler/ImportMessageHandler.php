<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\MessageHandler;

use Asdoria\Bundle\ImportBundle\Message\ImportNotificationInterface;
use Asdoria\Bundle\ImportBundle\Resolver\FormConfiguration\FormConfigurationHandleResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
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
        // ... do some work - like sending an SMS message!
        $importHandle = $this->importRegistry->get($message->getEntityClass());
        if(!$importHandle instanceof ImportHandleInterface) {
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

        $this->entityManager->transactional(function(EntityManagerInterface $manager) use ($message, $importHandle) {
            $importHandle->handle($entity, $message);
        });
    }
}
