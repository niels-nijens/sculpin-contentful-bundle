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
use Contentful\Delivery\Query;
use Contentful\Delivery\Resource\Asset;
use DateTime;
use Sculpin\Core\Source\DataSourceInterface;
use Sculpin\Core\Source\SourceSet;

/**
 * Data source for a collection of {@see Asset} instances.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
final class AssetDataSource implements DataSourceInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $assetsOutputPath;

    /**
     * @var DateTime
     */
    private $sourceUpdatedAt;

    /**
     * Creates a new AssetDataSource instance.
     */
    public function __construct(ClientInterface $client, string $assetsOutputPath)
    {
        $this->client = $client;
        $this->assetsOutputPath = $assetsOutputPath;
        $this->sourceUpdatedAt = new DateTime('1970-01-01T00:00:00Z');
    }

    /**
     * {@inheritdoc}
     */
    public function dataSourceId(): string
    {
        return 'ContentfulSource:AssetDataSource';
    }

    /**
     * Fetches new/updated {@see Asset} instances and merges them into the {@see SourceSet} as {@see AssetSource}.
     */
    public function refresh(SourceSet $sourceSet): void
    {
        $sourceUpdatedAt = $this->sourceUpdatedAt;
        $this->sourceUpdatedAt = new DateTime();

        $query = (new Query())->where('sys.updatedAt[gte]', $sourceUpdatedAt->format(DateTime::ISO8601));

        foreach ($this->client->getAssets($query) as $asset) {
            $assetSource = new AssetSource($this, $asset, $this->assetsOutputPath, true);

            $sourceSet->mergeSource($assetSource);
        }
    }
}
