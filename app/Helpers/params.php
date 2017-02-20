<?php

function getFfmpegParams()
{
    return $params = [
        'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
        'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
        'timeout'          => 0, // The timeout for the underlying process
        'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
    ];
}