<?php

namespace App\Http\Libraries;

class Section
{

    public $_video;
    private $initial = 0;
    private $duration = -1;
    private $marks = [];
    private $im;
    private $id;


    public function __construct()
    {
        $this->id = $id = uniqid();
    }


    public function getImage()
    {
        return $this->im;
    }

    public function dispose()
    {
        if ($this->im)
            imagedestroy($this->im);
    }

    public function setVideo($video)
    {
        return $this->_video = $video;
    }

    public function getVideo()
    {
        return $this->_video;
    }

    public function getInitial()
    {
        return $this->initial;
    }

    public function setInitial($initial)
    {
        $this->initial = $initial;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    public function addMark($mark /* Objeto Mark*/)
    {
        $this->marks[] = $mark;
    }


    public function resizeImage($im, $dst_width, $dst_height)
    {
        $width = imagesx($im);
        $height = imagesy($im);

        $newImg = imagecreatetruecolor($dst_width, $dst_height);

        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);

        return $newImg;

    }


    public function createImage()
    {

        // Crear una nueva imagen con la dimensión completa ...
        $width = $this->_video->getWidth();
        $height = $this->_video->getHeight();


        $image_p = imagecreatetruecolor($width, $height);
        imagesavealpha($image_p, true);
        $color = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
        imagefill($image_p, 0, 0, $color);

        foreach ($this->marks as $mark) {
            $mark->createImage();
            $im = $mark->getImage();
            $width2 = imagesx($im);
            $height2 = imagesy($im);

            //  De acuerdo a la posición configurada
            // ajustar la imagen
            if ($mark->getBottom() > -1)
                $top = $height - $height2 - $mark->getBottom();
            else
                $top = max($mark->getTop(), 0);

            if ($mark->getRight() > -1)
                $left = $width - $width2 - $mark->getRight();
            else
                $left = max($mark->getLeft(), 0);


            imagecopy($image_p, $im, $left, $top, 0, 0, $width2, $height2);
            $mark->dispose();

        }
        //$this->im= $image_p;

        // Ahora justar al tamaño original del vídeo ...

        $image = $this->resizeImage($image_p, $this->_video->getOriginalWidth(), $this->_video->getOriginalHeight());
        imagedestroy($image_p);
        //$image= imagescale($image_p, $this->_video->getOriginalWidth(),$this->_video->getOriginalHeight());
        //imagedestroy($image_p);
        $this->im = $image;
    }


    public function getPath()
    {
        return $this->path;
    }

    public function execute()
    {
        $video1 = $this->getVideo();
        if (!$this->im) {
            if ($this->marks)
                $this->createImage();
        }

        $ffmpeg = \FFMpeg\FFMpeg::create(getFfmpegParams());
        $video = $ffmpeg->open($video1->getPath());


        if ($this->initial > 0 || $this->duration > 0) {
            if ($this->duration > 0) {
                $video->filters()->clip(
                    \FFMpeg\Coordinate\TimeCode::fromSeconds(max($this->initial, 0)),
                    \FFMpeg\Coordinate\TimeCode::fromSeconds($this->duration)
                );
            } else {
                $video->filters()->clip(
                    \FFMpeg\Coordinate\TimeCode::fromSeconds(max($this->initial, 0))
                );
            }
        }

        $this->resizeIfNeeded($video);

        if ($this->im) {
            $ruta = $this->saveImage();

            $video->filters()->watermark(
                $ruta, array(
                    'position' => 'relative',
                    'top' => 0,
                    'left' => 0,
                )
            );
        }

        $video->filters()->framerate(new \FFMpeg\Coordinate\FrameRate(25), 50);
        $this->path = $video1->getWorkingDir() . "/" . $this->id . ".mp4";
        $video->save($video1->getFormat(), $this->path);


    }


    public function saveImage()
    {
        $unique = uniqid();
        $file_name = $unique . '.png';
        $r = $this->_video->getWorkingDir() . "/" . $file_name;

        imagepng($this->im, $r);

        //return $r;
        return 'files/' . $this->_video->id . '/' . $file_name;
    }


    public function resizeIfNeeded($video1)
    {
        $video = $this->getVideo();

        if ($video->getWidth() != $video->getOriginalWidth() || $video->getHeight() != $video->getOriginalHeight()) {
            $video1->filters()->resize(new \FFMpeg\Coordinate\Dimension($video->getWidth(), $video->getHeight()));
        }
    }


}
