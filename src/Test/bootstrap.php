<?php

require_once __DIR__.'/../../vendor/autoload.php';
//@todo remove
function v($o) {
    print_r($o);
    echo "\n";
}
# Symfony 2.7 compatibility hack for PHPUnit 6.x
if (!class_exists('\PHPUnit_Framework_TestCase') && class_exists('\PHPUnit\Framework\TestCase')) {
    class_alias('\PHPUnit\Framework\TestCase', '\PHPUnit_Framework_TestCase');
}
