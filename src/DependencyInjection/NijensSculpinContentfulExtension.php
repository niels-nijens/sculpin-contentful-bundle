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

use Contentful\ContentfulBundle\ContentfulBundle;
use Contentful\ContentfulBundle\DependencyInjection\ContentfulExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Loads the services and configuration into the service container.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
final class NijensSculpinContentfulExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nijens_sculpin_contentful.assets.output_path', $config['assets']['output_path']);

        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');

        $this->loadContentfulExtension($configs, $container);
    }

    /**
     * Loads the extension from the {@see ContentfulBundle}.
     */
    private function loadContentfulExtension(array $configs, ContainerBuilder $container): void
    {
        $configs = array_map(
            function (array $config) {
                return $config['contentful'];
            },
            $configs
        );

        $contentfulExtension = new ContentfulExtension();
        $contentfulExtension->load($configs, $container);
    }
}
