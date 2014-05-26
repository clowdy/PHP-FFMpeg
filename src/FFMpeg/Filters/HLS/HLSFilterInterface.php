<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <dev.team@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FFMpeg\Filters\HLS;

use FFMpeg\Filters\FilterInterface;
use FFMpeg\Format\HLSInterface;
use FFMpeg\Media\HLS;

interface HLSFilterInterface extends FilterInterface
{
    /**
     * Applies the filter on the the Video media given an format.
     *
     * @param type          $media
     * @param HLSInterface $format
     *
     * @return array An array of arguments
     */
    public function apply(HLS $media, HLSInterface $format);
}
