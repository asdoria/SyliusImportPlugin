<?php


namespace Asdoria\SyliusImportPlugin\Loader;

use Asdoria\SyliusImportPlugin\Loader\Model\YamlConverterFileLoaderInterface;
use Symfony\Component\Serializer\Exception\MappingException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlConverterFileLoader
 * @package Asdoria\SyliusImportPlugin\Loader
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class YamlConverterFileLoader implements YamlConverterFileLoaderInterface
{
    private ?Parser $yamlParser = null;

    /**
     * An array of YAML class descriptions.
     *
     * @var array
     */
    private array $classes = [];

    /**
     * @var string
     */
    protected string $file;

    /**
     * @param string $file The mapping file to load
     *
     * @throws MappingException if the mapping file does not exist or is not readable
     */
    public function __construct(string $file)
    {
        if (!is_file($file)) {
            throw new MappingException(sprintf('The mapping file %s does not exist', $file));
        }

        if (!is_readable($file)) {
            throw new MappingException(sprintf('The mapping file %s is not readable', $file));
        }

        $this->file = $file;
    }
    /**
     * {@inheritdoc}
     */
    public function loadClass(string $context) : array
    {
        if (empty($this->classes)) {
            $this->classes = $this->getClassesFromYaml();
        }

        if (!$this->classes) {
            return [];
        }

        return $this->classes[$context] ?? [];
    }

    /**
     * @param $className
     *
     * @return array
     */
    public function getNormalize($className): array
    {
        return $this->loadClass($className)['normalize'] ?? [];
    }

    /**
     * @param $className
     *
     * @return array
     */
    public function getDenormalize($className): array
    {
        return $this->loadClass($className)['denormalize'] ?? [];
    }


    /**
     * @param $className
     *
     * @return array
     */
    public function getExtraAttributes($className): array
    {
        return $this->loadClass($className)['extra_attributes'] ?? [];
    }

    /**
     * @param $className
     *
     * @return array
     */
    public function getPriorities($className): array
    {
        return $this->loadClass($className)['priority'] ?? [];
    }

    /**
     * @param $className
     *
     * @return array
     */
    public function getResets($className): array
    {
        return $this->loadClass($className)['reset'] ?? [];
    }

    /**
     * @param $className
     *
     * @return array
     */
    public function getIgnores($className): array
    {
        return $this->loadClass($className)['ignore'] ?? [];
    }

    /**
     * Return the names of the classes mapped in this file.
     *
     * @return string[] The classes names
     */
    public function getMappedClasses()
    {
        if (null === $this->classes) {
            $this->classes = $this->getClassesFromYaml();
        }

        return array_keys($this->classes);
    }

    /**
     * @return array|\stdClass|\Symfony\Component\Yaml\Tag\TaggedValue
     */
    private function getClassesFromYaml()
    {
        if (!stream_is_local($this->file)) {
            throw new MappingException(sprintf('This is not a local file "%s".', $this->file));
        }

        if (null === $this->yamlParser) {
            $this->yamlParser = new Parser();
        }

        $classes = $this->yamlParser->parseFile($this->file, Yaml::PARSE_CONSTANT);

        if (empty($classes)) {
            return [];
        }

        if (!\is_array($classes)) {
            throw new MappingException(sprintf('The file "%s" must contain a YAML array.', $this->file));
        }

        return $classes;
    }
}
