<?php

namespace FFMpeg\Filters\Audio;

use FFMpeg\Media\Audio;
use FFMpeg\Filters\Audio\AudioResamplableFilter;
use FFMpeg\Filters\Audio\AudioNoVideoFilter;

class AudioFilters
{
    protected $media;

    public function __construct(Audio $media)
    {
        $this->media = $media;
    }

    /**
     * Resamples the audio file.
     *
     * @param Integer $rate
     *
     * @return AudioFilters
     */
    public function resample($rate)
    {
        $this->media->addFilter(new AudioResamplableFilter($rate));

        return $this;
    }
	
	/**
     * Remove any video from the audio file.
     *
     * @return AudioFilters
     */
    public function novideo()
    {
        $this->media->addFilter(new AudioNoVideoFilter());

        return $this;
    }
}
