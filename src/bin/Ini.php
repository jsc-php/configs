<?php

namespace JscPhp\Configs\bin;

class Ini extends Parser
{
    public function writeFile(array $data): void
    {
        if (!is_writable($this->file_path)) {
            throw new \Exception("{$this->file_path} not writable");
        }
        $ini = $this->arrayToIni($data);
        $this->_write($ini);
    }

    private function arrayToIni(array $data): string
    {
        $result = '';

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Add section header
                $result .= "[$key]\n";

                // Add key-value pairs within the section
                foreach ($value as $subKey => $subValue) {
                    if (is_array($subValue)) {
                        // Handle array values (multi-value keys)
                        foreach ($subValue as $item) {
                            $result .= $subKey . '[] = ' . $this->formatValue($item) . "\n";
                        }
                    } else {
                        $result .= $subKey . ' = ' . $this->formatValue($subValue) . "\n";
                    }
                }

                $result .= "\n";
            } else {
                // Top-level key-value pairs (before any section)
                $result .= $key . ' = ' . $this->formatValue($value) . "\n";
            }
        }

        return rtrim($result);
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

    public function parseFile(): array
    {
        return parse_ini_file($this->file_path, true, INI_SCANNER_TYPED);
    }

}