<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Serializer;


use Asdoria\SyliusImportPlugin\Registry\Model\ServiceRegistryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class BaseSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class BaseSerializer implements SerializerInterface
{
    /**
     * @var Serializer
     */
    protected Serializer $serializer;


    /**
     * @var string|null
     */
    protected $context;

    /**
     * @var ServiceRegistryInterface
     */
    protected $serializerResolver;

    /**
     * @var array
     */
    protected $importerData = [];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * BaseSerializer constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param                        $path
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Connection
     */
    public function getConnection() : Connection {
        return $this->entityManager->getConnection();
    }

    /**
     * @return string|null
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * @param string|null $context
     */
    public function setContext(?string $context): void
    {
        $this->context = $context;
    }

    /**
     * @return ServiceRegistryInterface
     */
    public function getSerializerResolver(): ServiceRegistryInterface
    {
        return $this->serializerResolver;
    }

    /**
     * @param array $importerData
     */
    public function setImporterData(array $importerData): void
    {
        $this->importerData = $importerData;
    }

    /**
     * @param string|null $key
     *
     * @return array|null
     */
    public function getImporterData(string $key = null): ?array
    {
        return $this->importerData[$key] ?? null;
    }

    /**
     * @param ServiceRegistryInterface $serializerResolver
     */
    public function setSerializerResolver(ServiceRegistryInterface $serializerResolver): void
    {
        $this->serializerResolver = $serializerResolver;
    }

    /**
     * @return array
     */
    protected function getNormalizerContext() : array {
        return [
            ObjectNormalizer::CALLBACKS => $this->getCallbacks(),
            ObjectNormalizer::LOGGER => $this->logger
        ];
    }

    /**
     * @return array
     */
    protected function getNormalizers(): array
    {
        $nameConverter = $this->getNameConverter();
        $normalizer = new ObjectNormalizer(
            null,
            $nameConverter,
            null,
            null,
            null,
            null,
            $this->getNormalizerContext()
        );
        $normalizer->setExtraAttributes($nameConverter->getExtraAttributes());
        $normalizer->setBeforeInserts($nameConverter->getBeforeInserts());
        $normalizer->setEm($this->entityManager);

        return [$normalizer];
    }

    /**
     * @return SerializerInterface|Serializer
     */
    protected function getSerializer()
    {
        if (!$this->serializer instanceof SerializerInterface) {
            $this->serializer = new Serializer($this->getNormalizers(), [new JsonEncoder()]);
        }

        return $this->serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize(array $data, $type = null, array $context = [])
    {
        $this->logger = $context[ObjectNormalizer::LOGGER] ?? $this->logger;
        return $this->getSerializer()->deserialize(
            json_encode($data),
            !empty($type) ? $type: $this->getContext(),
            'json',
            $context
        );
    }

    /**
     * @param string $className
     *
     * @return SerializerInterface|null
     */
    protected function getSerializerByClass(string $className): ?SerializerInterface {
        /** @var SerializerInterface $serializer */
        $serializer  = $this->getSerializerResolver()->get($className);
        $serializer->setContext($className);
        $serializer->setSerializerResolver($this->getSerializerResolver());
        $serializer->setImporterData($this->importerData);

        return $serializer;
    }

    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [];
    }

    /**
     * @return string
     */
    protected function getTableName(): string
    {
        return $this->getClassMetadata()->getTableName();
    }

    /**
     * @return ClassMetadataInfo
     */
    protected function getClassMetadata() : ClassMetadataInfo {
        return $this->entityManager->getMetadataFactory()->getMetadataFor($this->getContext());
    }

    /**
     * @param $propertyName
     *
     * @return string
     */
    protected function camelCaseToSnakeCase($propertyName)
    {
        return strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($propertyName)));
    }
}
