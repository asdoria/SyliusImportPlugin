<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Serializer\Model;


use Asdoria\SyliusImportPlugin\Registry\Model\ServiceRegistryInterface;
use Doctrine\DBAL\Connection;

/**
 * Interface SerializerInterface
 * @package Asdoria\SyliusImportPlugin\Serializer\Model
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
interface SerializerInterface
{
    /**
     * @return Connection
     */
    public function getConnection(): Connection;

    /**
     * @return string|null
     */
    public function getContext(): ?string;
    /**
     * @param string|null $context
     */
    public function setContext(?string $context): void;

    /**
     * {@inheritdoc}
     */
    public function deserialize(array $data, $type = null, array $context = []);

    /**
     * @return ServiceRegistryInterface
     */
    public function getSerializerResolver(): ServiceRegistryInterface;

    /**
     * @param array $importerData
     */
    public function setImporterData(array $importerData): void;


    /**
     * @param string|null $key
     *
     * @return array|null
     */
    public function getImporterData(string $key = null): ?array;

    /**
     * @param ServiceRegistryInterface $serializerResolver
     */
    public function setSerializerResolver(ServiceRegistryInterface $serializerResolver): void;

}
