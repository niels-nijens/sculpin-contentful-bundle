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
            ->end();

        $rootNode->append(parent::getConfigTreeBuilder()->getRootNode());

        return $treeBuilder;
    }
}
