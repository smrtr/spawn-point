<?php

namespace Smrtr\SpawnPoint;

use Smrtr\HaltoRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class App is a very basic DI container for the project.
 *
 * @package Smrtr\SpawnPoint
 * @author Joe Green
 */
class App
{
    /**
     * @var HaltoRouter
     */
    public $router;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var App
     */
    private static $instance;

    /**
     * @return App
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Performs route matching & dispatch and sends the response.
     *
     * @return $this
     */
    public function run()
    {
        // Route
        $match = $this->router->match(
            $this->request->getPathInfo(),
            $this->request->getMethod(),
            $this->request->getHttpHost(false)
        );

        // Dispatch
        $this->dispatch($match);

        // Respond
        $this->response->send();
    }

    /**
     * Takes the result of router matching and tries to call a controller action.
     * Sets response code to 404 if no valid match is found.
     *
     * @param mixed $match
     *
     * @return $this
     */
    protected function dispatch($match)
    {
        if (is_array($match)) {

            list($controller, $action) = explode("@", $match['target'], 2);

            // Check controller

            $reflectionClass = new \ReflectionClass($controller);

            if ($reflectionClass->implementsInterface('\Smrtr\SpawnPoint\ControllerInterface') && $reflectionClass->hasMethod($action)) {

                // Check action

                $reflectionMethod = $reflectionClass->getMethod($action);

                if ($reflectionMethod->isPublic()) {

                    // Call action

                    try {
                        $reflectionMethod->invoke($reflectionClass->newInstance(), $this);
                    }
                    catch (\Exception $e) {
                        $this->response->setStatusCode(500);
                    }

                    // Finished

                    return $this;
                }
            }
        }

        // 404 if action not called

        $this->response->setStatusCode(404);

        return $this;
    }
}
