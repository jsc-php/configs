<?php

namespace JscPhp\Configs;

use Exception;
use JscPhp\Configs\bin\Ini;

class Config
{
    private       $file_path;
    private       $parser;
    private array $data;

    private array $options = [
        'autosave' => true,
    ];

    public function __construct(string $file_path, array $options = [])
    {
        $this->options = array_merge($this->options, $options);
        $extension = pathinfo($file_path, PATHINFO_EXTENSION) |>
                     strToLower(...);
        $this->parser = match ($extension) {
            'ini'   => new Ini($file_path),
            default => throw new Exception("Unsupported file extension: {$extension}")
        };
        $this->data = $this->parser->parseFile();
    }

    public function __destruct()
    {
        if ($this->options['autosave']) {
            $this->parser->writeFile($this->data);
        }
    }

    public function save()
    {
        $this->parser->writeFile($this->data);
    }

    public function delete(string $key): void
    {

        if (!isset($this->data[$key])) {
            unset($this->data[$key]);
        }

    }

    public function __get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

}