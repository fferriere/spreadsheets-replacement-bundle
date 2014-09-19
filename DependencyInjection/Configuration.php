<?php

namespace Fferriere\Bundle\SpreadsheetsReplacementBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fferriere_spreadsheets_replacement');

        $defaultPatternPath = dirname(__DIR__) . DIRECTORY_SEPARATOR
                            . 'Resources' . DIRECTORY_SEPARATOR
                            . 'config' . DIRECTORY_SEPARATOR
                            . 'replacementPattern.php';

        $rootNode
            ->children()
                ->scalarNode('data_path')->end()
                ->scalarNode('replacement_pattern_path')
                    ->defaultValue($defaultPatternPath)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
