<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Registry;

use Asdoria\SyliusImportPlugin\Registry\Model\ServiceRegistryInterface;

/**
 * Class ServiceRegistry
 * @package Asdoria\SyliusImportPlugin\Registry
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class ServiceRegistry implements  ServiceRegistryInterface
{
    /**
     * @var iterable
     */
    protected iterable $handlers;

    /**
     * SerializerServiceRegistry constructor.
     *
     * @param iterable $handlers
     */
    public function __construct(iterable $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return iterator_to_array($this->handlers);
    }

    /**
     * @param string $identifier
     *
     * @return mixed|object|null
     */
    public function get(string $identifier)
    {
        return $this->all()[$identifier] ?? null;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier): bool
    {
        return isset($this->all()[$identifier]);
    }
}
