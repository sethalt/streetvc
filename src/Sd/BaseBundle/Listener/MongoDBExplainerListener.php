<?php
/**
 * Created by PhpStorm.
 * User: dao
 * Date: 7/29/14
 * Time: 2:47 PM
 */

namespace Sd\BaseBundle\Listener;

use Doctrine\MongoDB\Event\FindEventArgs;
use Doctrine\MongoDB\Event\MutableEventArgs;
use Doctrine\ODM\MongoDB\DocumentManager;

class MongoDBExplainerListener
{
    private $dm;
    private $dbName;
    private $lastQuery;
    private $explains = array();

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
        $this->dbName = $dm->getConfiguration()->getDefaultDB();
    }

    public function collectionPreFind(FindEventArgs $args)
    {
        $this->lastQuery = $args->getQuery();
    }

    public function collectionPostFind(MutableEventArgs $args)
    {
        $e = new \Exception();
        $collection = $args->getInvoker();
        $cursor = $args->getData();
        $explain = $cursor->explain();
        $this->explains[] = array(
            'explain' => $explain,
            'query' => $this->lastQuery,
            'database' => $collection->getDatabase()->getName(),
            'collection' => $collection->getName(),
            'traceAsString' => $e->getTraceAsString()
        );
    }

    private function getCollection()
    {
        return $this->dm->getConnection()->selectCollection($this->dbName, 'query_explains');
    }

    public function __destruct()
    {
        $this->getCollection()->batchInsert($this->explains);
    }

}
