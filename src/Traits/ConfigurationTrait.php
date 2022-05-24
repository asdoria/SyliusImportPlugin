<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Traits;


use Asdoria\SyliusImportPlugin\Configurator\ConfigurationInterface;

/**
 * Class ConfigurationTrait
 * @package Asdoria\SyliusImportPlugin\Traits
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
trait ConfigurationTrait
{
    protected ConfigurationInterface $configuration;

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * @param ConfigurationInterface $configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->configuration = $configuration;
    }
}
