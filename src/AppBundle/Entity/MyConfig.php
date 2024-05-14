<?php
namespace AppBundle\Entity;

class MyConfig
{
    const APP_NAME = 'GSchool 1.0';
    const CHOSEN_TEXT = 'Sélectionner une option';

    public static function asset()
    {
        return [
            'bootstrapcss' => realpath('bootstrap/css/bootstrap.min.css'),
            'bootstrapjs' => realpath('bootstrap/js/bootstrap.min.js'),
            'jquery' => realpath('js/jquery.min.js'),
            'popper' => realpath('js/popper.min.js'),
            'style' => realpath('css/style_print.css'),
            'logo' => realpath('images/logo_ensup_300x211.jpg'),
            'img' => realpath('images'),
            'etudiant' => realpath('web/images/etudiants'),
        ];
    }

    public static function printOption()
    {
        return [
            // 'margin-top' => 20,
            // 'margin-right' => 10,
            //'margin-bottom' => 22,
            // 'margin-left' => 10,
            
            //'header-font-size' => '10',
            //'header-spacing' => '5',
            //'header-left' => '[date]',
            //'header-right' => '[page]/[toPage]',
            //'header-center' => 'Confidentiel',

            'footer-font-size' => '7',
            //'footer-spacing' => '10',
            'footer-right' => '[page]/[toPage]',
            'footer-left' => '[date] [time]',
            // 'footer-center' => 'Confidentiel',
            // 'orientation' => 'Landscape'
        ];

    }
}
