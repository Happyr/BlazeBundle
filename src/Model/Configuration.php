<?php

namespace Happyr\BlazeBundle\Model;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
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
     * {@inheritdoc}
     */
    public function getClasses()
    {
        return array_keys($this->config);
    }

    /**
     * {@inheritdoc}
     */
    public function getActions($class)
    {
        return array_keys($this->config[$class]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute($class, $action)
    {
        return $this->config[$class][$action]['route'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters($class, $action)
    {
        return $this->config[$class][$action]['parameters'];
    }

    /**
     * {@inheritdoc}
     */
    public function getComplementaryObjects($class, $action)
    {
        if (!isset($this->config[$class][$action]['complementaryObjects'])) {
            return [];
        }

        return $this->config[$class][$action]['complementaryObjects'];
    }

    /**
     * {@inheritdoc}
     */
    public function actionExist($class, $action)
    {
        return array_key_exists($action, $this->config[$class]);
    }

    /**
     * {@inheritdoc}
     */
    public function classExist($class)
    {
        return array_key_exists($class, $this->config);
    }
}
