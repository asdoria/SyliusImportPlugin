<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Message;

/**
 * Interface ImportNotificationInterface
 * @package Asdoria\SyliusImportPlugin\Message
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface ImportNotificationInterface
{
    /**
     * @return array
     */
    public function getConfiguration(?string $key = null): array;

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
