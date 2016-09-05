<?php

namespace SimpleThings\EntityAudit\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder->root('simple_things_entity_audit')
            ->children()
                ->arrayNode('audited_entities')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('global_ignore_columns')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('global_ignore_properties')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('table_prefix')->defaultValue('')->end()
                ->scalarNode('table_suffix')->defaultValue('_audit')->end()
                ->scalarNode('revision_field_name')->defaultValue('rev')->end()
                ->scalarNode('revision_type_field_name')->defaultValue('revtype')->end()
                ->scalarNode('revision_table_name')->defaultValue('revisions')->end()
                ->scalarNode('revision_id_field_type')->defaultValue('integer')->end()
                ->arrayNode('service')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('username_callable')->defaultValue('simplethings_entityaudit.username_callable.token_storage')->end()
                    ->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(function ($config) {
                    return $config['global_ignore_columns'] && $config['global_ignore_properties'];
                })
                ->thenInvalid('The `global_ignore_columns` and `global_ignore_properties` options are mutually exclusive. Please use `global_ignore_properties`.')
            ->end()
            ->beforeNormalization()
                ->ifTrue(function ($v) {
                    return isset($v['global_ignore_columns']);
                })
                ->then(function ($v) {
                    $v['global_ignore_properties'] = $v['global_ignore_columns'];
                    unset($v['global_ignore_columns']);

                    return $v;
                })
            ->end()
        ;

        return $builder;
    }
}
