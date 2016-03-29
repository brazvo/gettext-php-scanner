<?php

namespace PhpScanner\Generators;

/**
 * GeneratorInterface - interface for generators
 *
 * @package PhpScanner\Generators
 * @author Branislav Zvolensky <brano@zvolensky.info>
 */
interface GeneratorInterface
{
    /**
     * Create a file with prepared translations
     *
     * @return bool
     */
    public function create();

    /**
     * Set Lines
     *
     * @param array $lines
     *
     * @return GeneratorInterface
     */
    public function setLines($lines);

    /**
     * Set Input/Output file
     *
     * @param string $file
     *
     * @return GeneratorInterface
     */
    public function setFile($file);
}