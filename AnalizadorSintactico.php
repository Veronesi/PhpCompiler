<?php
namespace PhpCompiler;
    include('Arbol.php');
    include('Color.php');
    include('Producciones.php');
    include('Terminales.php');
    include('Variables.php');

    use \PhpCompiler\Producciones;
    use \PhpCompiler\Terminales;
    use \PhpCompiler\Variables;
class AnalizadorSintactico{

    private $tokens;
    private $T;
    private $V;
    private $P;
    private $S;
    private $P2;
    function __construct(){

        # Tokens
        $this->tokens = array();
        array_push($this->tokens, array('ID' => 'b', 'line' => 3));
        array_push($this->tokens, array('OPERADORASIGNACION' => '=', 'line' => 3));
        array_push($this->tokens, array('NUMERO' => '3', 'line' => 4));
        array_push($this->tokens, array('PUNTOYCOMA' => ';', 'line' => 4));
        
        # Conjunto finito de terminales
        $this->T = \PhpCompiler\Terminales\terminales;
        
        # Conjunto finito de variables
        $this->V = \PhpCompiler\Variables\variables;
        
        # Símbolo inicial
        $this->S = "<Programa>";

        # Conjunto finito de producciones
        $this->P = array();
        /*
        array_push($this->P, array('<Programa>' => array('<Asignacion>', 'PUNTOYCOMA')));
        array_push($this->P, array('<Asignacion>' => array('ID', 'OPERADORASIGNACION', '<ExpresionAritmetica>')));
        array_push($this->P, array('<ExpresionAritmetica>' => array('NUMERO')));
        array_push($this->P, array('<ExpresionAritmetica>' => array('ID')));
        */
        foreach (\PhpCompiler\Producciones\producciones as $key => $value) {
            array_push($this->P, $value);
        }
    }

    public function Analizar(){
        # Lista de las producciones que posiblemente puedan generar a Tokens
        $resultado = array();
        $i = 1;

        # Es el primer elemento
        $resultado = self::Generadores(key($this->tokens[0]));

        while($i< count($this->tokens)){
            $ProximoToken = true;
            print "\n-------------------------------------------";
            print "\nToken a analizar: ".key($this->tokens[$i]);
            
                # Recorremos las producciones obtenemos el i-esimo elemento del arbol.
                foreach ($resultado as $keyR => $unResultado) {
                    $nodo = $unResultado->GetElemento($i);
                    print "\n  - . - . - . - . - $keyR - . - . - . - . - . -";
                    print "\nNodo: $nodo";
                    print "\nCantidad de hijos: ".$unResultado->CantidadHijos();
                    if($unResultado->CantidadHijos() -1 < $i){
                        # el arbol esta completo.
                        print Color::Ok("\nEl arbol esta completo");
                        # Buscamos quienes producen a la raiz del arbol.
                        $nuevasRaices = self::Generadores($unResultado->nodo);
                        if(count($nuevasRaices) > 0){
                            foreach ($nuevasRaices as $keyRz => $raiz) {
                                # Insertamos el arbol viejo en el primer hijo de la nueva raiz.
                                $arbolNuevo = $raiz;
                                $arbolNuevo->SetChild(clone $unResultado, 0);  
                                array_push($resultado, $arbolNuevo);
                            }
                            $ProximoToken = false;
                        }else{
                            print Color::Error("\nLo eliminamos ya que nadie lo genera.");
                        }
                        # Eliminamos el arbol viejo.
                        print Color::Error("\nSe a eliminado el arbol $keyR.");
                        unset($resultado[$keyR]);

                    }
                    # Verificamos que es.
                    elseif(in_array($nodo, $this->T)){
                        # Es un terminal.
                        print "\nEl nodo es un terminal";
                        # Verificamos si es distinto al token.
                        if($nodo != key($this->tokens[$i])){
                            print Color::Error("\nNo coinciden los tokens");
                            # Verificamos si era el ultimo posible arbol.
                            if(count($resultado) == 1)
                                print "\n".Color::Advertencia("\nError de Analisis").": error sintactico, no se esperaba '".$this->tokens[$i][key($this->tokens[$i])]."' en ".__DIR__."\\codigoFuente.f"." en Linea ".$this->tokens[$i]['line']."\n";
                            # Eliminamos el arbol.
                            unset($resultado[$keyR]);
                        }else
                            print Color::Ok("\nCoinciden los tokens");

                    }elseif(in_array($nodo, $this->V)){
                        # El nodo no es una Variable.
                        print "\nEl nodo es una Variable";
                        # Buscamos las producciones que generan a esta variable.
                        $subProducciones = self::Genera($nodo);
                        # Insertamos los subarboles en el arbol viejo
                        if(count($subProducciones) > 0){
                            foreach ($subProducciones as $keySP => $unaSubProduccion){
                                $arbolNuevo = unserialize(serialize($unResultado));
                                print "\nArbol nuevo:\n:";
                                print_r($arbolNuevo);
                                $arbolNuevo->SetChild($unaSubProduccion, $i);
                                array_push($resultado, $arbolNuevo);
                            }
                            # Eliminamos el arbol viejo.
                            unset($resultado[$keyR]);
                            $ProximoToken = false;
                        }else{
                            print Color::Error("\nLo eliminamos ya que nadie lo genera.");
                            # Lo eliminamos ya que nadie lo genera.
                            unset($resultado[$keyR]);
                        }

                    }
                }
            if($ProximoToken)
                $i++;
        }
        print Color::Ok("\nEl arbol generador es:\n");
        print_r($resultado);

        # 1. Verificamos si el nodo raiz pertenece a S o puede generarse por el:
        $seguir = true;
        while($seguir){
            $seguir = false;
            # Verificamos si los nodos raices pertenecen a S y completamos los nodos que aun son variables.
            foreach ($resultado as $keyR => $unResultado) {
                $ArbolesRooteados = self::ForceRoot($unResultado);
                switch ($ArbolesRooteados) {
                    case 'IS_ROOT':
                        #array_push($resultado, $unResultado);
                        break;
                    case 'NOT_ROOTEABLE':
                        unset($resultado[$keyR]); 
                    break;
                    default:
                    $resultado = array_merge_recursive($resultado, $ArbolesRooteados);
                    $seguir = true;
                        break;
                }
            }
        }
        print Color::Ok("\nRooteados: \n");
        print_r($resultado);
    }

