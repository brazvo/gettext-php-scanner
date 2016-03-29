<?php

namespace PhpScanner\Generators;

/**
 * Class Po generates .po file
 *
 * @package PhpScanner\Generators
 * @author  Branislav Zvolensky <brano@zvolensky.info>
 */
class Po extends AbstractGenerator implements GeneratorInterface
{
    /**
     * Create a file with prepared translations
     *
     * @return bool
     */
    public function create()
    {
        if (count($this->lines) < 1) {
            return false;
        }

        //Get the old content
        $old_content = '';
        if (file_exists($this->file)) {
            $old_content = file_get_contents($this->file);
        }

        //Open the file and append on it or create it if not there
        $file = fopen($this->file, 'a+') or die('Could bot open file ' . $this->file);
        foreach ($this->lines as $k => $line) {
            //Check to see if the line was in the file
            if (preg_match('~' . preg_quote($line) . '~', $old_content, $matches)) {
                continue;
            }

            fwrite($file, 'msgid "' . $line . '"' . "\n" . 'msgstr ""' . "\n\n");
        }
        fclose($file);

        return true;
    }
}
