<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer\Model;


use Asdoria\SyliusImportPlugin\Configurator\ConfigurationInterface;
use Asdoria\SyliusImportPlugin\Registry\Model\ServiceRegistryInterface;
use Doctrine\DBAL\Connection;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Interface SerializerInterface
 * @package Asdoria\SyliusImportPlugin\Serializer\Model
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function deserialize(array $data, $type = null, array $context = []);

    /**
     * @return ServiceRegistryInterface
     */
    public function getSerializerResolver(): ServiceRegistryInterface;
    /**
     * @param ServiceRegistryInterface $serializerResolver
     */
    public function setSerializerResolver(ServiceRegistryInterface $serializerResolver): void;

    /**
     * @return string|null
     */
    public function getContext(): ?string;

    /**
     * @param string|null $context
     */
    public function setContext(?string $context): void;

    public function getSerializerContext(ResourceInterface $object, string $key): array;

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration): void;
}
