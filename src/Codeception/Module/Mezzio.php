<?php
namespace Codeception\Module;

use Codeception\Lib\Framework;
use Codeception\TestInterface;
use Codeception\Lib\Connector\Mezzio as MezzioConnector;
use Codeception\Lib\Interfaces\DoctrineProvider;

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
     * @var \Interop\Container\ContainerInterface
     * @deprecated Doesn't work as expected if Application is recreated between requests
     */
    public $container;

    /**
     * @var \Mezzio\Application
     * @deprecated Doesn't work as expected if Application is recreated between requests
     */
    public $application;

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
        } elseif (isset($this->application)) {
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
