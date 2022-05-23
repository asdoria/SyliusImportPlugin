<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Traits;


use Asdoria\SyliusImportPlugin\Registry\Model\ServiceRegistryInterface;

/**
 * Class ServiceRegistryTrait
 * @package Asdoria\SyliusImportPlugin\Traits
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
trait ServiceRegistryTrait
{
    protected ?ServiceRegistryInterface $serviceRegistry = null;

    /**
     * @return ServiceRegistryInterface|null
     */
    public function getServiceRegistry(): ?ServiceRegistryInterface
    {
        return $this->serviceRegistry;
    }

    /**
     * @param ServiceRegistryInterface|null $serviceRegistry
     */
    public function setServiceRegistry(?ServiceRegistryInterface $serviceRegistry): void
    {
        $this->serviceRegistry = $serviceRegistry;
    }
}
