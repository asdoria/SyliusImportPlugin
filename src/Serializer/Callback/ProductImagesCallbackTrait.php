<?php
declare(strict_types=1);

namespace Asdoria\SyliusImportPlugin\Serializer\Callback;

use App\Entity\Product\ProductImage;
use App\Model\Product\ProductInterface;
use Asdoria\SyliusImportPlugin\Serializer\Model\SerializerInterface;
use Asdoria\SyliusImportPlugin\Traits\ImageUploaderTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 *
 */
trait ProductImagesCallbackTrait
{
    use ImageUploaderTrait;
    /**
     * @return \Closure
     */
    protected function productImagesCallback(): \Closure
    {
        /** @var SerializerInterface $serializer */
        $serializer = $this->getSerializerByClass(ProductImage::class);

        return function ($value, ProductInterface $object, $key, $data) use ($serializer): Collection {
            if(!is_array($value)) $value = json_decode($value, true);
            $context = $this->getSerializerContext($object, $key);
            $images  = $object->hasImages() ? $object->getImages() : new ArrayCollection();
            foreach ($value as $item) {
                /** @var ProductImageInterface $img */
                $img = $serializer->deserialize([], null, $context);
                $value = $this->saveTmpContent($item);
                $img->setFile(new File($value));
                $this->getUploader()->upload($img);
                $this->getEntityManager()->persist($img);
                $img->setOwner($object);
                $images->add($img);
            }
            return $images;
        };
    }

    abstract public function getEntityManager(): ?EntityManagerInterface;
    abstract protected function getSerializerByClass(string $className): ?SerializerInterface;
    
    /**
     * @param string $content
     *
     * @return string
     */
    public function saveTmpContent(string $url): string
    {
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $filename = pathinfo($url, PATHINFO_FILENAME);
        $content= file_get_contents($url);
        $tmpfname = tempnam("/tmp", $filename);
        $temp     = fopen($tmpfname, "w");
        $newName  = sprintf("/tmp/%s.%s", $filename, $ext);
        fwrite($temp, $content);
        fclose($temp);
        rename($tmpfname, $newName);
        return $newName;
    }
}
