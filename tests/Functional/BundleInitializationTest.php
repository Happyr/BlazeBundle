<?php
namespace tests\Happyr\BlazeBundle\Functional;

use Happyr\BlazeBundle\HappyrBlazeBundle;
use Happyr\BlazeBundle\Service\BlazeManagerInterface;
use Nyholm\BundleTest\BaseBundleTestCase;

class BundleInitializationTest extends BaseBundleTestCase
{
    protected function getBundleClass()
    {
        return HappyrBlazeBundle::class;
    }

    public function testInitBundle()
    {
        // Create a new Kernel
        $kernel = $this->createKernel();

        // Add some configuration
        $kernel->addConfigFile(__DIR__.'/Resources/services.yml');

        // Boot the kernel.
        $this->bootKernel();

        // Get the containter
        $container = $this->getContainer();

        // Test if you services exists
        $this->assertTrue($container->has('test.happyr.blaze'));
        $service = $container->get('test.happyr.blaze');
        $this->assertInstanceOf(BlazeManagerInterface::class, $service);
    }

}
