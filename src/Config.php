<?php

namespace JscPhp\Configs;

use Exception;
use JscPhp\Configs\bin\Ini;
use JscPhp\Configs\bin\Json;
use JscPhp\Configs\bin\Yaml;
use JscPhp\Configs\Types\Type;

class Config
{
    private Yaml|Json|Ini $parser;
    private array $data;

    private string $file_path;

    private array $options = [
        'autosave' => true,
    ];

    public function __construct(string $file_path, array $options = [])
    {
        $this->file_path = $file_path;
        $this->options = array_merge($this->options, $options);
        $extension = pathinfo($file_path, PATHINFO_EXTENSION) |>
                strToLower(...);
        $this->parser = match ($extension) {
            'ini' => new Ini($file_path),
            'json' => new Json($file_path),
            'yaml' => new Yaml($file_path),
            default => throw new Exception("Unsupported file extension: {$extension}")
        };
        if (file_exists($file_path) && is_readable($file_path)) {
            $this->data = $this->parser->parseFile();
        }
    }

    public function __destruct()
    {
        if ($this->options['autosave']) {
            $this->save();
        }
    }

    public function save()
    {
        $this->parser->writeFile($this->data);
    }

    public function saveAs(Type $type)
    {
        $parser = match ($type) {
            Type::Ini => new Ini($this->file_path),
            Type::Json => new Json($this->file_path),
            Type::Yaml => new Yaml($this->file_path),
            default => throw new Exception("Unsupported file type: {$type->name}")
        };
        $parser->writeFile($this->data);
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