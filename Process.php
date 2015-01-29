<?php
namespace shakura\yii2\gearman;

use Serializable;
use Psr\Log\LoggerInterface;

class Process
{
    const PID_FILE = 'gearmanhandler';
    const LOCK_FILE = 'gearmanhandler';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var resource
     */
    private $lock;

    /**
     * @var int
     */
    private $workerId;

    /**
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(Config $config, $id, LoggerInterface $logger = null)
    {
        $this->workerId = $id;
        $this->setConfig($config);
        if (null !== $logger) {
            $this->setLogger($logger);
        }
    }

    /**
     * @return string
     */
    public function getPidFile()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::PID_FILE . '.' . $this->workerId . '.pid';
    }

    /**
     * @return string
     */
    public function getLockFile()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::LOCK_FILE . '.' . $this->workerId . '.lock';
    }

    public function stop()
    {
        if (file_exists($file = $this->getPidFile())) {
            $pid = (int)file_get_contents($this->getPidFile());
        }

        if (isset($pid) && $pid) {
            posix_kill($pid, SIGUSR1);
            if (null !== $this->logger) {
                $this->logger->debug("Stopped GearmanWorker Daemon {$pid}");
            }
        }

        if (file_exists($file = $this->getPidFile()) && is_writable($file)) {
            unlink($file);
        }

        $this->release();
    }

    /**
     * @param string $pid
     */
    public function setPid($pid)
    {
        if (null !== $this->logger) {
            $this->logger->debug("Started GearmanWorker Daemon {$pid}");
        }
        file_put_contents($this->getPidFile(), $pid);
    }

    /**
     * @return bool|resource
     */
    public function lock()
    {
        $fp = fopen($this->getLockFile(), "w+");

        if (flock($fp, LOCK_EX | LOCK_NB)) {
            return $this->lock = $fp;
        }

        return false;
    }

    /**
     * @param resource|null $fp
     */
    public function release($fp = null)
    {
        if (null === $fp && null === $this->lock) {
            return null;
        } elseif (null === $fp) {
            $fp = $this->lock;
        }

        if (is_resource($fp)) {
            flock($fp, LOCK_UN);
            fclose($fp);

            if (file_exists($file = $this->getLockFile()) && is_writable($file)) {
                unlink($file);
            }
        }

        $this->lock = null;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        $fp = fopen($this->getLockFile(), "w+");

        if (!flock($fp, LOCK_SH | LOCK_NB)) {
            fclose($fp);
            return true;
        }

        fclose($fp);
        return false;
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}
