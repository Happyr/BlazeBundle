<?php

namespace Happyr\BlazeBundle\Model;

/**
 * Class ConfigurationInterface.
 *
 * @author Tobias Nyholm
 */
interface ConfigurationInterface
{
    /**
     * Does this class exist?
     *
     * @param string $class
     *
     * @return bool
     */
    public function classExist($class);

    /**
     * Does this action exists for that class?
     *
     * @param string $class
     * @param string $action
     *
     * @return mixed
     */
    public function actionExist($class, $action);

    /**
     * Get the actions for a specific class.
     *
     * @param string $class
     *
     * @return array
     */
    public function getActions($class);

    /**
     * Get the route for a class and action.
     *
     * @param string $class
     * @param string $action
     *
     * @return string
     */
    public function getRoute($class, $action);

    /**
     * Get the parameters for the route.
     *
     * @param string $class
     * @param string $action
     *
     * @return array where key=>value is routeParam=>function
     */
    public function getParameters($class, $action);

    /**
     * Get the complementary objects.
     *
     * @param string $class
     * @param string $action
     *
     * @return array
     */
    public function getComplementaryObjects($class, $action);
}
