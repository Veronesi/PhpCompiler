<?php

namespace PhpCompiler\Producciones;

const producciones = array(
    '<Programa>'            => array('<Sentencia>', 'PUNTOYCOMA', '<ProgramaFin>'),
    
    '<ProgramaFin>'         => array('<Sentencia>', 'PUNTOYCOMA', '<Programa>'),
    '<ProgramaFin>'         => array('EPSILON'),
    
    '<Sentencia>'           => array('<Asignacion>'),
    '<Sentencia>'           => array('<Condicional>'),
    '<Sentencia>'           => array('<Ciclo>'),
    '<Sentencia>'           => array('<Lectura>'),
    '<Sentencia>'           => array('<Escritura>'),

    '<Asignacion>'          => array('ID', 'OPERADORASIGNACION', '<Expresion>'),
    
    '<Expresion>'           => array('<ExpresionAritmetica>'),
    '<Expresion>'           => array('<ExpresionLista>'),
    
    '<ExpresionAritmetica>' => array('<ExpresionAritmetica>', 'OPERADOR', '<ExpresionAritmetica>'),
    '<ExpresionAritmetica>' => array('ID', '<ExpresionAritmeticaFinal>'),
    '<ExpresionAritmetica>' => array('NUMERO', '<ExpresionAritmeticaFinal>'),
    '<ExpresionAritmetica>' => array('PARENTESIS_OPEN', '<ExpresionAritmetica>', 'PARENTESIS_CLOSE', '<ExpresionAritmeticaFinal>'),
    '<ExpresionAritmetica>' => array('FIRST', 'PARENTESIS_OPEN', '<ExpresionLista>', 'PARENTESIS_CLOSE', '<ExpresionAritmeticaFinal>'),
    
    '<ExpresionAritmeticaFinal>' => array('OPERADOR', '<ExpresionAritmetica>'),
    '<ExpresionAritmeticaFinal>' => array('EPSILON'),
    
    '<ExpresionLista>' => array('REST','PARENTESIS_OPEN', '<ExpresionLista>', 'PARENTESIS_CLOSE'),
    '<ExpresionLista>' => array('CONS','PARENTESIS_OPEN', '<ExpresionCons>', 'PARENTESIS_CLOSE'),
    '<ExpresionLista>' => array('<Lista>'),

    '<ExpresionCons>' => array('ID', 'COMA', '<ExpresionLista>'),
    '<ExpresionCons>' => array('NUMERO', 'COMA', '<ExpresionLista>'),
    
    '<Lista>' => array('ID'),
    '<Lista>' => array('CORCHETE_OPEN', '<ListaInterna>', 'CORCHETE_CLOSE'),
    
    '<ListaInterna>' => array('<ListaInterna>', 'COMA', '<ListaInternaNumero>'),
    '<ListaInterna>' => array('NUMERO'),

    '<ListaInternaNumero>' => array('NUMERO'),
    '<ListaInternaNumero>' => array('EPSILON'),

    '<Condicional>' => array('IF', '<Condicion>', 'LLAVE_OPEN', '<Programa>', '<CierreCondicion>'),
    
    '<CierreCondicion>' => array('LLAVE_CLOSE'),
    '<CierreCondicion>' => array('ELSE', 'LLAVE_OPEN', '<Programa>', 'LLAVE_CLOSE'),

    '<Ciclo>' => array('WHILE', '<Condicion>', 'LLAVE_OPEN', '<Programa>', 'LLAVE_CLOSE'),
    
    '<Condicion>' => array('<ExpresionAritmetica>', 'SIGNO', '<ExpresionAritmetica>'),
    '<Condicion>' => array('<Null>'),
    
    '<Lectura>' => array('<LecturaNumero>'),
    '<Lectura>' => array('<LecturaLista>'),
    
    '<Escritura>' => array('<EscribirNumero>'),
    '<Escritura>' => array('<EscribirLista>'),
    
    '<LecturaNumero>' => array('READINT', '<EscrituraLectura>'),
    '<LecturaLista>' => array('READLIST', '<EscrituraLectura>'),

    '<EscribirNumero>' => array('WRITEINT', '<EscrituraLectura>'),

    '<EscribirLista>' => array('WRITELIST', '<EscrituraLectura>'),

    '<EscrituraLectura>' => array('PARENTESIS_OPEN', '<Cadena>', 'ID', 'PARENTESIS_CLOSE'),

    '<Cadena>' => array('CADENA', 'COMA'),
    '<Cadena>' => array('EPSILON'),

    '<Null>' => array('NULL', 'PARENTESIS_OPEN', '<ExpresionLista>', 'PARENTESIS_CLOSE')
);