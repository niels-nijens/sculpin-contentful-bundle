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

use Contentful\Delivery\Resource\Entry;
use Dflydev\DotAccessConfiguration\Configuration as Data;
use Sculpin\Core\Source\AbstractSource;
use Sculpin\Core\Source\DataSourceInterface;

/**
 * Source instance for a Contentful {@see Entry}.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
final class EntrySource extends AbstractSource
{
    /**
     * @var Entry
     */
    private $entry;

    /**
     * Creates a new EntrySource instance.
     */
    public function __construct(DataSourceInterface $dataSource, Entry $entry, bool $hasChanged = false)
    {
        $this->sourceId = $dataSource->dataSourceId().':'.$entry->getId();
        $this->relativePathname = str_replace(':', '_', $dataSource->dataSourceId()).'/'.$entry->getId();
        $this->filename = $entry->getId();
        $this->entry = $entry;
        $this->isRaw = false;
        $this->hasChanged = $hasChanged;
        $this->canBeFormatted = true;
        $this->content = '';

        $this->init();
    }

    /**
     * {@inheritdoc}
     */
    protected function init(bool $hasChanged = false): void
    {
        parent::hasChanged($hasChanged);

        $originalData = $this->data;

        $this->data = new Data($this->entry->all(null, false));

        $this->data->set('id', $this->entry->getId());
        $this->data->set('contentful:id', $this->entry->getId());
        $this->data->set('contentful:content_type', $this->entry->getContentType()->getName());
        $this->data->set('contentful:revision', $this->entry->getSystemProperties()->getRevision());
        $this->data->set('contentful:created_at', $this->entry->getSystemProperties()->getCreatedAt()->formatForJson());
        $this->data->set('contentful:updated_at', $this->entry->getSystemProperties()->getUpdatedAt()->formatForJson());

        $this->data->set('calculated_date', strtotime($this->data->get('contentful:updated_at')));

        if ($originalData) {
            $this->data->import($originalData, false);
        }
    }
}
