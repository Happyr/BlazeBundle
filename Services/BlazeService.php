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
            throw new \Exception(sprintf('Blaze: Cant find route for non-object.'));
        }

        $class=$this->getClass($entity);

        if(!$this->config->actionExist($class, $action)){
            throw new \Exception(sprintf('Action "%s" for class %s does not exist in Blaze config.', $action, $class));
        }

        $route=$this->config->getRoute($class, $action);
        $params=$this->config->getParameters($class, $action);

        return array($route, $params);
    }


    /**
     * Alias for getPath with $absolute=true
     *
     * @param object &$entity
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
     * @param object &$entity
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
     * @return array
     */
    protected function getRouteParams(&$entity, array &$params)
    {
        /*
         * Assert: I know for sure that $entity is not null
         */
        $routeParams=array();
        foreach($params as $key=>$func){
            if(strstr($func, '.')){
                $funcs=explode('.', $func);
                $routeParams[$key]=$entity;
                foreach($funcs as $f){
                    $routeParams[$key]=$this->callEntityFunction($routeParams[$key], $f);

                    if($routeParams[$key]===null){
                        throw new \Exception(sprintf(
                            'Function "%s" ended up with returning non-object (null) after "%s".',$func, $f
                        ));
                    }
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
        try{
            return $entity->$function();
        }
        catch(\ErrorException $e){
            if(!method_exists($entity, $function)){
                throw new \Exception(sprintf('Method %s does not exits on object %s', $function, get_class($entity)));
            }
        }

    }

    /**
     * Get the class in the config.
     * If the class of $entity is not found, try the parent of $entity
     *
     * @param object &$entity
     *
     * @return string
     */
    protected function getClass(&$entity)
    {
        if(!is_object($entity)){
            //we assume that $entity is a string and namespace
            if(class_exists($entity)){
                return $entity;
            }

            //class not loaded
            throw new \Exception(sprintf(
                'Blaze must receive an object or a fully qualified name of a loaded class. We got "%s"', $entity
            ));
        }
       
        $class=get_class($entity);

        //Do max 3 times
        for($i=0; $i<3 && $class ; $i++){
            if($this->config->classExist($class)){
                return $class;
            }
            $class=get_parent_class($class);
        }

        throw new \Exception(sprintf('Class %s does not exist in Blaze config.', get_class($entity)));
    }

}