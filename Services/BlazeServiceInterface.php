<?php


namespace HappyR\BlazeBundle\Services;


/**
 * Class BlazeServiceInterface
 *
 * @author Tobias Nyholm
 *
 */
interface BlazeServiceInterface 
{
    /**
     * Get the url
     *
     * @param object &$entity
     * @param string $action
     *
     * @return string
     */
    public function getUrl(&$entity, $action);

    /**
     * Get the path
     *
     * @param object &$entity
     * @param string $action
     *
     * @return string
     */
    public function getPath(&$entity, $action);
}