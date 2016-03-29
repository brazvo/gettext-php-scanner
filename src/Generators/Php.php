<?php

namespace PhpScanner\Generators;

/**
 * Class Php generates .php file
 *
 * @package PhpScanner\Generators
 * @author  Branislav Zvolensky <brano@zvolensky.info>
 */
class Php extends AbstractGenerator implements GeneratorInterface
{
    /**
     * Create a file with prepared translations
     *
     * @return bool
     */
    public function create()
    {
        if (empty($this->lines)) {
            return false;
        }

        $arrayTemplate = "<?php\n\n
return array(\n
%s
);\n
";

        $oldData = array();
        if (file_exists($this->file)) {
            $oldData = include $this->file;
        }

        $rows = '';

        //handle existing rows
        foreach ($this->lines as $k => $line) {
            //Check if old key exists
            if (key_exists($line, $oldData)) {
                $rows .= "\t'{$line}' => '{$oldData[$line]}',\n";
            }
        }

        //handle new rows
        foreach ($this->lines as $k => $line) {
            //Check if old key exists
            if (!key_exists($line, $oldData)) {
                $rows .= "\t'{$line}' => '',\n";
            }
        }

        //save template to file
        $file = fopen($this->file, 'w+') or die('Could bot open file ' . $this->file);
        fwrite($file, sprintf($arrayTemplate, $rows));
        fclose($file);

        return true;
    }
}
