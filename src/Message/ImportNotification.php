<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Message;

/**
 * Class ImportNotification
 *
 * @author Philippe Vesin <pve.asdoria@gmail.com>
 */
class ImportNotification implements ImportNotificationInterface
{
    protected array $configuration = [];
    protected array $data = [];
    protected ?string $entityClass;

    /**
     * @param array $subject
     */
    public function __construct(array $data, string $entityClass, array $configuration = [])
    {
        $this->configuration  = $configuration;
        $this->entityClass    = $entityClass;
        $this->data           = $data;
    }

    /**
     * @return array
     */
    public function getConfiguration(?string $key = null): array
    {
        if(!empty($key)) $this->configuration[$key] ?? [];
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
