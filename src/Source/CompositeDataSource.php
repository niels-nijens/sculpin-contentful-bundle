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
     * Creates a new {@see CompositeDataSource} instance.
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;

        parent::__construct([]);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(SourceSet $sourceSet): void
    {
        $dataSources = $this->dataSources();
        if (empty($dataSources)) {
            $contentTypes = $this->client->getContentTypes();

            foreach ($contentTypes as $contentType) {
                $contentTypeDataSource = new ContentTypeDataSource($contentType, $this->client);

                $this->addDataSource($contentTypeDataSource);
            }
        }

        parent::refresh($sourceSet);
    }
}
