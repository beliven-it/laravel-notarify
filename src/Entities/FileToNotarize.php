<?php

namespace Beliven\Notarify\Entities;

use Illuminate\Support\Facades\Storage;

class FileToNotarize
{
    protected $path;

    protected $size;

    protected string $fileName;

    protected string $type;

    protected string $algorithm;

    protected $content;

    protected string $hash;

    protected $metaData;

    public function __construct(protected string $filePath)
    {
        if (! Storage::exists($filePath)) {
            throw new \Exception('File does not exist');
        }

        $this->init($filePath);
    }

    public function __get($property)
    {
        return $this->{$property};
    }

    private function init($filePath, $algorithm = 'sha256')
    {
        $this->path = $filePath;
        $this->algorithm = $algorithm;
        $this->size = Storage::size($this->path);
        $this->fileName = basename($this->path);
        $this->type = Storage::mimeType($this->path);
        $this->content = Storage::get($this->path);
        $this->hash = $this->getHashByAlgorithm($this->algorithm);
        $this->metaData = $this->setMetaData($this->fileName, $this->size, $this->type, $this->hash);
    }

    private function setMetaData($fileName, $size, $type, $hash)
    {
        return [
            'filename' => $fileName,
            'size' => $size,
            'type' => $type,
            'hash' => $hash,
        ];
    }

    private function getHashByAlgorithm()
    {
        switch ($this->algorithm) {
            case 'sha256':
                return hash('sha256', $this->content);
            default:
                throw new \Exception('Algorithm not supported');
        }
    }
}
