<?php

namespace JscPhp\Configs;

use Exception;
use JscPhp\Configs\bin\Ini;
use JscPhp\Configs\bin\Json;
use JscPhp\Configs\bin\Xml;
use JscPhp\Configs\bin\Yaml;
use JscPhp\Configs\Types\Type;

class Config
{
    private Yaml|Json|Ini|Xml $parser;
    private array             $data    = [];
    private string            $file_path;
    private array             $options = [
        'autosave' => true,
    ];

    public function __construct(string $file_path, array $options = [])
    {
        $this->file_path = $file_path;
        $this->options = array_merge($this->options, $options);
        $extension = pathinfo($file_path, PATHINFO_EXTENSION) |>
                     strToLower(...);
        $this->parser = match ($extension) {
            'ini'         => new Ini($file_path),
            'json'        => new Json($file_path),
            'yaml', 'yml' => new Yaml($file_path),
            'xml'         => new Xml($file_path),
            default       => throw new Exception("Unsupported file extension: {$extension}")
        };
        if (file_exists($file_path)) {
            $this->data = $this->parser->parseFile();
        }
    }

    public function __destruct()
    {
        if ($this->options['autosave']) {
            $this->save();
        }
    }

    public function save(): false|int
    {
        $content = $this->parser->convertArray($this->data);
        return file_put_contents($this->file_path, $content);
    }

    public function saveAs(string $file_path, Type $type): false|int
    {
        $parser = match ($type) {
            Type::Ini  => new Ini($this->file_path),
            Type::Json => new Json($this->file_path),
            Type::Yaml => new Yaml($this->file_path),
            Type::Xml  => new Xml($this->file_path),
            default    => throw new Exception("Unsupported file type: {$type->name}")
        };
        $extension = pathinfo($file_path, PATHINFO_EXTENSION) |>
                     strtolower(...);
        if (!in_array($extension, $parser->getValidExtensions())) {
            throw new Exception("Invalid file extension: {$extension}");
        }
        $content = $parser->convertArray($this->data);
        return file_put_contents($file_path, $content);
    }

    public function delete(string $key): void
    {
        if (!isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function mergeData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    public function __get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function get(string ...$keys)
    {
        $working_array = $this->data;
        for ($i = 0; $i < count($keys); $i++) {
            $working_array = $working_array[$keys[$i]] ?? null;
        }
        return $working_array;
    }

    public function set(mixed $value, string ...$keys): void
    {
        if (count($keys) === 1) {
            $this->data[$keys[0]] = $value;
        } else {
            $working_array = &$this->data;
            for ($i = 0; $i < count($keys); $i++) {
                if ($i < (count($keys) - 1)) {
                    if (array_key_exists($keys[$i], $working_array)) {
                        $working_array = &$working_array[$keys[$i]];
                    } else {
                        $this->data[$keys[$i]] = [];
                        $working_array = &$this->data[$keys[$i]];
                    }
                } else {
                    $working_array[$keys[$i]] = $value;
                }
            }
        }

    }

}