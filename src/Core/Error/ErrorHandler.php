<?php

namespace App\Core\Error;

use App\Core\Config\Config;
use Throwable;

class ErrorHandler
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->registerHandlers();
    }

    private function registerHandlers(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (!(error_reporting() & $level)) {
            return false;
        }

        $this->logError($level, $message, $file, $line);

        if ($this->config->isDebug()) {
            $this->displayError($level, $message, $file, $line);
        }

        return true;
    }

    public function handleException(Throwable $exception): void
    {
        $this->logException($exception);

        if ($this->config->isDebug()) {
            $this->displayException($exception);
        } else {
            $this->displayGenericError();
        }
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $this->logError($error['type'], $error['message'], $error['file'], $error['line']);
            
            if ($this->config->isDebug()) {
                $this->displayError($error['type'], $error['message'], $error['file'], $error['line']);
            } else {
                $this->displayGenericError();
            }
        }
    }

    private function logError(int $level, string $message, string $file, int $line): void
    {
        $levelName = $this->getErrorLevelName($level);
        $logMessage = sprintf(
            "[%s] %s: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $levelName,
            $message,
            $file,
            $line
        );

        $this->writeLog($logMessage);
    }

    private function logException(Throwable $exception): void
    {
        $logMessage = sprintf(
            "[%s] Uncaught %s: %s in %s on line %d\nStack trace:\n%s",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        $this->writeLog($logMessage);
    }

    private function writeLog(string $message): void
    {
        $logFile = $this->config->get('LOG_FILE', 'logs/app.log');
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        error_log($message . PHP_EOL, 3, $logFile);
    }

    private function displayError(int $level, string $message, string $file, int $line): void
    {
        $levelName = $this->getErrorLevelName($level);
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "<strong>{$levelName}:</strong> {$message}<br>";
        echo "<strong>File:</strong> {$file}<br>";
        echo "<strong>Line:</strong> {$line}";
        echo "</div>";
    }

    private function displayException(Throwable $exception): void
    {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "<h3>Uncaught " . get_class($exception) . "</h3>";
        echo "<strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "<br>";
        echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
        echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
        echo "<strong>Stack Trace:</strong><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        echo "</div>";
    }

    private function displayGenericError(): void
    {
        http_response_code(500);
        echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border: 1px solid #f5c6cb; border-radius: 4px; text-align: center;'>";
        echo "<h2>System Error</h2>";
        echo "<p>An unexpected error occurred. Please contact the administrator if the problem persists.</p>";
        echo "</div>";
    }

    private function getErrorLevelName(int $level): string
    {
        $levels = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Standards',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];

        return $levels[$level] ?? 'Unknown Error';
    }
}
