<?php

namespace PhpScanner\Generators;

/**
 * Abstract Generator
 *
 * @package PhpScanner\Generators
 * @author  Branislav Zvolensky <brano@zvolensky.info>
 */
abstract class AbstractGenerator
{
    /**
     * Lines
     *
     * @var array
     */
    protected $lines;

    /**
     * Input/Output File Path
     *
     * @var string
     */
    protected $file;

    /**
     * Set Lines
     *
     * @param array $lines
     * @return AbstractGenerator
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
        return $this;
    }

    /**
     * Set input file
     *
     * @param string $file
     * @return AbstractGenerator
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }
}
