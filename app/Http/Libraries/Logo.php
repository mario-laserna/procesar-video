<?php

namespace App\Http\Libraries;

class Logo
{
    private $top = -1;
    private $left = -1;
    private $right = -1;
    private $bottom = -1;
    private $width;
    private $height;

    private $filename;
    //private $marginright=0;
    private $im;

    public function getImage()
    {
        return $this->im;
    }

    public function dispose()
    {
        imagedestroy($this->im);
    }


    public function getTop()
    {
        return $this->top;
    }

    public function setTop($top)
    {
        return $this->top = $top;
    }

    public function getLeft()
    {
        return $this->left;
    }

    public function setLeft($left)
    {
        return $this->left = $left;
    }

    public function getBottom()
    {
        return $this->bottom;
    }

    public function setBottom($bottom)
    {
        return $this->bottom = $bottom;
    }

    public function getRight()
    {
        return $this->right;
    }

    public function setRight($right)
    {
        return $this->right = $right;
    }

    public function getFileName()
    {
        return $this->filename;
    }

    public function setFileName($filename)
    {
        return $this->filename = $filename;
    }


    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        return $this->width = $width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        return $this->height = $height;
    }


    public function resizeImage($image, $max_width, $max_height)
    {
        //list($orig_width, $orig_height) = getimagesize($filename);

        $orig_width = imagesx($image);
        $orig_height = imagesy($image);


        $width = $orig_width;
        $height = $orig_height;

        # taller
        if ($height > $max_height) {
            $width = ($max_height / $height) * $width;
            $height = $max_height;
        }

        # wider
        if ($width > $max_width) {
            $height = ($max_width / $width) * $height;
            $width = $max_width;
        }

        $image_p = imagecreatetruecolor($width, $height);
        imagesavealpha($image_p, true);
        $color = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
        imagefill($image_p, 0, 0, $color);

        //$image = imagecreatefrompng($filename);

        imagecopyresampled($image_p, $image, 0, 0, 0, 0,
            $width, $height, $orig_width, $orig_height);


        return $image_p;
    }


    public function createImage()
    {

        $im = imagecreatefrompng($this->filename);
        if ($this->getWidth() > 0 && $this->getHeight() > 0) {

            $image = $this->resizeImage($im, $this->getWidth(), $this->getHeight());
            imagedestroy($im);
            $im = $image;
        }
        $this->im = $im;

    }


}
