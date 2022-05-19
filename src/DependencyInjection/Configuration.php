<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\DependencyInjection;

use Asdoria\SyliusImportPlugin\Controller\FacetController;
use Asdoria\SyliusImportPlugin\Controller\FacetGroupController;
use Asdoria\SyliusImportPlugin\Entity\Facet;
use Asdoria\SyliusImportPlugin\Entity\FacetGroup;
use Asdoria\SyliusImportPlugin\Entity\FacetGroupTranslation;
use Asdoria\SyliusImportPlugin\Entity\FacetTranslation;
use Asdoria\SyliusImportPlugin\Entity\FacetTypeGeneric;
use Asdoria\SyliusImportPlugin\Entity\FacetTypeProductAttribute;
use Asdoria\SyliusImportPlugin\Entity\FacetTypeProductOption;
use Asdoria\SyliusImportPlugin\Entity\FacetTypeTaxon;
use Asdoria\SyliusImportPlugin\Entity\Import;
use Asdoria\SyliusImportPlugin\Factory\FacetFactory;
use Asdoria\SyliusImportPlugin\Factory\FacetGroupFactory;
use Asdoria\SyliusImportPlugin\Factory\ImportFactory;
use Asdoria\SyliusImportPlugin\Form\Type\FacetGroupTranslationType;
use Asdoria\SyliusImportPlugin\Form\Type\FacetGroupType;
use Asdoria\SyliusImportPlugin\Form\Type\FacetTranslationType;
use Asdoria\SyliusImportPlugin\Form\Type\FacetType;
use Asdoria\SyliusImportPlugin\Form\Type\FacetTypeProductAttributeType;
use Asdoria\SyliusImportPlugin\Form\Type\FacetTypeProductOptionType;
use Asdoria\SyliusImportPlugin\Form\Type\FacetTypeTaxonType;
use Asdoria\SyliusImportPlugin\Form\Type\ImportType;
use Asdoria\SyliusImportPlugin\Model\FacetGroupInterface;
use Asdoria\SyliusImportPlugin\Repository\FacetGroupRepository;
use Asdoria\SyliusImportPlugin\Repository\FacetRepository;
use Asdoria\SyliusImportPlugin\Repository\ImportRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Asdoria\SyliusImportPlugin\DependencyInjection
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('asdoria_import');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('provider')->cannotBeEmpty()->end()
            ->scalarNode('doctrine_dbal_connection')->cannotBeEmpty()->end();

        return $treeBuilder;
    }
}
