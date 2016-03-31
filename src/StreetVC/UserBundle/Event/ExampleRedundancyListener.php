<?php
/**
 * Created by PhpStorm.
 * User: dao
 * Date: 7/29/14
 * Time: 12:18 PM
 */

namespace StreetVC\UserBundle\Event;


class ExampleRedundancyListener {
    /**
     * @var DocumentManager
     */
    private $odm;

    /**
     * @param DocumentManager $odm
     */
    function __construct(DocumentManager $odm)
    {
        $this->odm = $odm;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof \Content\Source) {
            if ($args->hasChangedField('locale')) {
                // update post collection, "source.enabled" field
                $this->odm->createQueryBuilder('Content\Post')
                    ->update()
                    ->multiple(true)
                    ->field('source.enabled')->set($entity->isEnabled())
                    ->field('source.id')->equals($entity->getId())
                    ->getQuery()
                    ->execute();
            }
        }
    }
}
