<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Converter\Model;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface as BaseNameConverterInterface;

/**
 * Interface NameConverterInterface
 * @package Asdoria\SyliusImportPlugin\Converter\Model
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface NameConverterInterface extends BaseNameConverterInterface
{
    /**
     * @return string|null
     */
    public function getContext(): ?string;

    /**
     * @param string|null $context
     */
    public function setContext(?string $context): void;

    /**
     * @return array
     */
    public function getExtraAttributes(): array;

    /**
     * @return array
     */
    public function getPriorities(): array;

    /**
     * @return array
     */
    public function getResets(): array;

    /**
     * @return array
     */
    public function getIgnores(): array;
}
