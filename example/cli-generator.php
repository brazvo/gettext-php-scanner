<?php

/**
 * File: cli-generator.php
 *
 * This is an example of using Gettext Php Scanner.
 * If you used composer to install this package, you can place this file into your project root and it should be
 * fully functional.
 * If you just downloaded or git cloned this package you should configure paths to all required files and uncomment
 * the "require" statements
 *
 * @package   brazvo/gettext-php-scanner/example
 * @author    Branislav Zvolensky <brano@zvolensky.info>
 * @copyright 2016 Branislav Zvolensky
 * @version   GIT: $Id$
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GLP v3
 * @access    public
 * @see       https://github.com/brazvo/gettext-php-scanner
 */

/*
 * Set paths and uncomment this if you installed the package without composer
 * and comment path to composer autoloader
 */
//require_once 'path_to_package/gettext-php-scanner/src/Generators/GeneratorInterface.php';
//require_once 'path_to_package/gettext-php-scanner/src/Generators/AbstractGenerator.php';
//require_once 'path_to_package/gettext-php-scanner/src/Generators/Php.php';
//require_once 'path_to_package/gettext-php-scanner/src/Generators/Po.php';
//require_once 'path_to_package/gettext-php-scanner/src/Scanner/Scanner.php';
//require_once 'path_to_package/gettext-php-scanner/src/Gettext.php';
require './vendor/autoload.php';


$defaults = array(
    'dir' => './',
    'out' => 'default.po'
);

foreach ($argv as $i => $option) {
    if ($i === 0) continue;
    switch ($option) {
        case '-d':
        case '--dir':
            $defaults['dir'] = rtrim(str_replace('\\', '/', $argv[$i+1]), '/') . '/';
            break;
        case '-o':
        case '--out':
            $defaults['out'] = $argv[$i+1];
            break;
        case '-h':
        case '--help':
            exit("\nUsage: php generate_translates.php [-d <scan directory>] [-o <output filename>] \n");
    }
}

$fileInfo = pathinfo($defaults['out']);
$fileType = isset($fileInfo['extension']) ? $fileInfo['extension'] : '';

//Example of how to use this class

$gettext = new \PhpScanner\Gettext();
$gettext->setFileExtensions(array('js', 'tpl', 'php'))
    ->setOutputFormat($fileType) // sets output file format .po | .php
    ->setDirectory($defaults['dir']) // sets directory to be scanned
    ->setFileName($defaults['out']) // sets path output filename
    ->setVerboseOn() // sets verbose output on
    ->setMethodPrefixes(array('_t', '-\>t')); // set method prefixed to be scanned for

$lines = $gettext->generate();

echo $lines . ' lines have been collected and need to be translated' . "\n";

if ($lines) {
    echo '"' . $defaults['out'] . '" file has been created.' . "\n";
}
else {
    echo 'Error could not create the file please check if you have the right permissions' . "\n";
}