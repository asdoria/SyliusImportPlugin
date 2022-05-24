<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Traits;

use App\Kernel;

/**
 *
 */
trait KernelTrait
{
    protected  ?Kernel $kernel = null;

    /**
     * @return Kernel|null
     */
    public function getKernel(): ?Kernel
    {
        return $this->kernel;
    }

    /**
     * @param Kernel|null $kernel
     */
    public function setKernel(?Kernel $kernel): void
    {
        $this->kernel = $kernel;
    }
}
