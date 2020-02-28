<?php
namespace Codeception\Lib\Connector;

use Codeception\Configuration;
use Codeception\Lib\Connector\Mezzio\ResponseCollector;
use Symfony\Component\BrowserKit\AbstractBrowser as Client;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Application;
use Laminas\Diactoros\UploadedFile;

class Mezzio extends Client
{

    /**
     * @var Application
     */
    private $application;
    /**
     * @var ResponseCollector
     */
    private $responseCollector;

    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * @var array Configuration of the module
     */
    private $config;

    /**
     * @param BrowserKitRequest $request
     *
     * @return Response
     * @throws \Exception
     */
    public function doRequest($request)
    {
        $inputStream = fopen('php://memory', 'r+');
        $content = $request->getContent();
        if ($content !== null) {
            fwrite($inputStream, $content);
            rewind($inputStream);
        }

        $queryParams = [];
        $postParams = [];
        $queryString = parse_url($request->getUri(), PHP_URL_QUERY);
        if ($queryString != '') {
            parse_str($queryString, $queryParams);
        }
        if ($request->getMethod() !== 'GET') {
            $postParams = $request->getParameters();
        }

        $serverParams = $request->getServer();
        if (!isset($serverParams['SCRIPT_NAME'])) {
            //required by WhoopsErrorHandler
            $serverParams['SCRIPT_NAME'] = 'Codeception';
        }
        
        $cookies = $request->getCookies();
        $headers = $this->extractHeaders($request);

        //set cookie header because dflydev/fig-cookies reads cookies from header
        if (!empty($cookies)) {
            $headers['cookie'] = implode(';', array_map(function ($key, $value) {
                return "$key=$value";
            }, array_keys($cookies), $cookies));
        }

        $mezzioRequest = new ServerRequest(
            $serverParams,
            $this->convertFiles($request->getFiles()),
            $request->getUri(),
            $request->getMethod(),
            $inputStream,
            $headers,
            $cookies,
            $queryParams,
            $postParams
        );

        $this->request = $mezzioRequest;

        $cwd = getcwd();
        chdir(codecept_root_dir());

        if ($this->config['recreateApplicationBetweenRequests'] === true || $this->application === null) {
            $application = $this->initApplication();
        } else {
            $application = $this->application;
        }

        if (method_exists($application, 'handle')) {
            // Mezzio v3
            $response = $application->handle($mezzioRequest);
        } else {
            //Older versions
            $application->run($mezzioRequest);
            $response = $this->responseCollector->getResponse();
            $this->responseCollector->clearResponse();
        }

        chdir($cwd);

        return new Response(
            (string)$response->getBody(),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }

    private function convertFiles(array $files)
    {
        $fileObjects = [];
        foreach ($files as $fieldName => $file) {
            if ($file instanceof UploadedFile) {
                $fileObjects[$fieldName] = $file;
            } elseif (!isset($file['tmp_name']) && !isset($file['name'])) {
                $fileObjects[$fieldName] = $this->convertFiles($file);
            } else {
                $fileObjects[$fieldName] = new UploadedFile(
                    $file['tmp_name'],
                    $file['size'],
                    $file['error'],
                    $file['name'],
                    $file['type']
                );
            }
        }
        return $fileObjects;
    }

    private function extractHeaders(BrowserKitRequest $request)
    {
        $headers = [];
        $server = $request->getServer();

        $contentHeaders = ['Content-Length' => true, 'Content-Md5' => true, 'Content-Type' => true];
        foreach ($server as $header => $val) {
            $header = html_entity_decode(implode('-', array_map('ucfirst', explode('-', strtolower(str_replace('_', '-', $header))))), ENT_NOQUOTES);

            if (strpos($header, 'Http-') === 0) {
                $headers[substr($header, 5)] = $val;
            } elseif (isset($contentHeaders[$header])) {
                $headers[$header] = $val;
            }
        }

        return $headers;
    }

    public function initApplication()
    {
        $cwd = getcwd();
        $projectDir = Configuration::projectDir();
        chdir($projectDir);
        $this->container = require $projectDir . $this->config['container'];
        $app = $this->container->get(\Mezzio\Application::class);

        $middlewareFactory = null;
        if ($this->container->has(\Mezzio\MiddlewareFactory::class)) {
            $middlewareFactory = $this->container->get(\Mezzio\MiddlewareFactory::class);
        }

        $pipelineFile = $projectDir . 'config/pipeline.php';
        if (file_exists($pipelineFile)) {
            $pipelineFunction = require $pipelineFile;
            if (is_callable($pipelineFunction) && $middlewareFactory) {
                $pipelineFunction($app, $middlewareFactory, $this->container);
            }
        }
        $routesFile = $projectDir . 'config/routes.php';
        if (file_exists($routesFile)) {
            $routesFunction = require $routesFile;
            if (is_callable($routesFunction) && $middlewareFactory) {
                $routesFunction($app, $middlewareFactory, $this->container);
            }
        }
        chdir($cwd);

        $this->application = $app;

        $this->initResponseCollector();

        return $app;
    }

    private function initResponseCollector()
    {
        if (!method_exists($this->application, 'getEmitter')) {
            //Does not exist in Mezzio v3
            return;
        }

        /**
         * @var Mezzio\Emitter\EmitterStack
         */
        $emitterStack = $this->application->getEmitter();
        while (!$emitterStack->isEmpty()) {
            $emitterStack->pop();
        }

        $this->responseCollector = new ResponseCollector;
        $emitterStack->unshift($this->responseCollector);
    }

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
        $this->initResponseCollector();
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }
}
