<?php

declare(strict_types=1);

namespace Codeception\Module;

use Codeception\Lib\Connector\Mezzio as MezzioConnector;
use Codeception\Lib\Framework;
use Codeception\Lib\Interfaces\DoctrineProvider;
use Codeception\TestInterface;
use Interop\Container\ContainerInterface;
use Mezzio\Application;

/**
 * This module allows you to run tests inside Mezzio.
 *
 * Uses `config/container.php` file by default.
 *
 * ## Status
 *
 * * Maintainer: **Naktibalda**
 * * Stability: **alpha**
 *
 * ## Config
 *
 * * `container` - (default: `config/container.php`) relative path to file which returns Container
 * * `recreateApplicationBetweenTests` - (default: false) whether to recreate the whole application before each test
 * * `recreateApplicationBetweenRequests` - (default: false) whether to recreate the whole application before each request
 *
 * ## Public properties
 *
 * * application -  instance of `\Mezzio\Application`
 * * container - instance of `\Interop\Container\ContainerInterface`
 * * client - BrowserKit client
 *
 */
class Mezzio extends Framework implements DoctrineProvider
{
    /**
     * @var array
     */
    protected $config = [
        'container'                          => 'config/container.php',
        'recreateApplicationBetweenTests'    => true,
        'recreateApplicationBetweenRequests' => false,
    ];

    /**
     * @var \Codeception\Lib\Connector\Mezzio
     */
    public $client;

    /**
     * @deprecated Doesn't work as expected if Application is recreated between requests
     */
    public ContainerInterface $container;

    /**
     * @deprecated Doesn't work as expected if Application is recreated between requests
     */
    public Application $application;

    public function _initialize()
    {
        $this->client = new MezzioConnector();
        $this->client->setConfig($this->config);

        if ($this->config['recreateApplicationBetweenTests'] == false && $this->config['recreateApplicationBetweenRequests'] == false) {
            $this->application = $this->client->initApplication();
            $this->container   = $this->client->getContainer();
        }
    }

    public function _before(TestInterface $test)
    {
        $this->client = new MezzioConnector();
        $this->client->setConfig($this->config);

        if ($this->config['recreateApplicationBetweenTests'] != false && $this->config['recreateApplicationBetweenRequests'] == false) {
            $this->application = $this->client->initApplication();
            $this->container   = $this->client->getContainer();
        } elseif ($this->application !== null) {
            $this->client->setApplication($this->application);
        }
    }

    public function _after(TestInterface $test)
    {
        //Close the session, if any are open
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        if (isset($_SESSION)) {
            $_SESSION = [];
        }

        parent::_after($test);
    }

    public function _getEntityManager()
    {
        $service = 'Doctrine\ORM\EntityManager';
        if (!$this->container->has($service)) {
            throw new \PHPUnit\Framework\AssertionFailedError("Service $service is not available in container");
        }

        return $this->container->get('Doctrine\ORM\EntityManager');
    }
}
