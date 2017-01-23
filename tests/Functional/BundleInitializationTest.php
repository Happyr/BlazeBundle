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
        // Boot the kernel.
        $this->bootKernel();

        // Get the containter
        $container = $this->getContainer();

        // Test if you services exists
        $this->assertTrue($container->has('happyr.blaze'));
        $service = $container->get('happyr.blaze');
        $this->assertInstanceOf(BlazeManagerInterface::class, $service);
    }

}
