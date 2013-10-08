<?php


namespace HappyR\BlazeBundle\Services;
use HappyR\BlazeBundle\Model\ConfigurationInterface;
use Symfony\Component\Routing\RouterInterface;


/**
 * Class BlazeService
 *
 * @author Tobias Nyholm
 *
 */
class BlazeService implements BlazeServiceInterface
{
    /**
     * @var RouterInterface router
     *
     *
     */
    protected $router;

    /**
     * @var \HappyR\BlazeBundle\Model\ConfigurationInterface config
     *
     *
     */
    protected $config;

    /**
     * @param ConfigurationInterface $config
     * @param RouterInterface $router
     */
    function __construct(ConfigurationInterface $config, RouterInterface $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * Get the route and the params.
     *
     * @param object &$entity
     * @param string $action
     *
     * @return array array($route, $params)
     * @throws \Exception
     */
    protected function getRoute(&$entity, $action)
    {
        if($entity==null){
            throw new \Exception(sprintf('Blaze: Cant find route for null object.'));
        }

        $class=get_class($entity);
        if(!$this->config->classExist($class)){
            throw new \Exception(sprintf('Class %s does not exist in Blaze config.', $class));
        }

        if(!$this->config->actionExist($class, $action)){
            throw new \Exception(sprintf('Action %s for class %s does not exist in Blaze config.', $action, $class));
        }

        $route=$this->config->getRoute($class, $action);
        $params=$this->config->getParameters($class, $action);

        return array($route, $params);
    }


    /**
     * Alias for getPath with $absolute=true
     *
     * @param object $entity
     * @param string $action
     *
     * @return string url
     */
    public function getUrl(&$entity, $action)
    {
        return $this->getPath($entity, $action, true);
    }

    /**
     * Get the path
     *
     * @param object $entity
     * @param string $action
     * @param bool $absolute if true we return the url
     *
     * @return string url
     */
    public function getPath(&$entity, $action, $absolute=false)
    {
        list($route, $params)=$this->getRoute($entity, $action);
        $routeParams=$this->getRouteParams($entity, $params);

        return $this->router->generate($route, $routeParams, $absolute);
    }

    /**
     *
     *
     * @param object &$entity
     * @param array &$params
     *
     */
    protected function getRouteParams(&$entity, array &$params)
    {
        $routeParams=array();
        foreach($params as $key=>$func){
            if(strstr($func, '.')){
                $funcs=explode('.', $func);
                $routeParams[$key]=$entity;
                foreach($funcs as $f){
                    $routeParams[$key]=$this->callEntityFunction($routeParams[$key], $f);
                }
            }
            else{
                $routeParams[$key]=$this->callEntityFunction($entity, $func);
            }

        }

        return $routeParams;
    }

    /**
     * Call a $function on the entity
     *
     * @param object &$entity
     * @param string $function
     *
     * @return mixed
     */
    private function callEntityFunction(&$entity, $function)
    {
        return $entity->$function();
    }

}