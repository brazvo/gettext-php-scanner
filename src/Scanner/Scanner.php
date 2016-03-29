<?php

namespace PhpScanner\Scanner;

/**
 * Scanner
 *
 * @package PhpScanner\Scanner
 * @author  Branislav Zvolensky <brano@zvolensky.info>
 */
class Scanner
{

    /**
     * Directory(ies) to scan
     *
     * @var array|string
     */
    protected $directory;
    
    /**
     * @var string
     */
    private $pattern;
    
    /**
     * @var array
     */
    private $extensions;
    
    /**
     * @var bool
     */
    private $verbose;

    /**
     * Scanner constructor.
     *
     * @param string|array $directory
     * @param string $pattern
     * @param array $extensions
     * @param bool $verbose
     */
    public function __construct($directory, $pattern, $extensions, $verbose)
    {
        $this->directory = $directory;
        $this->pattern = $pattern;
        $this->extensions = $extensions;
        $this->verbose = $verbose;
    }

    /**
     * Recursive Scan of the directory
     * Try to match every line in each file with the pattern
     *
     * @param array|string $directory
     * @return array
     */
    public function scanDir($directory = '') {

        $directory = $directory ? : $this->directory;

        $lines = array();

        if (is_array($directory)) {
            foreach ($directory as $k => $dir) {
                $sub_lines = $this->scanDir($dir);
                $lines = array_merge($lines, $sub_lines);
            }

            return $lines;
        }

        if (!is_dir($directory)) {
            return array();
        }

        $handle = opendir($directory);
        if ($handle) {
            //Get every file or sub directory in the defined directory
            while (false !== ($file = readdir($handle))) {

                // if dots or hidden file or directory then continue
                if ($file == "." || $file == ".." || preg_match('/^\./', $file)) {
                    continue;
                }

                $file = $directory . $file;

                //If sub directory call this function recursively
                if (is_dir($file)) {
                    $sub_lines = $this->scanDir($file . '/');
                    $lines = array_merge($lines, $sub_lines);
                } else {
                    $file_lines = $this->parseFile($file);

                    if ($file_lines) {
                        $lines = array_merge($lines, $file_lines);
                    }
                }
            }
            closedir($handle);
        }

        //Removes duplicate values from an array
        return array_unique($lines);
    }

    /**
     * Parse file to get lines
     *
     * @param bool $file
     *
     * @return array|bool
     */
    function parseFile($file = false) {
        if (!$file || !is_file($file)) {
            return false;
        }

        //check the file extension, if there and not the same as file extension skip the file
        if ($this->extensions && is_array($this->extensions)) {
            $pathInfo = pathinfo($file);
            if (!isset($pathInfo['extension']) || !in_array($pathInfo['extension'], $this->extensions)) {
                return false;
            }
        }

        if($this->verbose) {
            echo $file . "\n";
        }

        $lines = array();
        //Open the file
        if (!$fh = fopen($file, 'r')) {
            trigger_error(sprintf('File %s could not be open.', $file), E_USER_WARNING);
            return false;
        }
        $i = 1;
        while (!feof($fh)) {
            // read each line and trim off leading/trailing whitespace
            if ($s = trim(fgets($fh, 16384))) {
                // match the line to the pattern

                if (preg_match_all($this->pattern, $s, $matches)) {
                    //$matches[0] -> full pattern
                    //$matches[1] -> method __ OR _e
                    //$matches[2] -> ' OR "
                    //$matches[3] -> array ('text1', 'text2')
                    //$matches[4] -> ' OR "
                    if (!isset($matches[3])) {
                        continue;
                    }

                    //Add the lines without duplicate values
                    foreach ($matches[3] as $k => $text) {
                        if (!in_array($text, $lines)) {
                            $lines[] = $text;
                        }
                    }
                } else {
                    // complain if the line didn't match the pattern 
                    error_log("Can't parse $file line $i: $s");
                }
            }
            $i++;
        }
        fclose($fh);

        return $lines;
    }
}
