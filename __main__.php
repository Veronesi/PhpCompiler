<?php
$command = array(
    '-al <file>'    => " Realiza un analisis lexico",
    '-as <file>'    => " Realiza un analisis sintactico",
    '-c  <file>'     => " Compliar archivo",
    '-d'            => " Modo debug (muestra paso a paso la compilacion)",
    '-h'            => " Muestra una ayuda",
    '-p'            => " Muestra la lista de las producciones",
    '-r  <file>'     => " Ejecuta un archivo compilado",
    '-t'            => " Muestra la lista de terminales",
    '-v'            => " Muestra la lista de las variables",

);

if(in_array("-h", $_SERVER['argv'])){
    foreach ($command as $keyC => $value) {
        print str_pad("\n   ".$keyC, 20).$value; 
    }
    print "\n\n   Para mas ayuda https://github.com/Veronesi/PhpCompiler\n";
}

/*
if($_SERVER['argv'][1] == '-C'){
    if($_SERVER['argv'][2]){
        if(file_exists(__DIR__."\\".$_SERVER['argv'][2])){
            $AnalizadorLexico = new AnalizadorLexico();
            $AnalizadorLexico->Analizar();
        }else{
            print "Error: no se a encontrado el archivo ".__DIR__."/".$_SERVER['argv'][2];
        }
    }else{
        print "Falta el nombre del archivo a copilar";
    }
}else{
    print "Por favor ejecuta: \"php ".$_SERVER['argv'][0]." -C NombreDelArchivo.f\"";
}
*/
?>