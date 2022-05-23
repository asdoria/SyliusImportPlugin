<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Message;

use Asdoria\SyliusImportPlugin\Configurator\ConfigurationInterface;

/**
 * Interface ImportNotificationInterface
 * @package Asdoria\SyliusImportPlugin\Message
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface ImportNotificationInterface
{
    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface;

    /**
     * @return string|null
     */
    public function getEntityClass(): ?string;

    /**
     * @return array|null
     */
    public function getData(): array;

    /**
     * @return mixed
     */
    public function getDataByKey(?string $key);
}
