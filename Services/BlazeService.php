<?php


namespace HappyR\BlazeBundle\Services;
use HappyR\BlazeBundle\Exception\BlazeException;
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
     * @param object &$object
     * @param string $action
     *
     * @return array array($route, $params)
     * @throws \Exception
     */
    protected function getRoute(&$object, $action)
    {
        if($object==null){
            throw new BlazeException(sprintf('Blaze: Cant find route for non-object.'));
        }

        $class=$this->getClass($object);

        if(!$this->config->actionExist($class, $action)){
            throw new BlazeException(sprintf('Action "%s" for class %s does not exist in Blaze config.', $action, $class));
        }

        $route=$this->config->getRoute($class, $action);
        $params=$this->config->getParameters($class, $action);

        return array($route, $params);
    }


    /**
     * Alias for getPath with $absolute=true
     *
     * @param object &$object
     * @param string $action
     *
     * @return string url
     */
    public function getUrl(&$object, $action)
    {
        return $this->getPath($object, $action, true);
    }

    /**
     * Get the path
     *
     * @param object &$object
     * @param string $action
     * @param bool $absolute if true we return the url
     *
     * @return string url
     */
    public function getPath(&$object, $action, $absolute=false)
    {
        list($route, $params)=$this->getRoute($object, $action);
        $routeParams=$this->getRouteParams($object, $params);

        return $this->router->generate($route, $routeParams, $absolute);
    }

    /**
     *
     *
     * @param object &$object
     * @param array &$params
     *
     * @return array
     */
    protected function getRouteParams(&$object, array &$params)
    {
        /*
         * Assert: I know for sure that $object is not null
         */
        $routeParams=array();
        foreach($params as $key=>$func){
            if(strstr($func, '.')){
                $funcs=explode('.', $func);
                $routeParams[$key]=$object;
                foreach($funcs as $f){
                    $routeParams[$key]=$this->callObjectFunction($routeParams[$key], $f);

                    if($routeParams[$key]===null){
                        throw new BlazeException(sprintf(
                            'Function "%s" ended up with returning non-object (null) after "%s".',$func, $f
                        ));
                    }
                }
            }
            else{
                $routeParams[$key]=$this->callObjectFunction($object, $func);
            }

        }

        return $routeParams;
    }

    /**
     * Call a $function on the object
     *
     * @param object &$object
     * @param string $function
     *
     * @return mixed
     */
    private function callObjectFunction(&$object, $function)
    {
        try{
            return $object->$function();
        }
        catch(\ErrorException $e){
            if($object===null){
                throw new BlazeException(sprintf('Called "%s" on a non-object', $function));
            }

            if(!method_exists($object, $function)){
                throw new BlazeException(sprintf('Method %s does not exits on object %s', $function, get_class($object)));
            }
        }

    }

    /**
     * Get the class in the config.
     * If the class of $object is not found, try the parent of $object
     *
     * @param object &$object
     *
     * @return string
     */
    protected function getClass(&$object)
    {
        if(!is_object($object)){
            //we assume that $object is a string and namespace
            if(class_exists($object)){
                return $object;
            }

            //class not loaded
            throw new BlazeException(sprintf(
                'Blaze must receive an object or a fully qualified name of a loaded class. We got "%s"', $object
            ));
        }

        $class=get_class($object);

        //Do max 3 times
        for($i=0; $i<3 && $class ; $i++){
            if($this->config->classExist($class)){
                return $class;
            }
            $class=get_parent_class($class);
        }

        throw new BlazeException(sprintf('Class %s does not exist in Blaze config.', get_class($object)));
    }

}