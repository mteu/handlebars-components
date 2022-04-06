<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "handlebars_components".
 *
 * Copyright (C) 2021 Elias Häußler <e.haeussler@familie-redlich.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace Fr\Typo3HandlebarsComponents\Resource\Processing;

use Fr\Typo3HandlebarsComponents\Domain\Model\Media\MediaInterface;
use Fr\Typo3HandlebarsComponents\Exception\InvalidImageDimensionException;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * ImageProcessingInstructions
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
class ImageProcessingInstruction
{
    public const DEFAULT = 'default';
    public const SOURCE = 'source';

    /**
     * @var MediaInterface
     */
    protected $media;

    /**
     * @var string|null
     */
    protected $width;

    /**
     * @var string|null
     */
    protected $height;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $mediaQuery;

    /**
     * @var string
     */
    protected $cropVariant = 'default';

    /**
     * @param MediaInterface $media
     * @param string|int|null $width
     * @param string|int|null $height
     * @param string $type
     * @throws InvalidImageDimensionException
     */
    public function __construct(MediaInterface $media, $width = null, $height = null, string $type = self::DEFAULT)
    {
        $this->media = $media;
        $this->width = $this->parseSize($width);
        $this->height = $this->parseSize($height);
        $this->type = $type;

        $this->validate();
    }

    public function getMedia(): MediaInterface
    {
        return $this->media;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function getNormalizedWidth(): ?int
    {
        if (null === $this->width) {
            return null;
        }

        return $this->normalizeSize($this->width);
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function getNormalizedHeight(): ?int
    {
        if (null === $this->height) {
            return null;
        }

        return $this->normalizeSize($this->height);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isDefault(): bool
    {
        return self::DEFAULT === $this->type;
    }

    public function isSource(): bool
    {
        return 0 === strpos($this->type, self::SOURCE);
    }

    public function getMediaQuery(): ?string
    {
        return $this->mediaQuery;
    }

    public function setMediaQuery(string $mediaQuery): self
    {
        $this->mediaQuery = $mediaQuery;
        return $this;
    }

    public function getCropVariant(): string
    {
        return $this->cropVariant;
    }

    public function setCropVariant(string $cropVariant): self
    {
        $this->cropVariant = $cropVariant;
        return $this;
    }

    /**
     * @return array{width: string|null, height: string|null, crop: Area|null}
     */
    public function parse(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'crop' => $this->getCropArea(),
        ];
    }

    protected function getCropArea(): ?Area
    {
        $file = $this->media->getOriginalFile();

        if (!$file->hasProperty('crop')) {
            return null;
        }

        $cropString = $file->getProperty('crop');
        $cropVariantCollection = CropVariantCollection::create((string)$cropString);
        $cropArea = $cropVariantCollection->getCropArea($this->cropVariant);

        if ($cropArea->isEmpty()) {
            return null;
        }

        return $cropArea->makeAbsoluteBasedOnFile($file);
    }

    /**
     * @param string|int|null $size
     * @return string|null
     */
    protected function parseSize($size): ?string
    {
        if (null === $size) {
            return null;
        }

        if (is_int($size)) {
            return (string)$size;
        }

        // Validate given size
        if (!is_string($size)) {
            throw new \InvalidArgumentException(sprintf('Image sizes must be of type integer or string, %s given.', gettype($size)), 1631807380);
        }

        // Validate normalized size
        $normalizedSize = rtrim($size, 'cm');
        if (!MathUtility::canBeInterpretedAsInteger($normalizedSize)) {
            throw new \InvalidArgumentException('Image sizes must be integers, optionally followed by "c" or "m".', 1631807435);
        }

        return $size;
    }

    protected function normalizeSize(string $size): int
    {
        return (int)rtrim($size, 'cm');
    }

    /**
     * @throws InvalidImageDimensionException
     */
    protected function validate(): void
    {
        if (null === $this->width && null === $this->height) {
            throw InvalidImageDimensionException::forMissingDimensions();
        }
    }
}
