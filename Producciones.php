<?php

namespace PhpCompiler\Producciones;
const producciones = array(
    array('<Programa>'                    => array('<Sentencia>', 'PUNTOYCOMA', '<ProgramaFin>')),
    array('<ProgramaFin>'                 => array('<Sentencia>', 'PUNTOYCOMA', '<Programa>')),
    array('<ProgramaFin>'                 => array('EPSILON')),
    array('<Programa>'                    => array('EPSILON')),
    array('<Sentencia>'                   => array('<Asignacion>')),
    array('<Sentencia>'                   => array('<Condicional>')),
    array('<Sentencia>'                   => array('<Ciclo>')),
    array('<Sentencia>'                   => array('<Lectura>')),
    array('<Sentencia>'                   => array('<Escritura>')),
    array('<Asignacion>'                  => array('ID', 'OPERADORASIGNACION', '<Expresion>')),
    array('<Expresion>'                   => array('<ExpresionAritmetica>')),
    array('<Expresion>'                   => array('<ExpresionLista>')),
    #array('<ExpresionAritmetica>'         => array('<ExpresionAritmetica>', 'OPERADOR', '<ExpresionAritmetica>')),
    array('<ExpresionAritmetica>'         => array('ID', '<ExpresionAritmeticaFinal>')),
    array('<ExpresionAritmetica>'         => array('NUMERO', '<ExpresionAritmeticaFinal>')),
    array('<ExpresionAritmetica>'         => array('PARENTESIS_OPEN', '<ExpresionAritmetica>', 'PARENTESIS_CLOSE', '<ExpresionAritmeticaFinal>')),
    array('<ExpresionAritmetica>'         => array('FIRST', 'PARENTESIS_OPEN', '<ExpresionLista>', 'PARENTESIS_CLOSE', '<ExpresionAritmeticaFinal>')),
    array('<ExpresionAritmeticaFinal>'    => array('OPERADOR', '<ExpresionAritmetica>')),
    array('<ExpresionAritmeticaFinal>'    => array('EPSILON')),
    array('<ExpresionLista>'              => array('REST','PARENTESIS_OPEN', '<ExpresionLista>', 'PARENTESIS_CLOSE')),
    array('<ExpresionLista>'              => array('CONS','PARENTESIS_OPEN', '<ExpresionCons>', 'PARENTESIS_CLOSE')),
    array('<ExpresionLista>'              => array('<Lista>')),
    array('<ExpresionCons>'               => array('ID', 'COMA', '<ExpresionLista>')),
    array('<ExpresionCons>'               => array('NUMERO', 'COMA', '<ExpresionLista>')),
    array('<Lista>'                       => array('ID')),
    array('<Lista>'                       => array('CORCHETE_OPEN', '<ListaInterna>', 'CORCHETE_CLOSE')),
    array('<ListaInterna>'                => array('<ListaInterna>', 'COMA', '<ListaInternaNumero>')),
    array('<ListaInterna>'                => array('NUMERO')),
    array('<ListaInternaNumero>'          => array('NUMERO')),
    array('<ListaInternaNumero>'          => array('EPSILON')),
    array('<Condicional>'                 => array('IF', '<Condicion>', 'LLAVE_OPEN', '<Programa>', '<CierreCondicion>')),
    array('<CierreCondicion>'             => array('LLAVE_CLOSE')),
    array('<CierreCondicion>'             => array('ELSE', 'LLAVE_OPEN', '<Programa>', 'LLAVE_CLOSE')),
    array('<Ciclo>'                       => array('WHILE', '<Condicion>', 'LLAVE_OPEN', '<Programa>', 'LLAVE_CLOSE')),
    array('<Condicion>'                   => array('<ExpresionAritmetica>', 'SIGNO', '<ExpresionAritmetica>')),
    array('<Condicion>'                   => array('<Null>')),
    array('<Lectura>'                     => array('<LecturaNumero>')),
    array('<Lectura>'                     => array('<LecturaLista>')),
    array('<Escritura>'                   => array('<EscribirNumero>')),
    array('<Escritura>'                   => array('<EscribirLista>')),
    array('<LecturaNumero>'               => array('READINT', '<EscrituraLectura>')),
    array('<LecturaLista>'                => array('READLIST', '<EscrituraLectura>')),
    array('<EscribirNumero>'              => array('WRITEINT', '<EscrituraLectura>')),
    array('<EscribirLista>'               => array('WRITELIST', '<EscrituraLectura>')),
    array('<EscrituraLectura>'            => array('PARENTESIS_OPEN', '<Cadena>', 'ID', 'PARENTESIS_CLOSE')),
    array('<Cadena>'                      => array('CADENA', 'COMA')),
    array('<Cadena>'                      => array('EPSILON')),
    array('<Null>'                        => array('NULL', 'PARENTESIS_OPEN', '<ExpresionLista>', 'PARENTESIS_CLOSE'))
);