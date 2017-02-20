<?php

namespace App\Http\Libraries;

class Text
{
    private $text = "";
    private $fontsize = 18;
    private $font = "Ubuntu-B.ttf";
    private $align = "center middle";
    private $top = -1;
    private $left = -1;
    private $shadow;
    private $right = -1;
    private $bottom = -1;
    private $width = 600;
    private $height = 100;
    private $marginright = 0;
    private $im;
    /** @var array */
    private $background = [229, 237, 247, 50];
    /** @var array */
    private $color = [80, 80, 80, 0];

    public function getImage()
    {
        return $this->im;
    }

    public function dispose()
    {
        imagedestroy($this->im);
    }


    public function getContent()
    {
        return $this->content;
    }

    public function setContent($text)
    {
        return $this->content = $text;
    }

    public function getMarginRight()
    {
        return $this->marginright;
    }

    public function setMarginRight($margin)
    {
        return $this->marginright = $margin;
    }

    public function getFontSize()
    {
        return $this->fontsize;
    }

    public function setFontSize($size)
    {
        return $this->fontsize = $size;
    }

    public function getFont()
    {
        return $this->font;
    }

    public function setFont($text)
    {
        return $this->font = $text;
    }

    public function getAlign()
    {
        return $this->align;
    }

    public function setAlign($align)
    {
        return $this->align = $align;
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

    public function getBackground()
    {
        return $this->background;
    }

    public function setBackground($color)
    {
        return $this->background = $color;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function setColor($color)
    {
        return $this->color = $color;
    }

    public function getShadow()
    {
        return $this->shadow;
    }

    public function setShadow($shadow)
    {
        return $this->shadow = $shadow;
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


    public function getAlignCoord($width, $height, $tbox, $tboxprev)
    {
        $align = $this->align;
        $options = explode(" ", $align);

        if (in_array("center", $options)) {
            $px = ($width - $tbox[2]) / 2;
        } else if (in_array("right", $options)) {
            $px = $width - $tbox[2];
        } else {
            $px = 0;
        }

        if (in_array("middle", $options)) {
            $px2 = ($height - $tbox[1] + ($tboxprev ? $tboxprev[1] : 0)) / 2;
        } else if (in_array("bottom", $options)) {
            $px2 = ($height - $tbox[1] + ($tboxprev ? $tboxprev[1] : 0));
        } else {
            $px2 = 0;
        }
        return [
            "x" => $px,
            "y" => $px2
        ];

    }

    protected function fontPath()
    {
        //return dirname(__FILE__). "/../Resources/fonts";
        return resource_path('fonts');
    }


    public function createImage()
    {


        $width = $this->width;
        $theight = $this->height;

        $cadena = $this->getContent();
        $parts = explode("\n", $cadena);

        $bg = $this->background;
        $co = $this->color;

        $im = imagecreatetruecolor($width, $theight);
        imagesavealpha($im, true);

        $naranja = imagecolorallocatealpha($im, $bg[0], $bg[1], $bg[2], $bg[3]);
        imagefill($im, 0, 0, $naranja);


        $naranja = imagecolorallocatealpha($im, $co[0], $co[1], $co[2], $co[3]);


        // Width logo en caso sea necesario restar ..
        $width = imagesx($im);
        $height = imagesy($im);
        if ($this->marginright) {
            $width -= $this->marginright;
        }

        $multiplicator = 1;
        $tb = imagettfbbox($this->fontsize, 0, $this->fontPath() . "/" . $this->font, $cadena);
        $tb2 = null;
        $tb1 = imagettfbbox($this->fontsize * $multiplicator, 0, $this->fontPath() . "/" . $this->font, $parts[0]);
        if (isset($parts[1]))
            $tb2 = imagettfbbox($this->fontsize * $multiplicator, 0, $this->fontPath() . "/" . $this->font, $parts[1]);


        $pos = $this->getAlignCoord($width, $height, $tb, null);
        $px = $pos['x'];
        $px2 = $pos['y'];


        if ($this->shadow) {
            $sh = imagecolorallocatealpha($im, $this->shadow[0], $this->shadow[1], $this->shadow[2], $this->shadow[3]);
            \imagettftextblur($im, $this->fontsize, 0, $px, $px2, $sh, $this->fontPath() . "/" . $this->font, $parts[0], $this->shadow[4]);
        }
        imagettftext($im, $this->fontsize, 0, $px, $px2, $naranja, $this->fontPath() . "/" . $this->font, $parts[0]);
        if ($tb2) {

            $pos = $this->getAlignCoord($width, $height, $tb, null);
            $px = $pos['x'];
            $px2 = $pos['y'];


            if ($this->shadow) {
                $sh = imagecolorallocatealpha($im, $this->shadow[0], $this->shadow[1], $this->shadow[2], $this->shadow[3]);
                \imagettftextblur($im, $this->fontsize, 0, $px, $px2, $sh, $this->fontPath() . "/" . $this->font, $parts[1], $this->shadow[4]);
            }
            imagettftext($im, $this->fontsize, 0, $px, $px2, $naranja, $this->fontPath() . "/" . $this->font, $parts[1]);
        }


        $this->im = $im;
    }


}
