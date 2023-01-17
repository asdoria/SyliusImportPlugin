<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Converter;

use Asdoria\SyliusImportPlugin\Converter\Model\NameConverterInterface;
use Asdoria\SyliusImportPlugin\Loader\YamlConverterFileLoader;
use Asdoria\SyliusImportPlugin\Traits\ContextTrait;

/**
 * Class NameConverter
 * @package Asdoria\SyliusImportPlugin\Converter
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class NameConverter implements NameConverterInterface
{
    use ContextTrait;

    /**
     * @var YamlConverterFileLoader
     */
    protected YamlConverterFileLoader $loader;

    /**
     * NameConverter constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->loader = new YamlConverterFileLoader($path);
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function normalize(string $propertyName): string
    {
        return $this->loader->getNormalize($this->getContext())[$propertyName] ?? $propertyName;
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function denormalize(string $propertyName): string
    {
        return $this->loader->getDenormalize($this->getContext())[$propertyName] ?? $propertyName;
    }

    /**
     * @return array
     */
    public function getExtraAttributes(): array
    {
        return $this->loader->getExtraAttributes($this->getContext());
    }

    /**
     * @return array
     */
    public function getPriorities(): array
    {
        return $this->loader->getPriorities($this->getContext());
    }

    /**
     * @return array
     */
    public function getResets(): array
    {
        return $this->loader->getResets($this->getContext());
    }

    /**
     * @return array
     */
    public function getIgnores(): array
    {
        return $this->loader->getIgnores($this->getContext());
    }
}
