<?php
namespace PhpCompiler;

class Debug{
    public static function print(string $str, $see){
        if($see) print $str;
    }
}