<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Registry\Model;


/**
 * Interface ServiceRegistryInterface
 * @package Asdoria\SyliusImportPlugin\Registry\Model
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface ServiceRegistryInterface
{
    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier): bool;

    /**
     * @param string $identifier
     * @return object|null
     */
    public function get(string $identifier);
}
