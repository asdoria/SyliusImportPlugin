<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Loader\Model;


/**
 * Interface YamlConverterFileLoaderInterface
 * @package Asdoria\SyliusImportPlugin\Loader\Model
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface YamlConverterFileLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadClass(string $context) : array;

    /**
     * @param $className
     *
     * @return array
     */
    public function getNormalize($className): array;
    /**
     * @param $className
     *
     * @return array
     */
    public function getDenormalize($className): array;

    /**
     * @param $className
     *
     * @return array
     */
    public function getExtraAttributes($className): array;

    /**
     * @param $className
     *
     * @return array
     */
    public function getPriorities($className): array;

    /**
     * @param $className
     *
     * @return array
     */
    public function getResets($className): array;

    /**
     * Return the names of the classes mapped in this file.
     *
     * @return string[] The classes names
     */
    public function getMappedClasses();
}
