<?php

    include_once('Arbol.php');
    include_once('Producciones.php');
    include_once('Terminales.php');
    include_once('Variables.php');
    include_once('Color.php');
    include_once('AnalizadorLexico.php');
    include_once('AnalizadorSintactico.php');
    include_once('ExpReg.php');
    include_once('Caracter.php');
    include_once('Debug.php');
    include_once('PalabrasReservadas.php');

    use \PhpCompiler\Producciones;
    use \PhpCompiler\Terminales;
    use \PhpCompiler\Variables;
    use \PhpCompiler\Arbol;
    use \PhpCompiler\Color;
    use \PhpCompiler\AnalizadorLexico;
    use \PhpCompiler\AnalizadorSintactico;
    use \PhpCompiler\ExpReg;
    use \PhpCompiler\Caracteres;
    use \PhpCompiler\Debug;
    use \PhpCompiler\PalabrasReservadas;

$command = array(
    '-al <file>'     => " Realiza un analisis lexico",
    '-as <file>'    => " Realiza un analisis sintactico",
    '-c  <file>'    => " Compliar archivo",
    '-d'            => " Modo debug (muestra paso a paso la compilacion)",
    '-f'            => " Fuerza a compilar un archivo",
    '-h'            => " Muestra una ayuda",
    '-p'            => " Muestra la lista de las producciones",
    '-r  <file>'    => " Ejecuta un archivo compilado",
    '-t'            => " Muestra la lista de terminales",
    '-v'            => " Muestra la lista de las variables",
    '-y'            => " Acepta todas las preguntas que el complidaor hara",
    'foo="bar"'     => " Pasa como parametro una variable"
);

$commands = ['-al', '-as', 'c', '-d', '-f', '-h', '-p', '-r', '-t', '-v', '-y'];

if(in_array("-h", $_SERVER['argv'])){
    foreach ($command as $keyC => $value) {
        print str_pad("\n   ".$keyC, 20).$value; 
    }
    print "\n\n   Para mas ayuda https://github.com/Veronesi/PhpCompiler \n";
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
}elseif(in_array("-al", $_SERVER['argv'])){
    $cmd = getFileName('f');
    if ($cmd){
        if(file_exists($cmd)){
            $yes = false;
            if(in_array("-y", $_SERVER['argv'])) : $yes = true; endif;
            $AnalizadorLexico = new AnalizadorLexico($cmd);
            $AnalizadorLexico->Analizar($yes);
        }else{
            $fileLev = getLevenshtein($cmd, scandir(__DIR__));
            print Color::Error("   No se a podido abrir el archivo: $cmd");
            if($fileLev == "..") : print ""; else: print "\n   Quizas intentaste escribir ".$fileLev; endif;
        }
    }else
        print Color::Error("   Se esperaba como parametro el nombre del archivo con extencion .f")."\n   php ".$_SERVER['argv'][0]." -cl <file>";
}elseif(in_array("-as", $_SERVER['argv'])){
    $cmd = getFileName('f2');
    if ($cmd){
        if(file_exists($cmd)){
            if(in_array("-d", $_SERVER['argv']))
                define("MODE_DEBUG", true); 
            else
                define("MODE_DEBUG", false);
            $AnalizadorSintactico = new AnalizadorSintactico($cmd);
            $AnalizadorSintactico->Analizar();
        }else{
            $fileLev = getLevenshtein($cmd, scandir(__DIR__));
            print Color::Error("   No se a podido abrir el archivo: $cmd");
            if($fileLev == "..") : print ""; else: print "\n   Quizas intentaste escribir ".$fileLev; endif;
        }
    }else
        print Color::Error("   Se esperaba como parametro el nombre del archivo con extencion .f2")."\n   php ".$_SERVER['argv'][0]." -cl <file>";
}elseif(in_array("-c", $_SERVER['argv'])){
    $cmd = getFileName('f');
    if ($cmd){
        if(file_exists($cmd)){
            $yes = false;
            if(in_array("-y", $_SERVER['argv'])) : $yes = true; endif;
            $AnalizadorLexico = new AnalizadorLexico($cmd);
            if($AnalizadorLexico->Analizar($yes)){
                $debug = false;
                if(in_array("-d", $_SERVER['argv'])) : $debug = true; endif;
                $AnalizadorSintactico = new AnalizadorSintactico($cmd."2", $debug);
                $AnalizadorSintactico->Analizar(); 
            }
        }else{
            $fileLev = getLevenshtein($cmd, scandir(__DIR__));
            print Color::Error("   No se a podido abrir el archivo: $cmd");
            if($fileLev == "..") : print ""; else: print "\n   Quizas intentaste escribir ".$fileLev; endif;
        }
    }else
        print Color::Error("   Se esperaba como parametro el nombre del archivo con extencion .f")."\n   php ".$_SERVER['argv'][0]." -cl <file>";
}

function getFileName($ext){
    foreach ($_SERVER['argv'] as $key => $value) {
        if(preg_match('/^\w+\.'.$ext.'$/', $value) && $value != $_SERVER['argv'][0])
            return $value;
    }
    
}

function getLevenshtein(string $ask,array $words): string{
    $shortest = -1;
    foreach ($words as $word) {
        $lev = levenshtein($ask, $word);
        if ($lev <= $shortest || $shortest < 0) {
            // establece la coincidencia más cercana y la distancia más corta
            $closest  = $word;
            $shortest = $lev;
        }
    }
    return $closest;
}
?>


