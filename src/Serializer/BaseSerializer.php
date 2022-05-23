<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Serializer;


use Asdoria\SyliusImportPlugin\Configurator\ConfigurationInterface;
use Asdoria\SyliusImportPlugin\Converter\NameConverter;
use Asdoria\SyliusImportPlugin\Normalizer\ObjectNormalizer;
use Asdoria\SyliusImportPlugin\Registry\Model\ServiceRegistryInterface;
use Asdoria\SyliusImportPlugin\Serializer\Callback\DateTimeCallbackTrait;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Asdoria\SyliusImportPlugin\Traits\ConverterPathTrait;
use Asdoria\SyliusImportPlugin\Traits\EntityManagerTrait;
use Asdoria\SyliusImportPlugin\Traits\ServiceRegistryTrait;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Psr\Log\LoggerAwareTrait;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * Class BaseSerializer
 * @package Asdoria\SyliusImportPlugin\Serializer
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class BaseSerializer implements SerializerInterface
{
    use ConverterPathTrait;
    use DateTimeCallbackTrait;
    use LoggerAwareTrait;
    use EntityManagerTrait;
    use ServiceRegistryTrait;
    protected ?Serializer $serializer = null;
    protected ?string $context = null;
    protected ConfigurationInterface $configuration;

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
     * @param ResourceInterface $object
     * @param string            $key
     *
     * @return array
     */
    public function getSerializerContext(ResourceInterface $object, string $key): array {
        if(!$this->getConfiguration()->isUpdater()) return [];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $resource         = $propertyAccessor->getValue($object, $key);
        if(!$resource instanceof ResourceInterface)  return [];

        return [
            AbstractNormalizer::OBJECT_TO_POPULATE => $resource
        ];
    }

    /**
     * @return ServiceRegistryInterface
     */
    public function getSerializerResolver(): ServiceRegistryInterface
    {
        return $this->serializerResolver;
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
            ObjectNormalizer::IMPORT_CALLBACKS => $this->getCallbacks(),
        ];
    }

    /**
     * @return array
     */
    protected function getNormalizers(): array
    {
        $normalizer = new ObjectNormalizer(
            null,
            $this->getNameConverter(),
            null,
            null,
            null,
            null,
            $this->getNormalizerContext()
        );

        return [$normalizer];
    }

    /**
     * @return NameConverter|null
     */
    protected function getNameConverter(): ?NameConverter
    {

        if ($this->nameConverter instanceof NameConverter) {
            return $this->nameConverter;
        }

        $path = ($this->getConfiguration()->getProvider() !== ConfigurationInterface::_DEFAULT_PROVIDER) ?
            sprintf($this->getConverterPath(), $this->getConfiguration()->getProvider()):
            $this->getConfiguration()->getConverterPath();

        $this->nameConverter = new NameConverter($path);
        $this->nameConverter->setContext($this->getContext());

        return $this->nameConverter;
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
        $serializer->setConfiguration($this->getConfiguration());

        return $serializer;
    }

    /**
     * @return array
     */
    protected function getCallbacks(): array
    {
        return [
            'createdAt' => $this->dateTimeCallback(),
            'updatedAt' => $this->dateTimeCallback(),
        ];
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

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * @param ConfigurationInterface $configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->configuration = $configuration;
    }
}
