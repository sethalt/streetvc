<?php
namespace StreetVC\Tests;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
//require_once(__DIR__ . "/../../../app/AppKernel.php");

//class BaseTestCase extends \PHPUnit_Framework_TestCase
class BaseTestCase extends KernelTestCase
{
//    protected $_container;

    /**
     * @var Symfony\Component\DependencyInjection\Container $container
     */
    protected $container;

    public function setUp()
    {
        self::bootKernel();
        /** @var Container $container */
        $this->container = static::$kernel->getContainer();
    }

    /*
    public function __construct()
    {
        $kernel = new \AppKernel("test", true);
        $kernel->boot();
        $this->_container = $kernel->getContainer();
        parent::__construct();
    }
    */

    protected function get($service)
    {
        return $this->container->get($service);
//        return $this->_container->get($service);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }
}