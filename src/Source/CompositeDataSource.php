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

namespace Nijens\SculpinContentfulBundle\Source;

use Contentful\Delivery\Client\ClientInterface;
use Sculpin\Core\Source\CompositeDataSource as SculpinCompositeDataSource;
use Sculpin\Core\Source\SourceSet;

/**
 * The composite data source that creates separate data sources for each Contentful content type.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class CompositeDataSource extends SculpinCompositeDataSource
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $contentTypes;

    /**
     * @var string
     */
    private $assetsPath;

    /**
     * Creates a new {@see CompositeDataSource} instance.
     */
    public function __construct(ClientInterface $client, array $contentTypes, string $assetsPath)
    {
        $this->client = $client;
        $this->contentTypes = $contentTypes;
        $this->assetsPath = $assetsPath;

        parent::__construct([]);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(SourceSet $sourceSet): void
    {
        $dataSources = $this->dataSources();
        if (empty($dataSources)) {
            $this->addContentTypesAsDataSource();

            $this->addDataSource(new AssetDataSource($this->client, $this->assetsPath));
        }

        parent::refresh($sourceSet);
    }

    /**
     * Adds the configured content types as {@see ContentTypeDataSource}.
     */
    private function addContentTypesAsDataSource(): void
    {
        $contentTypeConfigurations = $this->createContentTypeConfigurations();
        foreach ($contentTypeConfigurations as $contentTypeConfiguration) {
            $contentTypeDataSource = new ContentTypeDataSource(
                $contentTypeConfiguration->getContentType(),
                $this->client
            );

            $this->addDataSource($contentTypeDataSource);
        }
    }

    /**
     * Returns a list of ContentTypeConfiguration instances for the configured content types.
     *
     * @return ContentTypeConfiguration[]
     */
    private function createContentTypeConfigurations(): array
    {
        $contentTypeConfigurations = [];

        foreach ($this->contentTypes as $contentTypeName => $contentType) {
            $contentfulContentType = $this->client->getContentType($contentType['content_type']);

            $contentTypeConfigurations[] = new ContentTypeConfiguration(
                $contentTypeName,
                $contentfulContentType,
                $contentType['enabled'],
                $contentType['filename_property'],
                $contentType['relative_path'],
                $contentType['additional_metadata']
            );
        }

        return $contentTypeConfigurations;
    }
}
