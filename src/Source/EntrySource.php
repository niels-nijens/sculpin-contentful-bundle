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
class EntrySource extends AbstractSource
{
    /**
     * @var Entry
     */
    private $entry;

    /**
     * Creates a new ContentfulEntrySource instance.
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

        $this->data = new Data($this->entry->all());
        $this->data->set('contentful_type', $this->entry->getContentType()->getName());

        if ($originalData) {
            $this->data->import($originalData, false);
        }
    }
}
