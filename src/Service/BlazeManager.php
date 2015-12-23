<?php

namespace Happyr\BlazeBundle\Service;

use Happyr\BlazeBundle\Exception\BlazeException;
use Happyr\BlazeBundle\Model\ConfigurationInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class BlazeManager implements BlazeManagerInterface
{
    /**
     * @var RouterInterface router
     */
    protected $router;

    /**
     * @var \Happyr\BlazeBundle\Model\ConfigurationInterface config
     */
    protected $config;

    /**
     * @param ConfigurationInterface $config
     * @param RouterInterface        $router
     */
    public function __construct(ConfigurationInterface $config, RouterInterface $router)
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
     * @return array array($route, $params, $cmpObj)
     *
     * @throws \Exception
     */
    protected function getRoute($object, $action)
    {
        if ($object == null) {
            throw new BlazeException(sprintf('Blaze: Cant find route for non-object.'));
        }

        $class = $this->getClass($object);

        if (!$this->config->actionExist($class, $action)) {
            throw new BlazeException(
                sprintf('Action "%s" for class %s does not exist in Blaze config.', $action, $class)
            );
        }

        $route = $this->config->getRoute($class, $action);
        $params = $this->config->getParameters($class, $action);
        $cmpObj = $this->config->getComplementaryObjects($class, $action);

        return array($route, $params, $cmpObj);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($object, $action, array $cmpObj = array())
    {
        return $this->getPath($object, $action, $cmpObj, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($object, $action, array $cmpObj = array(), $absolute = false)
    {
        list($route, $params, $confCmpObj) = $this->getRoute($object, $action);

        // make sure that $confCmpObj and $cmpObj is matching
        foreach ($confCmpObj as $key => $class) {
            if (!isset($cmpObj[$key])) {
                throw new BlazeException(sprintf(
                    'The %d parameter of complementary objects was expected to be %s but you gave me nothing!',
                    $key,
                    $class
                ));
            }

            if ($this->getClass($cmpObj[$key]) != $class) {
                throw new BlazeException(sprintf(
                    'The %d parameter of complementary objects was expected to be %s but instance of %s was given',
                    $key,
                    $class,
                    get_class($cmpObj[$key])
                ));
            }
        }

        $routeParams = $this->getRouteParams($object, $params, $cmpObj);

        return $this->router->generate($route, $routeParams, $absolute);
    }

    /**
     * Get the parameters to send to the @router.
     *
     * @param object &$object
     * @param array  &$params
     * @param array  &$cmpObj
     *
     * @return array
     */
    protected function getRouteParams(&$object, array &$params, array &$cmpObj = array())
    {
        /*
         * Assert: I know for sure that $object is not null
         */
        $routeParams = array();
        foreach ($params as $key => $func) {
            //if there is complementary objects
            if (is_array($func)) {
                /**
                 * the first element should use the $object
                 * other elements should use objects in the $cmpObj.
                 */
                if ($key == 0) {
                    //make sure that the size of $params is equal to $cmpObj + the $obejct
                    if (count($params) != count($cmpObj) + 1) {
                        throw new BlazeException(sprintf(
                            'There is a mismatch in the number of route params and the number of objects. This is '.
                            'usually cased by a configuration error or that you forgotten to send the complementary'.
                            ' objects to the Blaze service. We found %s parameter arrays but %d objects',
                            count($params),
                            count($cmpObj) + 1
                        ));
                    }

                    $routeParams = array_merge($routeParams, $this->getRouteParams($object, $func));
                    continue;
                } else {
                    /*
                     * We know for sure that $cmpObj[$key-1] and is of type like the config says
                     */

                    //get the route params with the complementary object
                    $routeParams = array_merge($routeParams, $this->getRouteParams($cmpObj[$key - 1], $func));
                    continue;
                }
            }

            $routeParams[$key] = $this->getSingleRouteParam($object, $func);
        }

        return $routeParams;
    }

    /**
     * Get a route param.
     *
     * @param object &$object
     * @param string &$function
     *
     * @return mixed
     *
     * @throws \Happyr\BlazeBundle\Exception\BlazeException
     */
    protected function getSingleRouteParam(&$object, &$function)
    {
        //if there is a chain of functions
        if (strstr($function, '.')) {
            $funcs = explode('.', $function);
            $returnValue = $object;
            foreach ($funcs as $f) {
                $returnValue = $this->callObjectFunction($returnValue, $f);

                if ($returnValue === null) {
                    throw new BlazeException(sprintf(
                        'Function "%s" ended up with returning non-object (null) after "%s".',
                        $function,
                        $f
                    ));
                }
            }

            return $returnValue;
        } else {
            return $this->callObjectFunction($object, $function);
        }
    }

    /**
     * Call a $function on the object.
     *
     * @param object &$object
     * @param string $function
     *
     * @return mixed
     */
    private function callObjectFunction(&$object, $function)
    {
        try {
            return $object->$function();
        } catch (\Exception $e) {
            if ($object === null) {
                throw new BlazeException(sprintf('Called "%s" on a non-object', $function));
            }

            if (!method_exists($object, $function)) {
                throw new BlazeException(
                    sprintf('Method %s does not exits on object %s', $function, get_class($object))
                );
            }
        }
    }

    /**
     * Get the class in the config.
     * If the class of $object is not found, try the parent of $object.
     *
     * @param object &$object
     *
     * @return string
     */
    protected function getClass(&$object)
    {
        if (!is_object($object)) {
            //we assume that $object is a string and namespace
            if (class_exists($object)) {
                return $object;
            }

            //class not loaded
            throw new BlazeException(sprintf(
                'Blaze must receive an object or a fully qualified name of a loaded class. We got "%s"',
                $object
            ));
        }

        $class = get_class($object);

        //Do max 3 times
        for ($i = 0; $i < 3 && $class; ++$i) {
            if ($this->config->classExist($class)) {
                return $class;
            }
            $class = get_parent_class($class);
        }

        throw new BlazeException(sprintf('Class %s does not exist in Blaze config.', get_class($object)));
    }
}
