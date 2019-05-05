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
                                print "\nArbol nuevo:\n\n| | | | | | | | | | | | \n\n";
                                $arbolNuevo->MostrarArbol();
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
        self::ArrayToTree($resultado);

        # 1. Verificamos si el nodo raiz pertenece a S o puede generarse por el:
        $seguir = true;
        while($seguir){
            $seguir = false;
            # Verificamos si los nodos raices pertenecen a S y completamos los nodos que aun son variables.
            foreach ($resultado as $keyR => $unResultado) {
                $ArbolesRooteados = self::ForceRoot($unResultado);
                switch ($ArbolesRooteados) {
                    case 'IS_ROOT':

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
        self::ArrayToTree($resultado);

        # 2. Verificamos que sean todos Terminales y no quede ninguna Variable.
        $seguir = true;
        while($seguir){
            $seguir = false;
            # Verificamos si los hijos del arbol sean todos terminales.
            foreach ($resultado as $keyR => $unResultado) {
                $ArbolesTerminales = self::ForceTerminal($unResultado);
                switch ($ArbolesTerminales) {
                    case 'IS_TERMINALIZE':
                        break;
                    case 'NOT_TERMINALIZE':
                        unset($resultado[$keyR]); 
                    break;
                    default:
                    $resultado = array_merge_recursive($resultado, $ArbolesTerminales);
                    unset($resultado[$keyR]);
                    $seguir = true;
                        break;
                }
            }
        }
        print Color::Ok("\nTerminales: \n");
        self::ArrayToTree($resultado);      
    }

    public function ArrayToTree(array $array){
        foreach ($array as $keyA => $arbol) {
            print "\n\n| | | | | | | | | | | | \n\n";
            $arbol->MostrarArbol();
        }
    }

    public function ForceTerminal(Arbol $arbol){
        for ($pos=0; $pos < $arbol->CantidadHijos(); $pos++) { 
            $elem = $arbol->GetElemento($pos);
            if (in_array($elem, $this->V)){
                $return = array();
                foreach ($this->P as $keyP => $unaProduccion) {
                    if(key($unaProduccion) == $elem && !self::PoseeTerminales($unaProduccion[key($unaProduccion)])){
                        $arbolNuevo = unserialize(serialize($arbol));
                        $subArbol = new Arbol(key($unaProduccion) ,$unaProduccion[key($unaProduccion)]);
                        $arbolNuevo->SetChild($subArbol, $pos-2);
                        array_push($return, $arbolNuevo);
                    }
                }
                return (count($return) ? $return : 'NOT_TERMINALIZE');
            }
        }
        return "IS_TERMINALIZE";
    }

    public function ForceRoot(Arbol $arbol){
        if($arbol->nodo != $this->S){
            $return = array();
            # Buscamos quienes generan a la raiz.
            foreach ($this->P as $keyP => $unaProduccion){
                if($unaProduccion[key($unaProduccion)][0] == $arbol->nodo && !self::PoseeTerminales($unaProduccion[key($unaProduccion)])){
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
        if(count($childs) == 1 && $childs[0] == 'EPSILON')
            return false;
        $childs = array_diff($childs, array('EPSILON'));
        if(count(array_intersect($childs, $this->T)))
            return true;
        return false;
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
        self::ArrayToTree($return);
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
        self::ArrayToTree($return);
        return $return;
    }
}

$AnalizadorSintactico = new AnalizadorSintactico();
$AnalizadorSintactico->Analizar();