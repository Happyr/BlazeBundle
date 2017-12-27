<?php

namespace tests\Happyr\BlazeBundle\DependencyInjection;

use Happyr\BlazeBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testValuesAreInvalidIfRequiredValueIsNotProvided()
    {
        $this->assertConfigurationIsValid([
                [
                    'objects' => [
                        'Acme\Foo' => [
                            'edit' => [
                                'route' => 'foo_edit',
                                'parameters' => ['id' => 'getId'],
                            ],
                        ],
                    ],
                ],
        ]);
    }
}
