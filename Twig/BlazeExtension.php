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
     * @param mixed $entities
     * @param string $action
     * @param boolean $absolute
     *
     * @return string
     */
    public function blaze($entities, $action, $absolute=false)
    {
        //This is a fix until we support multiple entities..
        if(is_array($entities)){
            $entities=array_shift($entities);
        }

        if($absolute){
            return $this->blaze->getUrl($entities, $action);
        }
        else{
            return $this->blaze->getPath($entities, $action);
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