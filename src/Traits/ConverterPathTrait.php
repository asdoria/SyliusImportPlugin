<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Traits;


/**
 * Class ConverterPathTrait
 * @package Asdoria\SyliusImportPlugin\Traits
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
trait ConverterPathTrait
{
    protected ?string $converterPath = null;

    /**
     * @return string|null
     */
    public function getConverterPath(): ?string
    {
        if (empty($this->converterPath)) return '';

        return $this->converterPath;
    }

    /**
     * @param string|null $converterPath
     */
    public function setConverterPath(?string $converterPath): void
    {
        $this->converterPath = $converterPath;
    }
}
