<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FFMpeg\FFProbe\DataMapping;

class Format extends AbstractData
{
	public function getDuration()
    {
        $duration = null;

        if ($this->has('duration')) {
            $duration = $this->get('duration');
        }

        return $duration;
    }
	
	public function getBitrate()
    {
		$bitrate = null;

        if ($this->has('bit_rate')) {
            $bitrate = $this->get('bit_rate');
        }

        return $bitrate;
    }
}
