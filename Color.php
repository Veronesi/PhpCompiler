<?php
namespace PhpCompiler;
class Color{
    private static $HEADER = "\033[95m";
    private static $OKBLUE = "\033[94m";
    private static $OKGREEN = "\033[92m";
    private static $WARNING = "\033[93m";
    private static $FAIL = "\033[91m";
    private static $ENDC = "\033[0m";
    private static $BOLD = "\033[1m";
    private static $UNDERLINE = "\033[4m";

    public static function Ok(string $str): string{
        return Color::$OKGREEN.$str.Color::$ENDC;
    }

    public static function Error(string $str): string{
        return Color::$FAIL.$str.Color::$ENDC; 
    }

    public static function Advertencia(string $str): string{
        return Color::$WARNING.$str.Color::$ENDC; 
    }
}
