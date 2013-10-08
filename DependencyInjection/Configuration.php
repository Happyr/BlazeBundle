<?php

namespace HappyR\BlazeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('happy_r_blaze')

            ->children()
                ->arrayNode('entities')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('variable')
                    ->treatNullLike(array())
                    //make sure that there is some config after each entity
                    ->validate()
                        ->ifTrue(function($entites) {
                            foreach($entites as $e){
                                if(!is_array($e)){
                                    return true;
                                }
                            }

                            return false;
                        })
                        ->thenInvalid('The happy_r_blaze.entities config %s must be an array.')
                    ->end()

                    //make sure route and parameters is set
                    ->validate()
                        ->ifTrue(function($entites) {
                            foreach($entites as $e){
                                if(!isset($e['route']) || !isset($e['parameters'])){
                                    return true;
                                }
                            }

                            return false;
                        })
                        ->thenInvalid('%s must contain "route" and "parameters".')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
