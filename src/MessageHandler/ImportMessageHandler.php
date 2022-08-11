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

    public function __invoke(ImportNotificationInterface $message): void
    {
        $this->logger->info('start ImportMessageHandler');
        // ... do some work - like sending an SMS message!
        $importHandle = $this->importRegistry->get($message->getEntityClass());
        if (!$importHandle instanceof HandlerResolverInterface) {
            return;
        }

        $repository = $this->entityManager->getRepository($message->getEntityClass());
        if(!$repository instanceof RepositoryInterface) {
            return;
        }

        $identifier      = $message->getConfiguration()->getIdentifier();
        $identifiers     = explode(',', $identifier);
        $criteria = array_reduce(
            $identifiers,
            function($carry, $v) use ($message) {
                $carry[$v] = $message->getDataByKey($v);
                return $carry;
            },
            []
        );

        $resource        = !empty($criteria) ? $repository->findOneBy($criteria) : null;
        $existing        = $resource instanceof ResourceInterface;

        if ($existing && !$message->getConfiguration()->isUpdater()) return;

        $entity = $importHandle->handle($message, $resource);

        if (!$existing)
            $this->entityManager->persist($entity);
    }
}
