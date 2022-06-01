<?php

declare(strict_types=1);


namespace Asdoria\SyliusImportPlugin\Traits;


use Sylius\Component\Core\Uploader\ImageUploaderInterface;

/**
 * Class ImageUploaderTrait
 * @package Asdoria\SyliusImportPlugin\Traits
 *
 * @author  Philippe Vesin <pve.asdoria@gmail.com>
 */
trait ImageUploaderTrait
{
    protected ImageUploaderInterface $uploader;

    /**
     * @return ImageUploaderInterface
     */
    public function getUploader(): ImageUploaderInterface
    {
        return $this->uploader;
    }

    /**
     * @param ImageUploaderInterface $uploader
     */
    public function setUploader(ImageUploaderInterface $uploader): void
    {
        $this->uploader = $uploader;
    }
}
