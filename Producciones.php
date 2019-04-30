<?php

namespace PhpCompiler\Producciones;

const producciones = array(
    '<Programa>'            => array('<Sentencia>', 'PUNTOYCOMA', '<ProgramaFin>'),
    '<ProgramaFin>'         => array('<Sentencia>', 'PUNTOYCOMA', '<Programa>'),
    '<ProgramaFin>'         => array('EPSILON'),

);