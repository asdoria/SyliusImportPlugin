<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Configurator;
use Asdoria\SyliusImportPlugin\Traits\ConverterPathTrait;

/**
 * Class Configuration
 *
 * @author Philippe Vesin <pve.asdoria@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    use ConverterPathTrait;
    protected ?string $identifier = 'id';
    protected ?string $provider = self::_CUSTOM_PROVIDER;
    protected bool $updater = false;

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     */
    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @param string|null $provider
     */
    public function setProvider(?string $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @return bool
     */
    public function isUpdater(): bool
    {
        return $this->updater;
    }

    /**
     * @param bool $updater
     */
    public function setUpdater(bool $updater): void
    {
        $this->updater = $updater;
    }

}
