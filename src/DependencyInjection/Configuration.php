<?php

declare(strict_types=1);

/*
 * This file is part of the niels-nijens/sculpin-contentful-bundle package.
 *
 * (c) Niels Nijens <nijens.niels@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nijens\SculpinContentfulBundle\DependencyInjection;

use Contentful\ContentfulBundle\DependencyInjection\Configuration as ContentfulConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Validates and merges configuration from the configuration files.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class Configuration extends ContentfulConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nijens_sculpin_contentful');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()
            ->arrayNode('assets')
                ->children()
                    ->scalarNode('output_path')
                        ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->arrayNode('content_types')
                ->useAttributeAsKey('name', false)
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('name')
                            ->info('The name used in Sculpin for this content type')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                        ->scalarNode('content_type')
                            ->info('The name of the content type in Contentful.')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->end()
                        ->booleanNode('enabled')
                            ->info('Indicating if creating pages for this content type is enabled.')
                            ->defaultTrue()
                            ->end()
                        ->scalarNode('filename_property')
                            ->info('The Contentful entry property used as filename for the EntrySource.')
                            ->defaultValue('id')
                            ->end()
                        ->scalarNode('relative_path')
                            ->info(
                                'The relative path used for the EntrySource. Properties from the Contentful entry '.
                                'can be used for dynamic path generation. For example: {{contentful:content_type}} '.
                                'will add the content type to the path.'
                            )
                            ->defaultValue('{{contentful:content_type}}/{{filename}}')
                            ->end()
                        ->arrayNode('additional_metadata')
                            ->info('Allows adding additional metadata to an EntrySource eg. a layout property.')
                            ->variablePrototype()
                            ->end()
                    ->end()
                ->end()
            ->end();

        $rootNode->append(parent::getConfigTreeBuilder()->getRootNode());

        return $treeBuilder;
    }
}
