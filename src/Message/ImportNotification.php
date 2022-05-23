<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Message;

use Asdoria\SyliusImportPlugin\Configurator\Configuration;
use Asdoria\SyliusImportPlugin\Configurator\ConfigurationInterface;

/**
 * Class ImportNotification
 *
 * @author Philippe Vesin <pve.asdoria@gmail.com>
 */
class ImportNotification implements ImportNotificationInterface
{
    protected ?ConfigurationInterface $configuration = null;
    protected array $data = [];
    protected ?string $entityClass;

    /**
     * @param array $subject
     */
    public function __construct(array $data, string $entityClass, ?ConfigurationInterface $configuration = null)
    {
        $this->configuration  = $configuration;
        $this->entityClass    = $entityClass;
        $this->data           = $data;
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface
    {
        if (!$this->configuration instanceof ConfigurationInterface) {
            $this->configuration = new Configuration();
        }

        return $this->configuration;
    }
    /**
     * @return array
     */
    public function getDataByKey(?string $key)
    {
        return $this->getData()[$key] ?? null;
    }

    /**
     * @return string|null
     */
    public function getEntityClass(): ?string
    {
        return $this->entityClass;
    }

    /**
     * @return array|null
     */
    public function getData(): array
    {
        return $this->data;
    }
}
