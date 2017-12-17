<?php

namespace App\Contracts;

interface LoggerInterface
{
    /**
     * @param string $text
     * @param string $type
     * @return void
     */
    public function write($text, $type = '');
}