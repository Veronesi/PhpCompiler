<?php
namespace PhpCompiler;

    include_once('Color.php');
    include('ExpReg.php');
    include('Caracter.php');
    include('PalabrasReservadas.php');

    use \PhpCompiler\Color;
    use \PhpCompiler\ExpReg;
    use \PhpCompiler\Caracteres;
    use \PhpCompiler\PalabrasReservadas;

class AnalizadorLexico{

    private $errores;
    private $fileName;
    private $row;
    private $texto;
    private $tokens;
    private $posicion;
    private $palabrasReservadas;
    private $caracter;
    private $advertencias;

    function __construct(string $fileName){
        $this->fileName = $fileName;
        $this->tokens = array();
        $this->posicion = 0;
        $this->advertencias = 0;
        $this->errores = 0;
        $this->palabrasReservadas = PalabrasReservadas\palabrasReservadas;
        $this->caracter = Caracteres\caracter;
    }

    public function Analizar(): void{
        # Archivo en donde se encuentra el codigo Fuente
        $file = fopen($this->fileName, 'r');
        $this->row = 1;
        
        # Recorremos el archivo linea por linea
        while(!feof($file)){
            $this->texto = trim(fgets($file));
            $this->posicion = 0;
            
            # Omitimos la linea si es un comentario
            if($this->texto[0] != "#"){
                while(strlen($this->texto) > $this->posicion){
                    # Recortamos la cadena.
                    $subTexto = substr($this->texto, $this->posicion);
        
                    # Verificamos si hay un espacio en blanco al inicio de la cadena.
                    if(preg_match("/^\s+/",$subTexto, $cantidadDeEspacios)){
                        $this->posicion+=strlen($cantidadDeEspacios[0]);
                        $subTexto = substr($subTexto, strlen($cantidadDeEspacios[0]));
                    }

                    # Buscamos la primera palabra.
                    preg_match(ExpReg\ExpReg::e(),$subTexto, $palabraEncontrada);
                    $palabra = $palabraEncontrada[0];
                    $this->posicion+= strlen($palabra);
                    self::GetToken($palabra);
                }
            }
            $this->row++;
        }
        fclose($file);

        print "\nEl analisis Lexico a finalizado con ";
        if(!$this->errores){

            # Guardamos la lista de Tokens en un archivo
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
    /**
     * Obtiene el token asociado a una palabra
     * Gauarda el token el la lista del mismo
     * @param string $palabra: 
     */
    private function GetToken(string $palabra): void{
        # Palabra reservada.
        if(in_array(strtoupper($palabra), $this->palabrasReservadas))
            array_push($this->tokens, array(strtoupper($palabra) => strtolower($palabra), 'line' => $this->row));
        
        # Identificador.
        elseif(preg_match(ExpReg\ExpReg::Id(), $palabra))
            array_push($this->tokens, array('ID' => $palabra, 'line' => $this->row));
        
        # Cadena.
        elseif(preg_match(ExpReg\ExpReg::Cadena(), $palabra))
            array_push($this->tokens, array('CADENA' => $palabra, 'line' => $this->row));

        # Numero.
        elseif(preg_match(ExpReg\ExpReg::Numero(), $palabra))
            array_push($this->tokens, array('NUMERO' => $palabra, 'line' => $this->row));

        # Caracter.
        elseif(array_key_exists(strtoupper($palabra), $this->caracter))
            array_push($this->tokens, array($this->caracter[$palabra] => $palabra, 'line' => $this->row));
        
        else{
            print "\n".Color::Advertencia("Error de Analisis").": error lexico, no se esperaba '".$palabra."' en ".__DIR__.$this->fileName." en Linea ".$this->row;
            print "\n";
            $line = readline("Desea eliminarlo y continuar con el analisis? [Y/n]: ");
            if($line == "n")
                $this->errores++;
            else
                $this->advertencias++;
        }
    }
}
?>
