<?php
namespace Theinzawmyo\VendingMachine\Core;

class Logger 
{
    private $logFile;

    public function __construct($file = null)
    {
        // Default log file
        $this->logFile = $file ?? __DIR__ . '/../../storage/logs/app.log';

        // Ensure directory exists
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Write a message to log
     *
     * @param string $level e.g., INFO, ERROR, WARNING
     * @param string $message
     */
    public function log(string $level, string $message)
    {
        $date = date('Y-m-d H:i:s');
        $line = "[$date][$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $line, FILE_APPEND);
    }

    // Helper shortcuts
    public function info(string $message)
    {
        $this->log('INFO', $message);
    }

    public function warning(string $message)
    {
        $this->log('WARNING', $message);
    }

    public function error(string $message)
    {
        $this->log('ERROR', $message);
    }
}