<?php

namespace Smrtr\SpawnPoint;

/**
 * Abstract Class AbstractController
 *
 * @package Smrtr\SpawnPoint
 * @author Joe Green
 */
abstract class AbstractController implements ControllerInterface
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @inheritDoc
     */
    public function setApp(App $app)
    {
        $this->app = $app;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequest()
    {
        return $this->app->request;
    }

    /**
     * @inheritDoc
     */
    public function getResponse()
    {
        return $this->app->response;
    }
}
