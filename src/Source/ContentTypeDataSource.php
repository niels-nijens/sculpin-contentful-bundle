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

use Contentful\Core\Resource\ContentTypeInterface;
use Contentful\Delivery\Client\ClientInterface;
use Contentful\Delivery\Query;
use Contentful\Delivery\Resource\Entry;
use DateTime;
use Sculpin\Core\Source\DataSourceInterface;
use Sculpin\Core\Source\SourceSet;

/**
 * Data source for a collection of {@see Entry} instances of a certain {@see ContentTypeInterface}.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
final class ContentTypeDataSource implements DataSourceInterface
{
    /**
     * @var ContentTypeInterface
     */
    private $contentType;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var DateTime
     */
    private $sourceUpdatedAt;

    /**
     * Creates a new ContentTypeDataSource instance.
     */
    public function __construct(ContentTypeInterface $contentType, ClientInterface $client)
    {
        $this->contentType = $contentType;
        $this->client = $client;
        $this->sourceUpdatedAt = new DateTime('1970-01-01T00:00:00Z');
    }

    /**
     * {@inheritdoc}
     */
    public function dataSourceId(): string
    {
        return 'ContentfulSource:ContentTypeDataSource:'.$this->contentType->getName();
    }

    /**
     * Fetches new/updated {@see Entry} instances and merges them into the {@see SourceSet} as {@see EntrySource}.
     */
    public function refresh(SourceSet $sourceSet): void
    {
        $sourceUpdatedAt = $this->sourceUpdatedAt;
        $this->sourceUpdatedAt = new DateTime();

        $query = (new Query())->setContentType($this->contentType->getId())
            ->where('sys.updatedAt[gte]', $sourceUpdatedAt->format(DateTime::ISO8601));

        foreach ($this->client->getEntries($query) as $entry) {
            $entrySource = new EntrySource($this, $entry, true);

            $sourceSet->mergeSource($entrySource);
        }
    }
}
