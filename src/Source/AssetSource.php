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

use Contentful\Delivery\Resource\Asset;
use Dflydev\DotAccessConfiguration\Configuration as Data;
use Sculpin\Core\Source\AbstractSource;
use Sculpin\Core\Source\DataSourceInterface;

/**
 * Source instance for a Contentful {@see Asset}.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class AssetSource extends AbstractSource
{
    /**
     * @var Asset
     */
    private $asset;

    /**
     * Creates a new AssetSource instance.
     */
    public function __construct(
        DataSourceInterface $dataSource,
        Asset $asset,
        string $assetsOutputPath,
        bool $hasChanged = false
    ) {
        $this->filename = $asset->getFile()->getFileName();
        $this->relativePathname = $assetsOutputPath.'/'.$this->filename;
        $this->sourceId = $dataSource->dataSourceId().':/'.$this->relativePathname;
        $this->asset = $asset;
        $this->isRaw = false;
        $this->hasChanged = $hasChanged;
        $this->canBeFormatted = false;
        $this->shouldBeSkipped = false;

        $this->init();
    }

    /**
     * {@inheritdoc}
     */
    protected function init(bool $hasChanged = false): void
    {
        parent::hasChanged($hasChanged);

        $originalData = $this->data;

        $this->data = new Data();
        $this->data->set('id', $this->asset->getId());
        $this->data->set('contentful:id', $this->asset->getId());
        $this->data->set('contentful:content_type', $this->asset->getFile()->getContentType());
        $this->data->set('contentful:revision', $this->asset->getSystemProperties()->getRevision());
        $this->data->set('contentful:created_at', $this->asset->getSystemProperties()->getCreatedAt()->formatForJson());
        $this->data->set('contentful:updated_at', $this->asset->getSystemProperties()->getUpdatedAt()->formatForJson());

        $this->data->set('calculated_date', strtotime($this->data->get('contentful:updated_at')));

        if ($originalData) {
            $this->data->import($originalData, false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function content(): string
    {
        $url = 'https:'.$this->asset->getFile()->getUrl();

        return file_get_contents($url);
    }
}
