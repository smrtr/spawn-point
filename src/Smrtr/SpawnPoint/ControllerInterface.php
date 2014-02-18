<?php

namespace Smrtr\SpawnPoint;

/**
 * Interface ControllerInterface
 *
 * @package Smrtr\SpawnPoint
 * @author Joe Green
 */
interface ControllerInterface
{
    /**
     * Bind the App to the controller.
     *
     * @param App $app
     *
     * @return $this
     */
    public function setApp(App $app);

    /**
     * Get the request object.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest();

    /**
     * Get the response object.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse();

    /**
     * Get a value from the matched route parameters.
     *
     * @param string $key
     * @param mixed $default Optional
     *
     * @return mixed
     */
    public function getRoutedParam($key, $default = null);
}
