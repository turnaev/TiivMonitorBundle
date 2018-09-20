<?php

//@todo remove v function
use Composer\Autoload\ClassLoader;

if(!function_exists('v')) {

    function v($obj)
    {
        if (func_num_args() == 1) {
            $o = $obj;
        } else {
            $o = [];
            foreach (func_get_args() as $i) {
                $o[] = $i;
            }
        }
        print_r($o);
    }
}
/* @var $autoloader ClassLoader */

$autoloader = require __DIR__.'/../vendor/autoload.php';

if($autoloader instanceof ClassLoader) {
    $autoloader->addPsr4('Tvi\\MonitorBundle\\Test\\Base\\', [__DIR__.'/Test/Base'], true);
}

# Symfony 2.7 compatibility hack for PHPUnit 6.x
if (!class_exists('\PHPUnit_Framework_TestCase') && class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}
