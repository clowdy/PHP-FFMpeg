<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FFMpeg\Format\Audio;

/**
 * The Vorbis audio format
 */
class Aac extends DefaultAudio
{
    public function __construct()
    {
        $this->audioCodec = 'libfdk_aac';
    }
	
	/**
     * {@inheritdoc}
     */
    public function getExtraParams()
    {
        return array('-cutoff', '20000');
    }
	
    /**
     * {@inheritDoc}
     */
    public function getAvailableAudioCodecs()
    {
        return array('libvo_aacenc', 'libfaac', 'libfdk_aac');
    }
}
