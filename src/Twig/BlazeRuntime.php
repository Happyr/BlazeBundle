<?php

namespace Happyr\BlazeBundle\Twig;

use Happyr\BlazeBundle\Service\BlazeManagerInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class BlazeRuntime implements RuntimeExtensionInterface
{
    /**
     * @var BlazeManagerInterface blaze
     */
    protected $blaze;

    /**
     * @param BlazeManagerInterface $blaze
     */
    public function __construct(BlazeManagerInterface $blaze)
    {
        $this->blaze = $blaze;
    }

    /**
     * Call the blaze service.
     *
     * @param mixed  $object
     * @param string $action
     * @param bool   $absolute
     *
     * @return string
     */
    public function blaze($object, $action, $absolute = false)
    {
        $compObjects = [];

        if (is_array($object)) {
            $compObjects = $object;
            $object = array_shift($compObjects);
        }

        if ($absolute) {
            return $this->blaze->getUrl($object, $action, $compObjects);
        } else {
            return $this->blaze->getPath($object, $action, $compObjects);
        }
    }
}
