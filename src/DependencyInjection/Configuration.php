<?php

namespace Happyr\BlazeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('happyr_blaze')

            ->children()
            ->arrayNode('objects')
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('variable')
            ->treatNullLike(array())
            //make sure that there is some config after each object
            ->validate()
            ->ifTrue(
                function ($objects) {
                    foreach ($objects as $o) {
                        if (!is_array($o)) {
                            return true;
                        }
                    }

                    return false;
                }
            )
            ->thenInvalid('The happyr_blaze.objects config %s must be an array.')
            ->end()

            //make sure route and parameters is set
            ->validate()
            ->ifTrue(
                function ($objects) {
                    foreach ($objects as $o) {
                        if (!isset($o['route']) || !isset($o['parameters'])) {
                            return true;
                        }
                    }

                    return false;
                }
            )
            ->thenInvalid('%s must contain "route" and "parameters".')
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
