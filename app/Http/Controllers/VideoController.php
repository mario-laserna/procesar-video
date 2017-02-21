<?php

namespace App\Http\Controllers;

use App\Http\Libraries\Logo;
use App\Http\Libraries\Text;
use App\Http\Libraries\Video;

class VideoController extends Controller
{
    public function index()
    {
        //$video1= "https://descargasyoutube.tk/api/youtube/descargar?url=https%3A%2F%2Fwww.youtube.com%2Fwatch%3Fv%3DfKDqAADDscw";
        $video1 = public_path() . "/test.mp4";
        $video2 = public_path() . "/final1.mp4";

        $logo = 'https://codedream.ml/img/favicon.png';
        $size = [
            'width' => 80,
            'height' => 80
        ];
        /*$sizev = [
            'width' => 1920,
            'height' => 1080
        ];*/
        $texto1 = 'Este es el texto 1';
        $texto2 = 'Este es el texto 2';

        $color1 = '0,0,0,127';
        $color2 = '0,0,0,127';
        $shadow1 = '0,0,0,30,8';
        $shadow2 = '0,0,0,30,8';

        if ($shadow1) {
            $shadow1 = explode(",", $shadow1);
            for ($i = 0; $i < count($shadow1); $i++) {
                $shadow1[$i] = $shadow1[$i] + 0;
            }
        }

        if ($shadow2) {
            $shadow2 = explode(",", $shadow2);
            for ($i = 0; $i < count($shadow2); $i++) {
                $shadow2[$i] = $shadow2[$i] + 0;
            }
        }


        if ($color1) {
            $color1 = explode(",", $color1);
            for ($i = 0; $i < count($color1); $i++) {
                $color1[$i] = $color1[$i] + 0;
            }
        }


        if ($color2) {
            $color2 = explode(",", $color2);
            for ($i = 0; $i < count($color2); $i++) {
                $color2[$i] = $color2[$i] + 0;
            }
        }


        $color3 = '255,255,255,0';
        $color4 = '255,255,255,0';

        if ($color3) {
            $color3 = explode(",", $color3);
            for ($i = 0; $i < count($color3); $i++) {
                $color3[$i] = $color3[$i] + 0;
            }
        }

        if ($color4) {
            $color4 = explode(",", $color4);
            for ($i = 0; $i < count($color4); $i++) {
                $color4[$i] = $color4[$i] + 0;
            }
        }


        $ovideo1 = new Video();
        $ovideo1->load($video1);


        $ovideo2 = new Video();
        $ovideo2->load($video2);

        $ovideofinal = new Video();
        /*if ($sizev) {
            $ovideo1->setWidth($sizev['width'] + 0);
            $ovideo1->setHeight($sizev['height'] + 0);
            $ovideo2->setWidth($sizev['width'] + 0);
            $ovideo2->setHeight($sizev['height'] + 0);
        }*/
        if($ovideo2->getWidth() > $ovideo1->getWidth()){
            $ovideo2->setWidth($ovideo1->getWidth());
            $ovideo2->setHeight($ovideo1->getHeight());
        }
        else{
            $ovideo1->setWidth($ovideo2->getWidth());
            $ovideo1->setHeight($ovideo2->getHeight());
        }


        // Crear sección con marca de texto y logo ...
        // Está fija la duración pero se puede omitir los parámetros
        // para no cortar el vídeo
        //$section1= $ovideo1->createSection(0, 30);
        $section1 = $ovideo1->createSection();
        $otext1 = new Text();
        $otext1->setContent($texto1);
        if ($color1)
            $otext1->setBackground($color1);
        if ($color3)
            $otext1->setColor($color3);
        if ($shadow1)
            $otext1->setShadow($shadow1);
        $otext1->setMarginRight($size['width']);
        $otext1->setWidth($ovideo1->getWidth());
        $otext1->setHeight(100);
        $otext1->setFontSize(20);


        $otext2 = new Text();
        $otext2->setContent($texto2);
        if ($color2)
            $otext2->setBackground($color2);
        if ($color4)
            $otext2->setColor($color4);
        if ($shadow2)
            $otext2->setShadow($shadow2);

        $otext2->setAlign("bottom left");
        $otext2->setWidth($ovideo1->getWidth());
        $otext2->setHeight(80);
        $otext2->setFontSize(18);
        $otext2->setBottom(35);
        $otext2->setLeft(20);

        $ologo = new Logo();
        $ologo->setFileName($logo);
        if ($size) {
            $ologo->setWidth($size['width'] + 0);
            $ologo->setHeight($size['width'] + 0);
        }

        //var_dump(($otext1->getHeight()- $size['height']) /2 );

        $ologo->setTop(($otext1->getHeight() - $size['height']) / 2);
        $ologo->setRight(5);

        $section1->addMark($otext1);
        $section1->addMark($otext2);
        $section1->addMark($ologo);


        $section2 = $ovideo2->createSection();

        $ovideofinal->addSection($section1);
        $ovideofinal->addSection($section2);
        $n = uniqid() . ".mp4";
        $path = public_path() . "/" . $n;
        $ovideofinal->setExportPath($path);
        $ovideofinal->execute();
        $ovideofinal->dispose();
        $ovideo1->dispose();
        $ovideo2->dispose();

        /*return [
            "id"=> $n,
            "url"=> url($n)
        ];*/

        dd([
            "id" => $n,
            "url" => url($n)
        ]);


    }
}
