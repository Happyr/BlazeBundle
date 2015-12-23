<?php

namespace Happyr\BlazeBundle\Service;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface BlazeManagerInterface
{
    /**
     * Alias for getPath with $absolute=true.
     *
     * @param object $object
     * @param string $action
     * @param array  $cmpObj
     *
     * @return string url
     */
    public function getUrl($object, $action, array $cmpObj = array());

    /**
     * Get the path.
     *
     * @param object $object
     * @param string $action
     * @param array  $cmpObj   complementary objects that might be needed to generate the route
     * @param bool   $absolute if true we return the url
     *
     * @return string url
     */
    public function getPath($object, $action, array $cmpObj = array(), $absolute = false);
}
