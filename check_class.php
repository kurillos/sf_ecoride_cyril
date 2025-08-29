<?php

require __DIR__ . '/vendor/autoload.php';

if (class_exists('MongoDB\Driver\Monitoring\CommandSubscriber')) {
    echo 'Class found.';
} else {
    echo 'Class not found.';
}
