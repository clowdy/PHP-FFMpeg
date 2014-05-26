<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FFMpeg\Media;

use Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use FFMpeg\Filters\HLS\HLSFilters;
use FFMpeg\Filters\HLS\SimpleFilter;
use FFMpeg\Format\FormatInterface;
use FFMpeg\Exception\RuntimeException;
use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Filters\Audio\AudioFilterInterface;
use FFMpeg\Filters\FilterInterface;
use FFMpeg\Format\ProgressableInterface;
use FFMpeg\Format\HLSInterface;

class HLS extends AbstractStreamableMedia
{
    /**
     * {@inheritdoc}
     *
     * @return HLSFilters
     */
    public function filters()
    {
        return new HLSFilters($this);
    }

    /**
     * {@inheritdoc}
     *
     * @return Audio
     */
    public function addFilter(FilterInterface $filter)
    {
        if (!$filter instanceof AudioFilterInterface) {
            throw new InvalidArgumentException('Audio only accepts AudioFilterInterface filters');
        }

        $this->filters->add($filter);

        return $this;
    }

    /**
     * Exports the audio in the desired format, applies registered filters.
     *
     * @param FormatInterface $format
     * @param string          $outputPathfile
     *
     * @return Audio
     *
     * @throws RuntimeException
     */
    public function save(FormatInterface $format, $outputPathfile)
    {
        $listeners = null;

        if ($format instanceof ProgressableInterface) {
            $listeners = $format->createProgressListener($this, $this->ffprobe, 1, 1);
        }

        $commands = array('-y', '-i', $this->pathfile);

        $filters = clone $this->filters;
		$filters->add(new SimpleFilter($format->getExtraParams(), 10));
		
		if ($format instanceOf HLSInterface) {
            if (null !== $format->getCodec()) {
                $filters->add(new SimpleFilter(array('-codec', $format->getCodec())));
            }
        }
		
		foreach ($filters as $filter) {
            $commands = array_merge($commands, $filter->apply($this, $format));
        }

		$commands[] = '-map';
		$commands[] = '0';
		$commands[] = '-vbsf';
		$commands[] = 'h264_mp4toannexb';
		$commands[] = '-f';
		$commands[] = 'segment';
		
		$commands[] = '-segment_time';
		$commands[] = $format->getSegmentTime();
		$commands[] = '-segment_list_type';
		$commands[] = $format->getSegmentListType();
		
		$output = pathinfo($outputPathfile);
		$dir = $output['dirname'];
		if (!file_exists($dir)) {
			mkdir($dir);
		}
		$segmentList = $dir . '/' . $output['filename'] . '.m3u8';
		$segments = $dir . '/' . $output['filename'] . '_%d.ts';
		
		$commands[] = '-segment_list';
		$commands[] = $segmentList;
		$commands[] = '-segment_list_flags';
		$commands[] = $format->getSegmentListFlags();
		$commands[] = '-segment_format';
		$commands[] = $format->getSegmentFormat();
		$commands[] = $segments;

        try {
            $this->driver->command($commands, false, $listeners);
        } catch (ExecutionFailureException $e) {
            $this->cleanupTemporaryFile($outputPathfile);
            throw new RuntimeException('Encoding failed', $e->getCode(), $e);
        }

        return $this;
    }
}
