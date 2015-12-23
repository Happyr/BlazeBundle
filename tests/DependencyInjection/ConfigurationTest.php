<?php

namespace tests\Happyr\BlazeBundle\DependencyInjection;

use Happyr\BlazeBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;

class ConfigurationTest  extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testValuesAreInvalidIfRequiredValueIsNotProvided()
    {
        $this->assertConfigurationIsValid(array(
                array(
                    'objects'=>array(
                        'Acme\Foo'=>array(
                            'edit'=>array(
                                'route'=>'foo_edit',
                                'parameters'=>array('id'=>'getId'),
                            )
                        )
                    )
                )
        ));
    }
}