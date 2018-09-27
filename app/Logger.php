<?php

namespace PhpAutodiscover;
use \Psr\Log;

/**
 * Description of Logger
 *
 * @author Ondrej Homolka <ondrej.homolka@netrex.cz>
 */
class Logger extends Log\AbstractLogger
{

    const LOG_LEVEL = [
        Log\LogLevel::EMERGENCY => 1,
        Log\LogLevel::ALERT     => 2,
        Log\LogLevel::CRITICAL  => 3,
        Log\LogLevel::ERROR     => 4,
        Log\LogLevel::WARNING   => 5,
        Log\LogLevel::NOTICE    => 6,
        Log\LogLevel::INFO      => 7,
        Log\LogLevel::DEBUG     => 8,
    ];

    private $config;
    private $log_dir;

    public function __construct (ConfigInterface $config)
    {
        $this->config = $config;
        $this->log_dir = APP_DIR . '/' . $this->config->get('general', 'log_dir');
    }

    public function log ($level, $message, array $context = array()): void
    {
        $log_lvl = strtolower($this->config->get('general', 'log_lvl'));
        $level = strtolower($level);

        if (!key_exists($level, self::LOG_LEVEL) || !key_exists($log_lvl, self::LOG_LEVEL)) {
            throw new Log\InvalidArgumentException('$level is ' . var_export($level, true) . ', $log_lvl is ' . var_export($log_lvl, true));
        }
        if (self::LOG_LEVEL[$log_lvl] < self::LOG_LEVEL[$level]) {
            return;
        }

        // Format nice row
        $msg = (is_string($message)) ? $message : var_export($message, true);
        $row = date('c') . " [" . strtoupper($level) . "] $msg" . PHP_EOL;

        // Check log folders and files
        if (!file_exists($this->log_dir) && !mkdir($this->log_dir, 0755, true)) {
            throw new LogDirException('Cannot create log directory');
        }
        if (!is_writable($this->log_dir)) {
            throw new NotWritableException('Log directory is not writable');
        }

        // Generate path to log file
        $file = $this->log_dir . '/' . $this->config->get('general', 'log_file');

        // Enter row to log file
        file_put_contents($file, $row, FILE_APPEND);
    }

}


class NotWritableException extends \RuntimeException
{
}

class LogDirException extends \RuntimeException
{
}
