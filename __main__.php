<?php
    include('Arbol.php');
    include('Producciones.php');
    include('Terminales.php');
    include('Variables.php');
    include_once('Color.php');

    use \PhpCompiler\Producciones;
    use \PhpCompiler\Terminales;
    use \PhpCompiler\Variables;
    use \PhpCompiler\Arbol;
    use \PhpCompiler\Color;

$command = array(
    '-al <file>'    => " Realiza un analisis lexico",
    '-as <file>'    => " Realiza un analisis sintactico",
    '-c  <file>'    => " Compliar archivo",
    '-d'            => " Modo debug (muestra paso a paso la compilacion)",
    '-f'            => " Fuerza a compilar un archivo",
    '-h'            => " Muestra una ayuda",
    '-p'            => " Muestra la lista de las producciones",
    '-r  <file>'    => " Ejecuta un archivo compilado",
    '-t'            => " Muestra la lista de terminales",
    '-v'            => " Muestra la lista de las variables",
    'foo="bar"'     => " Pasa como parametro una variable"

);

if(in_array("-h", $_SERVER['argv'])){
    foreach ($command as $keyC => $value) {
        print str_pad("\n   ".$keyC, 20).$value; 
    }
    print "\n\n   Para mas ayuda https://github.com/Veronesi/PhpCompiler\n";
}elseif(in_array("-t", $_SERVER['argv'])){
    foreach (\PhpCompiler\Terminales\terminales as $keyT => $value) {
        print "\n   ".$value; 
    }
    print "\n"; 
}elseif (in_array("-p", $_SERVER['argv'])) {
    foreach (\PhpCompiler\Producciones\producciones as $keyP => $value) {
        $arbol = new Arbol(key(\PhpCompiler\Producciones\producciones[$keyP]), $value[key($value)]);
        $arbol->MostrarArbol(); 
        print "\n\n"; 
    }    
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