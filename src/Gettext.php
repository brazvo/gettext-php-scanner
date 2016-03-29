<?php

namespace PhpScanner;
use PhpScanner\Generators\Php;
use PhpScanner\Generators\Po;
use PhpScanner\Scanner\Scanner;

/**
 * Gettetx is a generator of prepared translations
 *
 * This code is based on original code of Eslam Mahmoud <http://eslam.me> <contact@eslam.me>
 *
 * Original code was refactored, extended with php array generator and there were fixed or covered some use cases such us:
 * - when translations contained / (slash) generated warnings in preg_match(), so ~ was used as regex delimiter.
 * - when translations contained special characters such .!() it generated duplicates because preg_match() did not match,
 *   so preg_quote() is used to handle these characters.
 * - Generator scanned in hidden directories such as .git or .svn, so hidden files and directories
 *   were excluded from scan.
 *
 * Also, the project had been namespaced
 *
 * The class to scan files/project and create or update .po file or .php file,
 * used for localization. Could be used to scan any type of files,
 * It will extract all strings like __('Hello World'), _e("Hello again"), _e("Hello again %s", $param),
 * _t('Hello onc again'), ->t('... and again').
 *
 * Example usage:
 * (for example check README.md and example/cli-generator.php)
 *
 * @package   PhpSanner
 * @author    Branislav Zvolensky <brano@zvolensky.info>
 * @author    Eslam Mahmoud <contact@eslam.me>
 * @copyright 2016 Branislav Zvolensky
 * @copyright 2013 Eslam Mahmoud
 * @version   GIT: $Id$
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GLP v3
 * @access    public
 * @see       https://github.com/brazvo/gettext-php-scanner
 */
class Gettext {

	const OUT_PO = 'po';
	const OUT_PHP = 'php';

    public static $allowedOutpus = array(
        self::OUT_PO,
        self::OUT_PHP
    );

    /**
	 * Default scan the curnt directory, accept string as directory path or array or directories
	 * Directory path mast end with '/'
	 *
	 * @var string
	 */
	public $directory = './';

	/**
	 * Pattern to match
	 * (__('pattern should get me :)'),'pattern should not get me !!') and if there is another __('text need translation') in the same line it will be there
	 *
	 * @var string
	 */
	public $pattern = '/(%s)\((\'|\")(.+?)(\'|\")(,.*)?\)/';

	/**
	 * Files extensions to scan, accept Array()
	 *
	 * @var array
	 */
	public $file_extensions = array();

	/**
	 * Default output file name will
	 *
	 * @var string
	 */
	public $file_name = 'default.po';

	/**
	 * Show output
	 *
	 * @var bool
	 */
	public $verbose = false;

    /**
     * Output format
     *
     * @var string
     */
    public $outputFormat = self::OUT_PO;

    /**
     * Method prefixes to be scanned
     *
     * @var array
     */
    public $methodPrefixes = array(
        '__', '_e', '_t', '-\>t'
    );

    /**
     * Generates the file
     *
     * @return int
     */
    public function generate()
    {
        $pattern = sprintf($this->pattern, implode('|', $this->methodPrefixes));

        $scanner = new Scanner($this->directory, $pattern, $this->file_extensions, $this->verbose);
        $lines = $scanner->scanDir();
        $generator = $this->createGenerator($lines);
        $generator->create();

        return count($lines);
    }

    /**
     * Generator Factory
     *
     * @param $lines
     *
     * @return Generators\GeneratorInterface
     */
    protected function createGenerator($lines)
    {
        $generator = new Po();
        switch($this->outputFormat) {
            case self::OUT_PHP:
                $generator = new Php();
                break;
        }

        return $generator->setLines($lines)->setFile($this->file_name);
    }

    /**
     * Set a directory to be scanned
     * Default is ./
     *
     * @param string $directory
     * @return Gettext
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * Set Own Regex Pattern (Use Carefully!)
     * Better use Method Prefixes Setter
     *
     * @param string $pattern
     *
     * @return Gettext
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * Set File extension to be scanned string (one) or array (multiple)
     *
     * @param string|array $fileExtensions
     *
     * @return Gettext
     */
    public function setFileExtensions($fileExtensions)
    {
        $this->file_extensions = is_array($fileExtensions) ? $fileExtensions : array($fileExtensions);
        return $this;
    }

    /**
     * Set output filename (full path)
     *
     * @param string $fileName
     * @return Gettext
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;
        return $this;
    }

    /**
     * Set to view list of scanned files
     *
     * @return Gettext
     */
    public function setVerboseOn()
    {
        $this->verbose = true;
        return $this;
    }

    /**
     * Set Output Format
     *
     * @param string $outputFormat
     *
     * @return Gettext
     */
    public function setOutputFormat($outputFormat)
    {
        if(!in_array($outputFormat, self::$allowedOutpus)) {
            trigger_error("Output format have to be onr of " . implode(', ', self::$allowedOutpus), E_USER_ERROR);
        }

        $this->outputFormat = $outputFormat;
        return $this;
    }

    /**
     * Set Method prefixes to be searched for. String (one) or Array (multiple)
     *
     * @param string|array $methodPrefixes ex: '__t' or ['__', '__t']
     *
     * @return Gettext
     */
    public function setMethodPrefixes($methodPrefixes)
    {
        $this->methodPrefixes = is_array($methodPrefixes) ? $methodPrefixes : array($methodPrefixes);
        return $this;
    }
}
