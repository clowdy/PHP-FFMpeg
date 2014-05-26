<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FFMpeg\Format;

interface HLSInterface extends FormatInterface
{
	/**
     * Returns the audio codec.
     *
     * @return string
     */
    public function getCodec();
	
	/**
     * Returns the list of available codecs for this format.
     *
     * @return array
     */
    public function getAvailableCodecs();
}