    public function ForceRoot(Arbol $arbol){
        if($arbol->nodo != $this->S){
            $return = array();
            # Buscamos quienes generan a la raiz.
            foreach ($this->P as $keyP => $unaProduccion){
                if($unaProduccion[key($unaProduccion)][0] == $arbol->nodo && self::PoseeTerminales($unaProduccion[key($unaProduccion)])){
                    $arbolNuevo = $unaProduccion;
                    $arbolNuevo->SetChild(unserialize(serialize($arbol)), 0);
                    array_push($return, $arbolNuevo);
                }
            }
            return (count($return) ? $return : 'NOT_ROOTEABLE'); 
        }else
            return "IS_ROOT";
    }

    public function PoseeTerminales(array $childs): bool{
        # Eliminamos las ocurrencias de los Epsilon.
        $childs = array_diff($childs, array('EPSILON'));
        if(count(array_intersect($childs, $this->T)))
            return false;
        return true;
    }

    public function Genera(string $token): array{
        $return = array();

        # Recorremos las Producciones.
        foreach ($this->P as $keyP => $unaProduccion) {

            # Verificamos si la variable es igual a la key.
            if($token == key($unaProduccion))
                array_push($return, new Arbol(key($unaProduccion), $unaProduccion[key($unaProduccion)]));
        }
        print "\nEl elemento $token puede ser generador por:\n";
        var_dump($return);
        return $return;
    }    
    public function Generadores(string $token): array{
        $return = array();

        # Recorremos las Producciones.
        foreach ($this->P as $keyP => $unaProduccion) {

            # Verificamos si el primer elemento es igual al token.
            if($token == $unaProduccion[key($unaProduccion)][0])
                array_push($return, new Arbol(key($unaProduccion), $unaProduccion[key($unaProduccion)]));
        }
        print "\nEl elemento $token puede ser generador por:\n";
        var_dump($return);
        return $return;
    }

    public function GetGeneradores(string $token): array{
        $generadores = array();
        # Recorremos las produciones.
        foreach ($this->P as $keyP => $produccion) {
            # Verificamos cuales empiezan con este token.
            if($produccion[key($produccion)][0] == $token)
            {
                # Verificamos si puede producirse mediante <Programa>
                $condicion = false;
                self::GenerateByS(key($produccion), $condicion);
                if($condicion)
                    array_push($generadores, new Arbol(key($produccion), $produccion[key($produccion)]));
            }
        }
        return $generadores;
    }
    /**
     * @var string $elemento 
     * @var bool &$esGenerado
     */
        public function GenerateByS(string $elemento, bool &$esGenerado): void{
        if($elemento == $this->S)
            $esGenerado = true;
        else{
            foreach ($this->P as $keyP => $produccion){
                if($produccion[key($produccion)][0] == $elemento)
                    self::GenerateByS(key($produccion), $esGenerado);
            }
        }
    }
}

$AnalizadorSintactico = new AnalizadorSintactico();
$AnalizadorSintactico->Analizar();