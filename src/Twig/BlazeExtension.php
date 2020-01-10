<?php

namespace Happyr\BlazeBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class BlazeExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('blaze', [BlazeRuntime::class, 'blaze']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('blaze', [BlazeRuntime::class, 'blaze']),
        ];
    }

    /**
     * @inherit
     *
     * @return string
     */
    public function getName()
    {
        return 'blaze_extension';
    }
}
