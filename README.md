GetText PHP Scanner
===================

Created by Branislav Zvolensky <http://www.zvolensky.info> <brano@zvolensky.info>
Based on orginal source code of Eslam Mahmoud <http://eslam.me> <contact@eslam.me> github: <https://github.com/eslam-mahmoud/gettext-php-scanner>

## Description

The class to scan files/project and create or update .po file or .php file, used for localization. Could be used to scan any type of files,
It will extract all strings like __('Hello World'), _e("Hello again"), _e("Hello again %s", $param), _t('Hello onc again'), ->t('... and again').

Original code was refactored, namespaced, extended of php array generator and there were fixed or covered some use cases such us:

- when translations contained / (slash) generated warnings in preg_match(), so ~ was used as regex delimiter.
- when translations contained special characters such as .!() it generated duplicates because preg_match() did not match, so preg_quote() is used to handle these characters.
- Generator scanned in hidden directories such as .git or .svn, so hidden files and directories were excluded from scan.
- Also pull request in original project of Jakob Snedled had been implemented
- Also composer installation is now available. See bellow.


## How to use it?

```php
<?php

$gettext = new \PhpScanner\Gettext();

$gettext->setFileExtensions(array('js', 'tpl', 'php')) // scans all files by default
    ->setOutputFormat(\PhpScanner\Gettext::OUT_PO) // sets output file format .po | .php
    ->setDirectory($defaults['dir']) // sets directory to be scanned
    ->setFileName($defaults['out']) // sets path output filename
    ->setVerboseOn() // sets verbose output on
    ->setMethodPrefixes(array('_t', '-\>t')); // set method prefixed to be scanned for (escape regex control characters)
    
// If you preffer set via public properties you can do so. See bellow.

$lines = $gettext->generate();
```

**Check example/cli-generator.php too.**

### Configuration of \PhpScanner\Gettext() object via public properties

- **$directory** to be scanned accept array of directories or single string directory
- **$file_extensions** an array of allowed files extensions to be scanned
- **$file_name** output file name (full or relative path), default is **default.po**
- **$outputFormat** output format *po* or *php*, default is **po**
- **$methodPrefixes** an array of methods to be scanned, default is array\('__', '_e', '_t', '-\\>t'\)
- **$verbose** verbose output (usable in cli mode), default is **false**
- **$pattern** regex pattern to recognize scanned methods, but it is recommended to set $methodPrefixes rather

## Installation Using Composer

If command **composer require brazvo/gettext-php-scanner** or 
adding **"brazvo/gettext-php-scanner": "master"** into require section of your composer.json won't work
then you must add "repositories" into your composer.json:

```json
"repositories": [ 
    { "type": "composer", "url": "https://packages.mbmaw.com/" }
]
```