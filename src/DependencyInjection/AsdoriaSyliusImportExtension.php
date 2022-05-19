<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\DependencyInjection;

use Asdoria\Bundle\MediaBundle\Uploader\File\Uploader as FileUploader;
use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class AsdoriaSyliusImportExtension
 * @package Asdoria\SyliusImportPlugin\DependencyInjection
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
final class AsdoriaSyliusImportExtension extends Extension
{


    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $container->setParameter('asdoria.import.provider', $config['provider']);
        $container->setParameter('asdoria.import.doctrine_dbal_connection', $config['doctrine_dbal_connection']);
        $loader->load('services.yaml');
    }

}
