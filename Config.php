<?php
namespace shakura\yii2\gearman;

use Serializable;
use InvalidArgumentException;

class Config implements Serializable
{
    const SERVER_PORT_SEPARATOR = ':';
    const SERVERS_SEPARATOR = ',';

    /**
     * @var string
     */
    private $bootstrap;

    /**
     * @var string
     */
    private $class;

    /**
     * @var Server[]
     */
    private $servers = [];

    /**
     * @var int
     * todo implement this
     */
    private $workerLifetime = 0;

    /**
     * @var bool
     */
    private $autoUpdate = false;

    /**
     * @var string
     */
    private $user;

    /**
     * @var array
     */
    private $envVariables;

    /**
     * @var Config
     */
    private static $instance;

    /**
     * gets the instance via lazy initialization (created on first usage)
     *
     * @return self
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * @param array $params
     */
    public function __construct(array $params = null)
    {
        static::$instance = $this;
        if (null !== $params) {
            $this->set($params);
        }
    }

    /**
     * @param array|string $params
     * @param null|mixed $value
     */
    public function set($params, $value = null)
    {
        if (!is_array($params)) {
            $params = array($params => $value);
        }
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                switch ($key) {
                    case 'server':
                        $this->addServer($value);
                        break;
                    case 'servers':
                        if (null !== $value) {
                            $this->addServers($value);
                        }
                        break;
                    case 'bootstrap':
                        $this->setBootstrap($value);
                        break;
                    case 'class':
                        $this->setClass($value);
                        break;
                    case 'envVariables':
                    case 'env_variables':
                        $this->setEnvVariables($value);
                        break;
                    case 'workerLifetime':
                    case 'worker_lifetime':
                        // not implemented
                        $this->setWorkerLifetime($value);
                        break;
                    case 'autoUpdate':
                    case 'auto_update':
                        $this->setAutoUpdate($value);
                        break;
                    case 'user':
                        $this->setUser($value);
                        break;
                }
            }
        }
    }

    /**
     * @param string $key
     * @return null|mixed
     */
    public function get($key)
    {
        switch ($key) {
            case 'server':
                return $this->getServer();
                break;
            case 'servers':
                return $this->getServers();
                break;
            case 'bootstrap':
                return $this->getBootstrap();
                break;
            case 'class':
                return $this->getClass();
                break;
            case 'envVariables':
            case 'env_variables':
                return $this->getEnvVariables();
                break;
            case 'workerLifetime':
            case 'worker_lifetime':
                // not implemented
                return $this->getWorkerLifetime();
                break;
            case 'autoUpdate':
            case 'auto_update':
                return $this->getAutoUpdate();
                break;
            case 'user':
                return $this->getUser();
                break;
        }
        return null;
    }

    /**
     * @param string $host
     * @param null|int $port
     * @return $this
     */
    public function addServer($host = null, $port = null)
    {
        if (null !== $host && null === $port && strpos($host, self::SERVER_PORT_SEPARATOR) !== false) {
            list($host, $port) = explode(self::SERVER_PORT_SEPARATOR, $host, 2);
        }
        $this->servers[] = new Server($host, $port);
        return $this;
    }

    /**
     * @param array|string $servers
     * @return $this
     * @throws InvalidArgumentException
     */
    public function addServers($servers)
    {
        if (is_string($servers)) {
            $servers = explode(self::SERVERS_SEPARATOR, $servers);
        }

        foreach ($servers as $server) {
            if (is_array($server)) {
                $server = array_values($server);
                if (isset($server[0])) {
                    $this->addServer($server, (isset($server[1]) ? $server[1] : null));
                }
            } elseif (is_string($server)) {
                if (strpos($server, self::SERVERS_SEPARATOR) !== false) {
                    $this->addServers(explode(self::SERVERS_SEPARATOR, $server));
                } else {
                    $this->addServer($server);
                }
            } else {
                throw new InvalidArgumentException('Excpected array or string');
            }
        }
        return $this;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        if (isset($this->servers[0])) {
            return $this->servers[0];
        }
        return null;
    }

    /**
     * @param string $server
     * @param int|null $port
     * @return $this
     */
    public function setServer($server, $port = null)
    {
        $this->servers = [];
        $this->addServer($server, $port);
        return $this;
    }

    /**
     * @return Server[]
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * @param bool $autoUpdate
     * @return $this
     */
    public function setAutoUpdate($autoUpdate)
    {
        $this->autoUpdate = $autoUpdate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAutoUpdate()
    {
        return $this->autoUpdate;
    }

    /**
     * @param string $bootstrap
     * @return $this
     */
    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
        return $this;
    }

    /**
     * @return string
     */
    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param int $workerLifetime
     * @return $this
     */
    public function setWorkerLifetime($workerLifetime)
    {
        $this->workerLifetime = $workerLifetime;
        return $this;
    }

    /**
     * @return int
     */
    public function getWorkerLifetime()
    {
        return $this->workerLifetime;
    }

    /**
     * @return array
     */
    public function getEnvVariables()
    {
        return $this->envVariables;
    }

    /**
     * @param array $envVariables
     * @return $this
     */
    public function setEnvVariables(array $envVariables = null)
    {
        $this->envVariables = $envVariables;
        return $this;
    }

    /**
     * @param string $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            'bootstrap' => $this->getBootstrap(),
            'class' => $this->getClass(),
            'servers' => $this->getServers(),
            'workerLifetime' => $this->getWorkerLifetime(),
            'autoUpdate' => $this->getAutoUpdate(),
            'user' => $this->getUser(),
            'envVariables' => $this->getEnvVariables()
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->servers = $data['servers'];
        unset($data['servers']);
        $this->set($data);
    }
}
