<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Traits;


/**
 *
 */
trait DefaultLocaleTrait
{
    protected ?string $defaultLocale = null;

    /**
     * @return string|null
     */
    public function getDefaultLocale(): ?string
    {
        return $this->defaultLocale;
    }

    /**
     * @param string|null $defaultLocale
     */
    public function setDefaultLocale(?string $defaultLocale): void
    {
        $this->defaultLocale = $defaultLocale;
    }
}
