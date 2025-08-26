<?php


class FileHandler
{
    protected array $file;
    protected string $destinationPath;
    protected array $allowedTypes;
    protected int $maxSize;

    public function __construct($file)
    {
        $this->file = $file;
        $this->allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $this->maxSize = 5 * 1024 * 1024;
    }


    public function setDestinationPath($path)
    {
        $this->destinationPath = $path;
        return $this;
    }

    public function setAllowedTypes($types)
    {
        $this->allowedTypes = $types;
        return $this;
    }

    public function setMaxSize($size)
    {
        $this->maxSize = $size;
        return $this;
    }


    
}

?>