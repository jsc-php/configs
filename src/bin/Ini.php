<?php

namespace JscPhp\Configs\bin;

class Ini extends Parser
{


    public function parseFile(): array
    {
        return parse_ini_file($this->file_path, true, INI_SCANNER_TYPED);
    }

    public function convertArray(array $data): string
    {
        return $this->arrayToIni($data);
    }

    private function arrayToIni(array $data): string
    {
        $root = '';
        $ret = '';
        $keys = array_keys($data);
        foreach ($keys as $key) {
            $current = $data[$key];
            if (is_array($current)) {
                if (array_is_list($current)) {
                    foreach ($current as $item) {
                        $root .= $key . '[] = ' . $this->formatValue($item) . PHP_EOL;
                    }
                } else {
                    $ret .= '[' . $key . ']' . PHP_EOL . $this->arrayToIni($current) . PHP_EOL;
                    $ret .= PHP_EOL;
                }
            } else {
                $root .= $key . ' = ' . $this->formatValue($current) . PHP_EOL;
            }
        }
        return rtrim($root . PHP_EOL . $ret);
    }

    private function formatValue($value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_numeric($value)) {
            return (string)$value;
        }

        if (is_string($value)) {
            // Quote strings that contain special characters or spaces
            if (preg_match('/[\W]/', $value)) {
                return '"' . $value . '"';
            }
            return $value;
        }

        return (string)$value;
    }

    public function getValidExtensions(): array
    {
        return ['ini'];
    }
}