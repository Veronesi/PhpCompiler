<?php
    include('Color.php');
    include('ExpReg.php');
    
class AnalizadorLexico{
    private $errores;
    private $fileName;
    private $row;
    private $texto;
    private $tokens;
    private $posicion;
    private $palabrasReservadas;
    private $listo;
    private $caracter;
    private $advertencias;
    function __construct(){
        $this->fileName = 'codigoFuente.f';
        $this->tokens = array();
        $this->posicion = 0;
        $this->advertencias = 0;
        $this->errores = 0;
        $this->listo = "";
        $this->palabrasReservadas = [
            'REST',
            'CONS',
            'FIRST',
            'IF',
            'ELSE',
            'WHILE',
            'READINT',
            'READLIST',
            'WRITEINT',
            'WRITELIST'
        ];
        $this->caracter = array(
            '.'   =>  'PUNTO',
            ','   =>  'COMA',
            ';'   =>  'PUNTOYCOMA',
            '['   =>  'CORCHETE_OPEN',
            ']'   =>  'CORCHETE_CLOSE',
            '('   =>  'PARENTESIS_OPEN',
            ')'   =>  'PARENTESIS_CLOSE',
            '{'   =>  'LLAVE_OPEN',
            '}'   =>  'LLAVE_CLOSE',
            '+'   =>  'OPERADOR',
            '-'   =>  'OPERADOR',
            '*'   =>  'OPERADOR',
            '/'   =>  'OPERADOR',
            '='   =>  'OPERADORASIGNACION',
            '=='  => 'SIGNO',
            '<'   =>  'SIGNO',
            '>'   =>  'SIGNO',
            '=<'  => 'SIGNO',
            '=>'  => 'SIGNO',
            '<='  => 'SIGNO',
            '>='  => 'SIGNO'
        );
    }

    public function Analizar(): void{
        $file = fopen($this->fileName, 'r');
        $this->row = 1;
        while(!feof($file)){
            $this->texto = trim(fgets($file));
            $this->posicion = 0;
            if($this->texto[0] != "#"){
                while(strlen($this->texto) > $this->posicion){
                    # Recortamos la cadena.
                    $subTexto = substr($this->texto, $this->posicion);
        
                    # Verificamos si hay un espacio en blanco al inicio de la cadena.
                    preg_match("/^\s+/",$subTexto, $cantidadDeEspacios);
                    $this->posicion+=strlen($cantidadDeEspacios[0]);
                    $subTexto = substr($subTexto, strlen($cantidadDeEspacios[0]));
        
                    # Buscamos la primera palabra.
                    preg_match(ExpReg::e(),$subTexto, $palabraEncontrada);
                    $palabra = $palabraEncontrada[0];
                    $this->posicion+= strlen($palabra);
                    self::GetToken($palabra);
                }
            }
            $this->row++;
        }
        fclose($file);
        # Boramos espacios en blancos.
        $this->listo = trim($this->listo);
        print "\nEl analisis Lexico a finalizado con ";
        if(!$this->errores){
            $f = fopen($this->fileName."2", 'w+');
            fwrite($f, json_encode($this->tokens));
            fclose($f);
            $msg = " advertencia";
            if ($this->advertencias != 1) : $msg.= "s"; endif;
            print Color::Advertencia($this->advertencias.$msg);
        }else{
            $msg = " error";
            if ($this->errores > 1) : $msg.= "es"; endif;
            print Color::Error($this->errores.$msg);
        }
    }

    private function GetToken(string $palabra): bool{
        
        # Palabra reservada.
        if(in_array(strtoupper($palabra), $this->palabrasReservadas)){
            array_push($this->tokens, array(strtoupper($palabra) => strtolower($palabra), 'line' => $this->row));
            $this->listo.= " ".strtoupper($palabra);
            return true;
        }

        # Identificador.
        if(preg_match(ExpReg::Id(), $palabra)){
            array_push($this->tokens, array('ID' => $palabra, 'line' => $this->row));
            $this->listo.= " ".'ID';
            return true;
        }

        # Cadena.
        if(preg_match(ExpReg::Cadena(), $palabra)){
            array_push($this->tokens, array('CADENA' => $palabra, 'line' => $this->row));
            $this->listo.= " ".'CADENA';
            return true;
        }   

        # Numero.
        if(preg_match(ExpReg::Numero(), $palabra)){
            array_push($this->tokens, array('NUMERO' => $palabra, 'line' => $this->row));
            $this->listo.= " ".'NUMERO';
            return true;
        }

        # Caracter.
        if(array_key_exists(strtoupper($palabra), $this->caracter)){
            array_push($this->tokens, array($this->caracter[$palabra] => $palabra, 'line' => $this->row));
            $this->listo.= " ".$this->caracter[$palabra];
            return true;
        }
        print "\n".Color::Advertencia("Error de Analisis").": error lexico, no se esperaba '".$palabra."' en ".__DIR__.$this->fileName." en Linea ".$this->row;
        print "\n";
        $line = readline("Desea eliminarlo y continuar con el analisis? [Y/n]: ");
        if($line == "n"){
            $this->errores++;
        }else{
            $this->advertencias++;
        }
        return false;
    }
}
if($_SERVER['argv'][1] == '-C'){
    if($_SERVER['argv'][2]){
        if(file_exists(__DIR__."\\".$_SERVER['argv'][2])){
            $AnalizadorLexico = new AnalizadorLexico();
            $AnalizadorLexico->Analizar();
        }else{
            print "Error: no se a encontrado el archivo ".__DIR__."\\".$_SERVER['argv'][2];
        }
    }else{
        print "Falta el nombre del archivo a copilar";
    }
}else{
    print "Por favor ejecuta: \"php ".$_SERVER['argv'][0]." -C NombreDelArchivo.f\"";
}

?>
