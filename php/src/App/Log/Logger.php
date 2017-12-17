<?php

namespace App\Log;

use App\Contracts\LoggerInterface;

class Logger implements LoggerInterface
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function write($text, $type = "")
    {
        $file  = $this->path . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';
        $stamp = date('Y-m-d H:i:s');
        $text  = "[{$stamp}]" . "[{$type}] {$text}\n";
        file_put_contents($file, $text, FILE_APPEND);
    }

}