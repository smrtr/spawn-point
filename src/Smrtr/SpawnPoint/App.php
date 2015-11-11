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
     * @var callable|null
     */
    public $errorHandler;

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
    public function dispatch($match)
    {
        if (is_array($match)) {

            // Check params

            if (is_array($match['params']) && count($match['params'])) {

                // Assign params to request object

                foreach ($match['params'] as $key => $value) {
                    $this->request->attributes->set($key, $value);
                }
            }

            // Extract target

            list($controller, $action) = explode("@", $match['target'], 2);
            $this->request->attributes->set('controller', $controller);
            $this->request->attributes->set('action', $action);

            // Check controller

            $reflectionClass = new \ReflectionClass($controller);

            if ($reflectionClass->implementsInterface('\Smrtr\SpawnPoint\ControllerInterface') && $reflectionClass->hasMethod($action)) {

                // Check action

                $reflectionMethod = $reflectionClass->getMethod($action);

                if ($reflectionMethod->isPublic()) {

                    // Call action

                    try {
                        $controllerObj = $reflectionClass->newInstance();
                        $controllerObj->setApp($this);
                        $reflectionMethod->invoke($controllerObj);
                    }
                    catch (\Exception $e) {

                        if ($this->errorHandler) {

                            call_user_func(
                                $this->errorHandler,
                                $e,
                                $this
                            );

                        } else {
                            $this->response->setStatusCode(500);
                        }
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
