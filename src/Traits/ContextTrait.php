<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Traits;


/**
 * Class ContextTrait
 * @package Asdoria\SyliusImportPlugin\Traits
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
trait ContextTrait
{
    protected ?string $context = null;

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

}
