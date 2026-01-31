<?php

namespace JscPhp\Configs\bin;

class Json extends Parser
{

    public function parseFile(): array
    {
        $json = file_get_contents($this->file_path);
        return json_decode($json, true);
    }

    public function writeFile(array $data): void
    {
        $json = json_encode($data);
        $this->_write($json);
    }
}