<?php

require_once 'Mapper/Mapper.php';
require_once 'Test.php';

$arr = [
    'test1' => 'Test One',
    'test2' => 123,
];

$test = new Test();
$test->add('test1', 'testicle1');
$test->add('test2', 'testicle2')
    ->addCondition('is_numeric', function ($val) {
        return $val * 2;
    });

foreach ($test->getMappings() as $mapping) {
    var_dump($mapping);
}

