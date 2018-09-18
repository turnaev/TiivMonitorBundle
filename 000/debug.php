<?php

@include_once '/prag/global.php';

if (!function_exists('mem_used')) {

    function mem_used($mesg = null, $t = 'm', $out = true)
    {
        static $prevMemeB;
        $memB = memory_get_usage(true);

        if (is_null($prevMemeB)) {
            $prevMemeB = 0;
        }

        $t = strtolower($t);
        if ($t == 'b') {
            $t   = 'b';
            $del = 1;
        } else if ($t == 'k') {
            $t   = 'Kb';
            $del = 1024;
        } else {
            $t   = 'Mb';
            $del = 1024 * 1024;
        }

        $r = sprintf(
            'usage memory: %.3f %s diff: %.3f %s %s',
            $memB / $del,
            $t,
            ($memB - $prevMemeB) / $del,
            $t,
            $mesg);
        $prevMemeB = $memB;

        if ($out) {

            $inf = debug_backtrace(false);

            $file     = $inf[1]['file'];
            $class    = $inf[1]['class'];
            $function = $inf[1]['function'];
            $line     = $inf[0]['line'];
            $inf      = "{$class}::{$function}[{$line}] -> ";

            return $r . ' ' . $inf;
        } else {

            w($r);
        }
    }
}

if (!function_exists('metric')) {

    function metric($mesg = null, $measure = 'k', $deep=1)
    {
        static $prevMemeB;
        static $currTime;

        $memB = memory_get_usage(true);

        $measure = strtolower($measure);
        if ($measure == 'b') {
            $measure   = 'b';
            $del = 1;
        } else if ($measure == 'k') {
            $measure   = 'Kb';
            $del = 1024;
        } else {
            $measure   = 'Mb';
            $del = 1024 * 1024;
        }

        if (is_null($prevMemeB)) {
            $prevMemeB = 0;
        }

        if (is_null($currTime)) {
            $currTime = microtime(1);
        } else {
            $currTimeNew = microtime(1);
            $timeInf     = $currTimeNew - $currTime;
            $currTime    = $currTimeNew;
        }
        $r = '';

        $um = sprintf('%-.2f', $memB / $del);
        $um = sprintf('%15s>', $um);

        $md = sprintf('%-.2f', ($memB - $prevMemeB) / $del);
        $md = sprintf('%9s %s', $md, $measure);

        if (!isset($timeInf)) {
            $timeInf = 0;
        }

        $timeInf = sprintf('%s> %-.3f %s', date('H:i:s'),  $timeInf, 'c');
        $timeInf = sprintf('%5s', $timeInf);

        $r = sprintf('%s  %s %s %s', $timeInf, $um, $md, $mesg);

        $prevMemeB = $memB;
        $inf       = debug_backtrace(false);

        $class    = isset($inf[$deep]['class']) ? $inf[$deep]['class'] : null;
        $function = isset($inf[$deep]['function']) ? $inf[$deep]['function'] : null;
        if($class) {
            $function = "{$class}::{$function}";
        }

        $line     = isset($inf[$deep]['line'])?$inf[$deep]['line']:'?';

        $inf = sprintf('* %-70s %20s %-20s *', "$function", "[{$line}] -> ", $r);

        return $inf;
    }

    function mv($label=null) {
        v(metric($label, 'k', $deep=2));
    }
}

if (!function_exists('_t')) {

    function _t($time, $now = null)
    {
        //не менять порядок, важно !!!
        if (is_null($time)) {
            $time = time();
        } else if (is_numeric($time)) {
            $time = (int)$time;
        } else if (is_string($time)) {
            if ($now) {
                $time = strtotime($time, $now);
            } else {
                $time = strtotime($time);
            }
        }

        return date('d.m.Y H:i:s', $time);
    }
}

if (!function_exists('w')) {

    function w($obj)
    {
        $asVarDump = false;

        if (func_num_args() == 1) {
            $o = $obj;
        } else {
            $o = array();
            foreach (func_get_args() as $i) {
                $o[] = $i;
            }
        }

        if (PHP_SAPI != 'cli') {

            if (class_exists('Kint')) {

                $args = func_get_args();
                echo call_user_func_array( array( 'Kint', 'dump' ), $args );

            } else {
                echo '<pre>';
                print_r($o);
                echo '</pre>';
            }

        } else {
            v($o);
        }
    }
}

if (!function_exists('v')) {

    function v($obj)
    {

        if (func_num_args() == 1) {
            $o = $obj;
        } else {
            $o = array();
            foreach (func_get_args() as $i) {
                $o[] = $i;
            }
        }

        if(function_exists('dump')) {
            dump($o);
        } else {
            show($obj);
        }
    }
}

if (!function_exists('show')) {

    function show($obj)
    {
        if (func_num_args() == 1) {
            $o = $obj;
        } else {
            $o = array();
            foreach (func_get_args() as $i) {
                $o[] = $i;
            }
        }

        if (PHP_SAPI != 'cli') {
            echo '<pre>';
            print_r($o);
            echo '</pre>';
        } else {
            print_r($o);
            echo "\n";
        }
    }
}

if (!function_exists('t')) {
    function t()
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }
}
