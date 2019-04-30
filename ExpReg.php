<?php
class ExpReg{
    PUBLIC static function Id(): string{
        return "/^[a-zA-Z]\w*/";
    } 
    PUBLIC static function Cadena(): string{
        return "/^(?:\"(?:\w+|\s|[\.\+-\:\*])*\")/";
    } 
    PUBLIC static function Numero(): string{
        return "/^\d+/";
    } 

    PUBLIC static function Operador(): string{
        return "/^[+\-/*]/";
    }    
    PUBLIC static function e(): string{
        return "/^((?:[a-zA-Z]\w*)|(?:\"(?:\w+|\s|[\.\+-\:\*])*\")|(?:\d+)|(?:\[(?:\,|\s|\d+)*\])|(?:[<>=]{1}=|=[<>=]{1}|\W))/";
    }
}