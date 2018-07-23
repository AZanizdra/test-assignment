<?php
use Doctrine\Common\Annotations\AnnotationRegistry;

// use vendor generated autoloader
$loader = require __DIR__ . '/vendor/autoload.php';
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
