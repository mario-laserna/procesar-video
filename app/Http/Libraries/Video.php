<?php

namespace App\Http\Libraries;

class Video
{

    private $originalwidth;
    private $originalheight;
    private $width;
    private $height;
    private $path;
    public $id;
    private $exportPath;
    private $sections = [];
    private $tmpdir;


    public function __construct()
    {

        $this->id = $id = uniqid();

        /*$tmpdir= sys_get_temp_dir();
        $this->tmpdir= $tmpdir."/$id";*/

        $this->tmpdir = public_path('files') . '/' . $id;

        if (!file_exists($this->tmpdir))
            mkdir($this->tmpdir);


    }

    public function load($file)
    {
        //$this->initialVideo= $videoUri;
        // En caso de ser un archivo local igualmente funcionaría ...
        $n = uniqid();
        $this->path = $this->tmpdir . "/" . $n . '.mp4';
        file_put_contents($this->path, fopen($file, 'r'));

        $ffprobe = \FFMpeg\FFProbe::create(getFfmpegParams());
        $dimension1 = $ffprobe
            ->streams($this->path)// extracts streams informations
            ->videos()// filters video streams
            ->first()// returns the first video stream
            ->getDimensions();


        $this->width = $this->originalwidth = $dimension1->getWidth();
        $this->height = $this->originalheight = $dimension1->getHeight();

    }


    public function getFormat()
    {
        //$format = new \FFMpeg\Format\Video\X264();
        $format = new \FFMpeg\Format\Video\X264('libmp3lame', 'libx264');
        $e = $this;/*
		$format->on('progress', function($a,$b,$c) use ($e){
			$e->progress($a,$b,$c);
		});*/
        $format->setAdditionalParameters(array('-preset', 'fast'));
        //$format->setAudioCodec("aac");
        $format->setAudioKiloBitrate(128);
        $format->setKiloBitrate(800);
        return $format;
    }


    public function dispose()
    {
        // Eliminar el directorio temporal ...
        $this->eliminarDir($this->tmpdir);
        foreach ($this->sections as $section) {
            $section->dispose();
        }
    }


    public function eliminarDir($carpeta)
    {
        foreach (glob($carpeta . "/*") as $archivos_carpeta) {
            //si es un directorio volvemos a llamar recursivamente
            if (is_dir($archivos_carpeta))
                $this->eliminarDir($archivos_carpeta);
            else//si es un archivo lo eliminamos
                unlink($archivos_carpeta);
        }
        if (file_exists($carpeta))
            rmdir($carpeta);
    }


    public function getPath()
    {
        return $this->path;
    }

    public function getExportPath()
    {
        return $this->exportPath;
    }

    public function setExportPath($path)
    {
        return $this->exportPath = $path;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getOriginalWidth()
    {
        return $this->originalwidth;
    }

    public function getOriginalHeight()
    {
        return $this->originalheight;
    }

    public function setWidth($width)
    {
        return $this->width = $width;
    }

    public function setHeight($height)
    {
        return $this->height = $height;
    }

    public function getWorkingDir()
    {
        return $this->tmpdir;
    }

    public function createSection($initial = null, $duration = null)
    {
        $e = new Section();
        if ($initial != null)
            $e->setInitial($initial);

        if ($duration != null)
            $e->setDuration($duration);

        $e->setVideo($this);
        return $e;
    }


    public function addSection($section)
    {
        $this->sections[] = $section;
    }


    public function execute()
    {
        $vids = [];
        foreach ($this->sections as $section) {
            $section->execute();
            $vids[] = $section->getPath();
        }

        // Concatenar todos los vídeos ..
        if ($vids) {
            $ffmpeg = \FFMpeg\FFMpeg::create(getFfmpegParams());
            $video = $ffmpeg->open($vids[0]);
            $video
                ->concat($vids)
                ->saveFromSameCodecs($this->exportPath, TRUE);
        }

    }


}
