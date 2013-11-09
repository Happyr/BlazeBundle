<?php


namespace HappyR\BlazeBundle\Twig;

use HappyR\BlazeBundle\Services\BlazeServiceInterface;

/**
 * Class BlazeExtension
 *
 * @author Tobias Nyholm
 *
 */
class BlazeExtension extends \Twig_Extension
{

    /**
     * @var BlazeServiceInterface blaze
     *
     *
     */
    protected $blaze;

    /**
     * @param BlazeServiceInterface $blaze
     */
    public function __construct(BlazeServiceInterface $blaze)
    {
        $this->blaze = $blaze;
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('blaze', array($this, 'blaze')),
        );
    }

    /**
     * @inherit
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('blaze', array($this, 'blaze')),
        );
    }

    /**
     * Call the blaze service
     *
     * @param mixed $object
     * @param string $action
     * @param boolean $absolute
     *
     * @return string
     */
    public function blaze($object, $action, $absolute = false)
    {
        $compObjects = array();

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