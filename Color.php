<?php
namespace PhpCompiler;
class Color{
    private static $HEADER = "\033[95m";
    private static $OKBLUE = "\033[96m";
    private static $EPSILON = "\033[90m";
    private static $OKGREEN = "\033[92m";
    private static $WARNING = "\033[93m";
    private static $FAIL = "\033[91m";
    private static $ENDC = "\033[0m";
    private static $BOLD = "\033[1m";
    private static $UNDERLINE = "\033[4m";
    private static $YELLOW = "\x1b[33m";
    public static function Ok(string $str): string{
        return Color::$OKGREEN.$str.Color::$ENDC;
    }

    public static function Error(string $str): string{
        return Color::$FAIL.$str.Color::$ENDC; 
    }

    public static function Advertencia(string $str): string{
        return Color::$WARNING.$str.Color::$ENDC; 
    }
    public static function Blue(string $str): string{
        return Color::$OKBLUE.$str.Color::$ENDC; 
    }
    public static function EPSILON(string $str): string{
        return Color::$EPSILON.$str.Color::$ENDC; 
    }
    public static function Yellow(string $str): string{
        return Color::$YELLOW.$str.Color::$ENDC; 
    }



}
