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
use Contentful\Delivery\Resource\Entry;

/**
 * Configuration container for content types.
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
class ContentTypeConfiguration
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ContentTypeInterface
     */
    private $contentType;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $filenameProperty;

    /**
     * @var string
     */
    private $relativePath;

    /**
     * @var array
     */
    private $additionalMetadata;

    /**
     * Constructs a new ContentTypeConfiguration instance.
     *
     * @param string               $name               the name used in Sculpin for this content type
     * @param ContentTypeInterface $contentType        the content type defined in Contentful
     * @param bool                 $enabled            indicating if creating pages for this content type is enabled
     * @param string               $filenameProperty   the {@see Entry} property used as filename for the {@see EntrySource}
     * @param string               $relativePath       the relative path used for the {@see EntrySource}. Properties from
     *                                                 the {@see Entry} can be used for dynamic path generation. For example:
     *                                                 {{contentful:content_type}} will add the content type to the path.
     * @param array                $additionalMetadata Allows adding additional metadata to an {@see EntrySource} eg. a layout
     *                                                 property.
     */
    public function __construct(
        string $name,
        ContentTypeInterface $contentType,
        bool $enabled,
        string $filenameProperty,
        string $relativePath,
        array $additionalMetadata = []
    ) {
        $this->name = $name;
        $this->contentType = $contentType;
        $this->enabled = $enabled;
        $this->filenameProperty = $filenameProperty;
        $this->relativePath = $relativePath;
        $this->additionalMetadata = $additionalMetadata;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContentType(): ContentTypeInterface
    {
        return $this->contentType;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getFilenameProperty(): string
    {
        return $this->filenameProperty;
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function getAdditionalMetadata(): array
    {
        return $this->additionalMetadata;
    }
}
