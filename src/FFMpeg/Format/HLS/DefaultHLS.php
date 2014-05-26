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

use Evenement\EventEmitter;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Format\HLSInterface;
use FFMpeg\Media\MediaTypeInterface;
use FFMpeg\Format\ProgressableInterface;
use FFMpeg\Format\ProgressListener\HLSProgressListener;
use FFMpeg\FFProbe;

abstract class DefaultHLS extends EventEmitter implements HLSInterface, ProgressableInterface
{
    /** @var string */
    protected $codec;
	
	protected $segmentTime = 10;
	
	protected $segmentListType = 'm3u8';
	
	protected $segmentListFlags = '+live';
	
	protected $segmentFormat = 'mpegts';

    /**
     * {@inheritdoc}
     */
    public function getCodec()
    {
        return $this->codec;
    }

    /**
     * Sets the audio codec, Should be in the available ones, otherwise an
     * exception is thrown.
     *
     * @param string $codec
     *
     * @throws InvalidArgumentException
     */
    public function setCodec($codec)
    {
        if ( ! in_array($codec, $this->getAvailableCodecs())) {
            throw new InvalidArgumentException(sprintf(
                    'Wrong audiocodec value for %s, available formats are %s'
                    , $codec, implode(', ', $this->getAvailableAudioCodecs())
            ));
        }

        $this->codec = $codec;

        return $this;
    }
	
	/**
     * {@inheritdoc}
     */
    public function getExtraParams()
    {
        return array();
    }
	
	/**
     * {@inheritdoc}
     */
    public function getSegmentTime()
    {
        return $this->segmentTime;
    }
	
	/**
     * {@inheritdoc}
     */
    public function getSegmentListType()
    {
        return $this->segmentListType;
    }
	
	/**
     * {@inheritdoc}
     */
    public function getSegmentListFlags()
    {
        return $this->segmentListFlags;
    }
	
	/**
     * {@inheritdoc}
     */
    public function getSegmentFormat()
    {
        return $this->segmentFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function createProgressListener(MediaTypeInterface $media, FFProbe $ffprobe, $pass, $total)
    {
        $format = $this;
        $listener = new HLSProgressListener($ffprobe, $media->getPathfile(), $pass, $total);
        $listener->on('progress', function () use ($media, $format) {
           $format->emit('progress', array_merge(array($media, $format), func_get_args()));
        });

        return array($listener);
    }

    /**
     * {@inheritDoc}
     */
    public function getPasses()
    {
        return 1;
    }
}
