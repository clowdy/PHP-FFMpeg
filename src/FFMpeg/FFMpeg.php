<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FFMpeg;

use Alchemy\BinaryDriver\ConfigurationInterface;
use FFMpeg\Driver\FFMpegDriver;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Exception\RuntimeException;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;
use FFMpeg\Media\HLS;
use Psr\Log\LoggerInterface;

class FFMpeg
{
    /** @var FFMpegDriver */
    private $driver;
    /** @var FFProbe */
    private $ffprobe;

    public function __construct(FFMpegDriver $ffmpeg, FFProbe $ffprobe)
    {
        $this->driver = $ffmpeg;
        $this->ffprobe = $ffprobe;
    }

    /**
     * Sets FFProbe.
     *
     * @param FFProbe
     *
     * @return FFMpeg
     */
    public function setFFProbe(FFProbe $ffprobe)
    {
        $this->ffprobe = $ffprobe;

        return $this;
    }

    /**
     * Gets FFProbe.
     *
     * @return FFProbe
     */
    public function getFFProbe()
    {
        return $this->ffprobe;
    }

    /**
     * Sets the ffmpeg driver.
     *
     * @return FFMpeg
     */
    public function setFFMpegDriver(FFMpegDriver $ffmpeg)
    {
        $this->driver = $ffmpeg;

        return $this;
    }

    /**
     * Gets the ffmpeg driver.
     *
     * @return FFMpegDriver
     */
    public function getFFMpegDriver()
    {
        return $this->driver;
    }

    /**
     * Opens a file in order to be processed.
     *
     * @param string $pathfile A pathfile
     * @param string $as overwrite the type detection return
     *                   (return video file as audio)
     *
     * @return Audio|Video
     *
     * @throws InvalidArgumentException
     */
    public function open($pathfile, $as = null)
    {
        if (null === $streams = $this->ffprobe->streams($pathfile)) {
            throw new RuntimeException(sprintf('Unable to probe "%s".', $pathfile));
        }
		
		$type = $this->detect($streams);
		$type = (is_null($as)) ? $type['type'] : $as;
		
		if ($type === 'video') {
			return new Video($pathfile, $this->driver, $this->ffprobe);
		} else if ($type === 'audio') {
			return new Audio($pathfile, $this->driver, $this->ffprobe);
		} else if ($type === 'image') {
			return new Video($pathfile, $this->driver, $this->ffprobe);
		}
		
		throw new InvalidArgumentException('Unable to detect file format, only audio and video supported');
    }
	
	public function getHls($pathfile) {
		return new HLS($pathfile, $this->driver, $this->ffprobe);
	}

    /**
     * Creates a new FFMpeg instance.
     *
     * @param array|ConfigurationInterface $configuration
     * @param LoggerInterface              $logger
     * @param FFProbe                      $probe
     *
     * @return FFMpeg
     */
    public static function create($configuration = array(), LoggerInterface $logger = null, FFProbe $probe = null)
    {
        if (null === $probe) {
            $probe = FFProbe::create($configuration, $logger, null);
        }

        return new static(FFMpegDriver::create($logger, $configuration), $probe);
    }
	
	public function detect($streams)
	{
		$hasVideoStream = false;
		$hasAudioStream = false;
		$hasImageStream = false;
		
		foreach($streams as $stream) {
			if ($stream->isVideo()) {
				$hasVideoStream = true;
			} else if ($stream->isAudio()) {
				$hasAudioStream = true;
			} else if ($stream->isImage()) {
				$hasImageStream = true;
			}
		}
		
		if ($hasVideoStream) {
			return array('type' => 'video');
		} else if ($hasAudioStream) {
			return array('type' => 'audio');
		} else if ($hasImageStream) {
			return array('type' => 'image');
		}
		
		return array('type' => 'other');
	}
}
