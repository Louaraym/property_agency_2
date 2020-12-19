<?php

namespace App\EventSubscriber;

use App\Entity\Picture;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{
    /**
     * @var CacheManager
     */
    private CacheManager $cacheManager;
    /**
     * @var UploaderHelper
     */
    private UploaderHelper $uploaderHelper;

    public function __construct(CacheManager $cacheManager, UploaderHelper $uploaderHelper)
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
       $entity = $eventArgs->getEntity();

       if (!$entity instanceof Picture){
           return;
       }

       if ($entity->getImageFile() instanceof UploadedFile){
           $path = $this->uploaderHelper->asset($entity, 'imageFile');
           $this->cacheManager->remove($path);
       }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();

        if (!$entity instanceof Picture){
            return;
        }

        $path = $this->uploaderHelper->asset($entity, 'imageFile');
        $this->cacheManager->remove($path);
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
                 'preUpdate',
                 'preRemove',
                ];
    }
}
