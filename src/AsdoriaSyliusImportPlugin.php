<?php

declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AsdoriaSyliusImportPlugin
 * @package Asdoria\SyliusImportPlugin
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
final class AsdoriaSyliusImportPlugin extends Bundle
{
    use SyliusPluginTrait;
}
