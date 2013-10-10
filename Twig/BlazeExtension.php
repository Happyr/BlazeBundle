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
    function __construct(BlazeServiceInterface $blaze)
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
     * @param mixed $objects
     * @param string $action
     * @param boolean $absolute
     *
     * @return string
     */
    public function blaze($objects, $action, $absolute=false)
    {
        //This is a fix until we support multiple objects..
        if(is_array($objects)){
            $objects=array_shift($objects);
        }

        if($absolute){
            return $this->blaze->getUrl($objects, $action);
        }
        else{
            return $this->blaze->getPath($objects, $action);
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