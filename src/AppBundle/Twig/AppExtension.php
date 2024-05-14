<?php

namespace AppBundle\Twig;

use Symfony\Component\Intl\Intl;

class AppExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('dateFr', [$this, 'dateFrFilter']),
            new \Twig_SimpleFilter('paysNom', [$this, 'paysNomFilter']),
            new \Twig_SimpleFilter('formatFr', [$this, 'formatFrFilter']),
            new \Twig_SimpleFilter('strToNbre', [$this, 'strToNbre']),
            new \Twig_SimpleFilter('zeroFill', [$this, 'zeroFill']),
        );
    }

    public function dateFrFilter($value, $char = '/')
    {
        return is_a($value, 'DateTime') ? $value->format("d{$char}m{$char}Y") : '';
    }

    public function paysNomFilter($code)
    {
        return Intl::getRegionBundle()->getCountryName($code);
    }

    public function formatFrFilter($value, $decimal = 0)
    {
        return number_format($value, $decimal, ',', ' ');
    }

    public function strToNbre($value)
    {
        return (float) $value;
    }

    public function zeroFill($value)
    {
        return str_pad($value, 6, '0', STR_PAD_LEFT);
    }
}
