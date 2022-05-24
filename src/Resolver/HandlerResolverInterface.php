<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Resolver;

use Asdoria\SyliusImportPlugin\Message\ImportNotificationInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Interface HandlerResolverInterface
 * @package Asdoria\SyliusImportPlugin\Resolver
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface HandlerResolverInterface
{
    /**
     * @param ImportNotificationInterface $message
     * @param ResourceInterface|null      $resource
     *
     * @return ResourceInterface|null
     */
    public function handle(ImportNotificationInterface $message, ?ResourceInterface $resource = null): ?ResourceInterface;
}
