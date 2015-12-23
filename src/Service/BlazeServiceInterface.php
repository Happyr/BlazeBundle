<?php

namespace Happyr\BlazeBundle\Service;

/**
 * Class BlazeServiceInterface.
 *
 * @author Tobias Nyholm
 */
interface BlazeServiceInterface
{
    /**
     * Get the url.
     *
     * @param object $object
     * @param string $action
     *
     * @return string
     */
    public function getUrl($object, $action);

    /**
     * Get the path.
     *
     * @param object $object
     * @param string $action
     *
     * @return string
     */
    public function getPath($object, $action);
}
