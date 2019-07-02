<?php
namespace PhpCompiler;

class Debug{
    public static function print(string $str){
        if(MODE_DEBUG) print $str;
    }
}