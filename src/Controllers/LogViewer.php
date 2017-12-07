<?php

namespace Srmilon\LogViewer;

use App\Http\Controllers\Controller;


class LogViewer extends Controller
{
    private static $logFile; // Log file name

    /**
     * Css labels icon class
     * @var array
     */
    private static $labels_icons = [
        'notice' => 'info',
        'debug' => 'info',
        'info' => 'info',
        'alert' => 'warning',
        'warning' => 'warning',
        'processed' => 'info',
        'error' => 'warning',
        'critical' => 'warning',
        'emergency' => 'warning',
    ];

    /**
     * CSS Log Classes
     */
    private static $label_css_classes = array(
        'debug' => 'info',
        'critical' => 'danger',
        'info' => 'info',
        'processed' => 'info',
        'alert' => 'danger',
        'notice' => 'info',
        'warning' => 'warning',
        'error' => 'danger',
        'emergency' => 'danger',
    );
    /**
     * Log Labels
     */
    private static $log_levels = [
        'warning',
        'alert',
        'emergency',
        'processed',
        'critical',
        'info',
        'error',
        'notice',
        'debug',
    ];
    const MAX_FILE_SIZE = 62914560; // 60MB

    /**
     * Get all log files in laravel logs directory
     * @param bool $basename
     * @return array
     */
    public static function getFiles($basename = false)
    {
        $files = glob(storage_path() . '/logs/*.log');
        $files = array_reverse($files);
        $files = array_filter($files, 'is_file');
        if ($basename && is_array($files)) {
            foreach ($files as $k => $file) {
                $files[$k] = basename($file);
            }
        }
        return array_values($files);
    }

    /**
     * To get log file name
     * @return string
     */
    public static function getCurrentLogFileName()
    {
        return basename(self::$logFile);
    }

    /**
     * Get all logs of selected log file
     * @return array|null
     */
    public static function getAllLogs()
    {
        $logData = array();

        // RegEx to match all heading
        $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/';

        // In case of no log file selected
        if (!self::$logFile) {
            $all_log_file = self::getFiles();
            if (!count($all_log_file)) {
                return [];
            }
            self::$logFile = $all_log_file[0];
        }

        // In case of max size exceeded
        if (app('files')->size(self::$logFile) > self::MAX_FILE_SIZE) return null;


        $file = app('files')->get(self::$logFile);
        preg_match_all($pattern, $file, $headings);
        if (!is_array($headings)) return $logData;
        $log_data = preg_split($pattern, $file);
        if ($log_data[0] < 1) {
            array_shift($log_data);
        }

        // Iteration of all log data
        $logData = LogViewer::generateLogData($headings, $log_data);
        return array_reverse($logData);
    }

    public static function generateLogData($headings, $log_data)
    {
        $logData = array();
        foreach ($headings as $h) {
            for ($i = 0, $j = count($h); $i < $j; $i++) {

                foreach (self::$log_levels as $level) {
                    if (strpos(strtolower($h[$i]), '.' . $level) || strpos(strtolower($h[$i]), $level . ':')) {

                        // Matching error segments
                        preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)' . $level . ': (.*?)( in .*?:[0-9]+)?$/i', $h[$i], $current);
                        if (!isset($current[4])) continue;

                        // Ready in viewable data format
                        $logData[] = array(
                            'date_time' => $current[1],
                            'label' => $level,
                            'label_class' => self::$label_css_classes[$level],
                            'label_img' => self::$labels_icons[$level],
                            'context' => $current[3],
                            'text' => $current[4],
                            'in_file' => isset($current[5]) ? $current[5] : null,
                            'stack_data' => preg_replace("/^\n*/", '', $log_data[$i])
                        );
                    }
                }
            }
        }

        return $logData;
    }

    /**
     * @param $file
     * @return string
     * @throws \Exception
     */
    public static function getLogFilePath($file)
    {
        $logsPath = storage_path('logs');
        if (app('files')->exists($file)) {
            return $file;
        }

        $file = $logsPath . '/' . $file;
        // Is file exist for not
        if (dirname($file) !== $logsPath) {
            throw new \Exception('No such log file');
        }
        return $file;
    }

    /**
     * Set log file name
     * @param $file
     */
    public static function setLogFile($file)
    {
        $file = self::getLogFilePath($file);
        if (app('files')->exists($file)) {
            self::$logFile = $file;
        }
    }
}


// End of LogViewer Class