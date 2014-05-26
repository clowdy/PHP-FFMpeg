<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FFMpeg\Format\HLS;

/**
 * The Flac audio format
 */
class Copy extends DefaultHLS
{
    public function __construct()
    {
        $this->codec = 'copy';
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableCodecs()
    {
        return array('copy');
    }

	/**
     * {@inheritDoc}
     */
	public function getExtraParams() {
		return array();
	}

}
