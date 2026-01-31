<?php

namespace JscPhp\Configs\bin;

abstract class Parser
{
    protected $file_path;

    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
    }


    public abstract function parseFile(): array;

    public abstract function writeFile(array $data): void;

    protected function _write(string $data): void
    {
        file_put_contents($this->file_path, $data);
    }

}