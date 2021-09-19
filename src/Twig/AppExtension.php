<?php

namespace App\Twig;

use Symfony\Component\String\Inflector\EnglishInflector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{


    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pluralize', [$this, 'doSomething']),
            new TwigFunction('pluralize_a', [$this, 'pluralizep']),
            new TwigFunction('singularize', [$this, 'singularize']),
        ];
    }

    public function doSomething(int $count , string $single )
    {
      $infector = new EnglishInflector();

        $str = $count === 1? $single :$infector->pluralize($single)[0];
        return "$count  $str";
    }
    public function pluralizep(int $count , string $single )
    {
        $infector = new EnglishInflector();

        $str = $count === 1? $single :$infector->pluralize($single)[0];
        return "$str";
    }
    public function singularize(int $count , string $single )
    {
        $infector = new EnglishInflector();

        $str = $count === 1? $single :$infector->singularize($single)[0];
        return "$str";
    }
}
