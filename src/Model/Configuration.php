<?php

namespace Happyr\BlazeBundle\Model;

/**
 * Class Configuration.
 *
 * @author Tobias Nyholm
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var array config
     *
     * This holds the configuration from the file
     */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get all classes in the config file.
     *
     *
     * @return array
     */
    public function getClasses()
    {
        return array_keys($this->config);
    }

    /**
     * Get the actions for a specific class.
     *
     * @param string $class
     *
     * @return array
     */
    public function getActions($class)
    {
        return array_keys($this->config[$class]);
    }

    /**
     * Get the route for a class and action.
     *
     * @param string $class
     * @param string $action
     *
     * @return string
     */
    public function getRoute($class, $action)
    {
        return $this->config[$class][$action]['route'];
    }

    /**
     * Get the parameters for the route.
     *
     * @param string $class
     * @param string $action
     *
     * @return array where key=>value is routeParam=>function
     */
    public function getParameters($class, $action)
    {
        return $this->config[$class][$action]['parameters'];
    }

    /**
     * Get the complementary objects if they exists for the route.
     *
     * @param string $class
     * @param string $action
     *
     * @return array of objects
     */
    public function getComplementaryObjects($class, $action)
    {
        if (!isset($this->config[$class][$action]['complementaryObjects'])) {
            return array();
        }

        return $this->config[$class][$action]['complementaryObjects'];
    }

    /**
     * Does this action exists for that class?
     *
     * @param string $class
     * @param string $action
     *
     * @return mixed
     */
    public function actionExist($class, $action)
    {
        return array_key_exists($action, $this->config[$class]);
    }

    /**
     * Does this class exist?
     *
     * @param string $class
     *
     * @return bool
     */
    public function classExist($class)
    {
        return array_key_exists($class, $this->config);
    }
}
