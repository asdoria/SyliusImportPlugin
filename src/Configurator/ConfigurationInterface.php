<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Configurator;
/**
 * Interface ConfigurationInterface
 * @package Asdoria\SyliusImportPlugin\Configurator
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface ConfigurationInterface
{
    const _PRESTASHOP_PROVIDER = 'prestashop';
    const _DEFAULT_PROVIDER = 'custom';

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string;

    /**
     * @param string|null $identifier
     */
    public function setIdentifier(?string $identifier): void;

    /**
     * @param string|null $provider
     */
    public function setProvider(?string $provider): void;

    /**
     * @return string|null
     */
    public function getProvider(): ?string;

    /**
     * @return bool
     */
    public function isUpdater(): bool;

    /**
     * @param bool $updater
     */
    public function setUpdater(bool $updater): void;

    /**
     * @return string|null
     */
    public function getConverterPath(): ?string;

    /**
     * @param string|null $converterPath
     */
    public function setConverterPath(?string $converterPath): void;
}
