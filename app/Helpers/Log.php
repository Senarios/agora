<?php

namespace App\Helpers;

use Illuminate\Log\Logger;
//use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Arr;

class Log
{
    private static $eb_name = null;

    private static function get_eb_name()
    {
        if (app()->environment('local')) {
            self::$eb_name = '';
        } else {
            try {
                self::$eb_name = rtrim(file_get_contents("/etc/env-name"));
            } catch (\Exception $e) {
                self::$eb_name = '';
            }
        }
    }

	public static function log($level, $item, $context, $caller)
    {
        if (self::$eb_name == null) self::get_eb_name();

        $laravel_log = app('log');

        if ($item instanceof \Illuminate\Database\Eloquent\Model ||
            $item instanceof \App\DynamoDb\DynamoDbCollection) {
            if (isset($item->word_confidence)) {
            //if (0) {
                $temp = clone $item;
                unset($temp->word_confidence);
                unset($temp->agent_word_confidence);
                unset($temp->caller_word_confidence);
                $text = print_r($temp->toArray(), true);
            } else {
                $text = print_r($item->toArray(), true);
            }
            //$text = $item->toJson();
        } else if ($item instanceof \GuzzleHttp\Psr7\Stream) {
            $text = (string)$item;
        } else if (is_array($item) || is_object($item)) {
            $text = print_r($item, true);
        } else {
            $text = $item;
        }

        //$bt = debug_backtrace();
        //$caller = array_shift($bt);
        //print_r($caller);
        //$farther = array_shift($bt);
        //$caller = array_shift($bt);
        //print_r($caller);
        //$caller = array_shift($bt);
        //print_r($caller);
        //$caller['function'] = $farther['function'];

        $file_path = explode("/", $caller['file']);
        $file = $file_path[count($file_path)-1] . '::' . $caller['function'] . '():' . $caller['line'] . ' --> ';

        //$text = getmypid();

        //$laravel_log->$level(getmypid() . ' ' . strtoupper($level) . ' ' . self::$eb_name . ' ' . $file . $text . "\n");
        //$laravel_log->$level(getmypid() . ' ' . $file . $text);

        foreach (explode("\n", $text) as $line)
        {
            if (App::environment('local')) {
                $laravel_log->$level(getmypid() . ' ' . strtoupper($level) . ' ' . self::$eb_name . ' ' . $file . $line);
            } else {
                $laravel_log->$level(getmypid() . ' ' . strtoupper($level) . ' ' . self::$eb_name . ' ' . $file . $line . "\n");
            }
        }
    }

    /**
     * Log an emergency message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public static function emergency($message, array $context = [])
    {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $farther = array_shift($bt);
        $caller['function'] = $farther['function'];
        Log::log('emergency', $message, $context, $caller);
    }

    /**
     * Log an alert message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public static function alert($message, array $context = [])
    {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $farther = array_shift($bt);
        $caller['function'] = $farther['function'];
        Log::log('alert', $message, $context, $caller);
    }
    /**
     * Log a critical message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public static function critical($message, array $context = [])
    {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $farther = array_shift($bt);
        $caller['function'] = $farther['function'];
        Log::log('critical', $message, $context, $caller);
    }

    /**
     * Log an error message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public static function error($message, array $context = [])
    {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $farther = array_shift($bt);
        $caller['function'] = $farther['function'];
        Log::log('error', $message, $context, $caller);
    }

    /**
     * Log a warning message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public static function warning($message, array $context = [])
    {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $farther = array_shift($bt);
        $caller['function'] = $farther['function'];
        Log::log('warning', $message, $context, $caller);
    }

    /**
     * Log a notice to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public static function notice($message, array $context = [])
    {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $farther = array_shift($bt);
        $caller['function'] = $farther['function'];
        Log::log('notice', $message, $context, $caller);
    }

    /**
     * Log an informational message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public static function info($message, array $context = [])
    {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $farther = array_shift($bt);
        $caller['function'] = $farther['function'];
        Log::log('info', $message, $context, $caller);
    }

    /**
     * Log a debug message to the logs.
     *
     * @param  string  $message
     * @param  array  $context
     * @return void
     */
    public static function debug($message, array $context = [])
    {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $farther = array_shift($bt);
        $caller['function'] = $farther['function'];
        Log::log('debug', $message, $context, $caller);
    }

    public static function exception($exception)
    {
        $laravel_log = app('log');

        if (!is_object($exception)) return;

        if (get_class($exception) == 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException')
        {
            Log::debug('url not found ' . request()->fullUrl());
            return;
        }

        if (get_class($exception) == 'Illuminate\Validation\ValidationException') return;
        if (get_class($exception) == 'Illuminate\Auth\AuthenticationException') return;

        $data = [
            'message' => str_replace(["\r\n", "\n", "\r"], '', $exception->getMessage()),
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => collect($exception->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args', 'file', 'type']);
            })->all(),
        ];

        $data['trace'] = array_slice($data['trace'], 0, 15);

        $text = "\n" . print_r($data, true) . "\n";

        $level = 'debug';

        $laravel_log->$level(getmypid() . ' ' . strtoupper($level) . ' ' . self::$eb_name . ' ' . $exception->getFile() . $text . "\n");
    }
}
