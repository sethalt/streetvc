<?php
namespace StreetVC\BaseBundle\Model;
use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * #DI\Service("streetvc.document_manager", public=false)
 * @author dao
 *
 */
class ResourceManager
{
    private $class;
    private $dispatcher;
    private $dm;

    /**
     * #DI\InjectParams()
     * @param unknown $class
     * @param EventDispatcherInterface $dispatcher
     * @param DocumentManager $dm
     */
    public function __construct($class, EventDispatcherInterface $dispatcher, DocumentManager $dm)
    {
        $this->class = $class;
        $this->dispatcher = $dispatcher;
        $this->dm = $dm;
    }



}