<?php
namespace PhpCompiler;

class AnalizadorSintactico{

    private $fileName;
    private $tokens;
    private $T;
    private $V;
    private $P;
    private $S;
    private $P2;
    
    function __construct(string $fileName){
        $this->fileName = $fileName;
        $file = fopen($fileName, 'r');
        $json = fread($file, filesize($fileName));
        fclose($file);
        # Tokens
        
        $this->tokens = json_decode($json);

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
            Debug::print("\n-------------------------------------------");
            Debug::print("\nToken a analizar: ".key($this->tokens[$i])." '".$this->tokens[$i]->{key($this->tokens[$i])}."'");
            self::ArrayToTree($resultado, true);
                # Recorremos las producciones obtenemos el i-esimo elemento del arbol.
                foreach ($resultado as $keyR => $unResultado) {
                    Debug::print("\n  - . - . - . - . -Arbol: $keyR - . - . - . - . - . -");
                    Debug::print("\nCantidad de hijos: ".$unResultado->CantidadHijos());
                    if($unResultado->CantidadHijos() -1 < $i){
                        # el arbol esta completo.
                        Debug::print(Color::Ok("\nEl arbol esta completo"));
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
                            Debug::print(Color::Error("\nLo eliminamos ya que nadie lo genera."));
                            if(count($resultado) == 1){
                                print("\n".Color::Advertencia("\nError de Analisis").": error sintactico, no se esperaba '".current($this->tokens[$i])."' en ".__DIR__."\\".$this->fileName." en Linea ".$this->tokens[$i]->line."\n");
                            }
                        }
                        # Eliminamos el arbol viejo.
                        Debug::print(Color::Error("\nSe a eliminado el arbol $keyR."));
                        unset($resultado[$keyR]);

                    }else{
                        $nodo = $unResultado->GetElemento($i);
                        Debug::print("\nNodo: $nodo");
                        # Verificamos que es.
                        if(in_array($nodo, $this->T)){
                            # Es un terminal.
                            Debug::print("\nEl nodo es un terminal");
                            # Verificamos si es distinto al token.
                            if($nodo != key($this->tokens[$i])){
                                Debug::print(Color::Error("\nNo coinciden los tokens"));
                                # Verificamos si era el ultimo posible arbol.
                                if(count($resultado) == 1)
                                    print("\n".Color::Advertencia("\nError de Analisis").": error sintactico, no se esperaba '".current($this->tokens[$i])."' en ".__DIR__."\\".$this->fileName." en Linea ".$this->tokens[$i]->line."\n");
                                    # Eliminamos el arbol.
                                unset($resultado[$keyR]);
                            }else
                                Debug::print(Color::Ok("\nCoinciden los tokens"));

                        }elseif(in_array($nodo, $this->V)){
                            # El nodo no es una Variable.
                            Debug::print("\nEl nodo es una Variable");
                            # Buscamos las producciones que generan a esta variable.
                            $subProducciones = self::Genera($nodo);
                            # Insertamos los subarboles en el arbol viejo
                            if(count($subProducciones) > 0){
                                foreach ($subProducciones as $keySP => $unaSubProduccion){
                                    $arbolNuevo = unserialize(serialize($unResultado));
                                    Debug::print("\nArbol nuevo: en pos ".($i)."\n\n");
                                    $arbolNuevo->SetChild($unaSubProduccion, $i);
                                    $arbolNuevo->MostrarArbol();
                                    array_push($resultado, $arbolNuevo);
                                }
                                # Eliminamos el arbol viejo.
                                unset($resultado[$keyR]);
                                $ProximoToken = false;
                            }else{
                                Debug::print(Color::Error("\nLo eliminamos ya que nadie lo genera."));
                                # Lo eliminamos ya que nadie lo genera.
                                unset($resultado[$keyR]);
                            }

                        }
                    }

                }
            if($ProximoToken)
                $i++;
        }
        Debug::print(Color::Blue("\n\nVerificamos si es generado por el simbolo inicial \n"));
        self::ArrayToTree($resultado, true);
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
        Debug::print(Color::Blue("\n\nCompletamos los arboles para que no quede ninguna Variable\n"));
        self::ArrayToTree($resultado, true);
        # 2. Verificamos que sean todos Terminales y no quede ninguna Variable.
        $seguir = true;
        while($seguir){
            $seguir = false;
            # Verificamos si los hijos del arbol sean todos terminales.
            foreach ($resultado as $keyR => $unResultado) {
                Debug::print("\n  - . - . - . - . -Arbol: $keyR - . - . - . - . - . -");
                self::ArrayToTree($resultado, true);
                $SubArbolesTerminales = self::ForceTerminal($unResultado);
                switch ($SubArbolesTerminales) {
                    case 'IS_TERMINALIZE':
                        break;
                    case 'NOT_TERMINALIZE':
                        unset($resultado[$keyR]);
                    break;
                    default:
                        # Insertamos los nuevos hijos en este arbol.
                        foreach ($SubArbolesTerminales as $keyS => $arbol) {
                            $arbolNuevo = unserialize(serialize($unResultado));
                            $arbolNuevo->SetChild($arbol, $unResultado->CantidadHijos() -1);
                            $arbolNuevo->MostrarArbol();
                            array_push($resultado, $arbolNuevo);
                        }
                        unset($resultado[$keyR]);
                        //$resultado = array_merge_recursive($resultado, $ArbolesTerminales);
                        $seguir = true;
                    break;
                }
            }
        }
        Debug::print(Color::Ok("\nArbol generador: \n"));
        self::ArrayToTree($resultado);
        if(count($resultado)){
            print "\n\nEl analisis Sintactico a finalizado con ".Color::Advertencia("0 advertencias");
            # Guardamos la lista de Tokens en un archivo
            $f = fopen(substr($this->fileName,0,-1)."3", 'w+');
            fwrite($f, serialize($resultado));
            fclose($f);
        }
            
        else
            print Color::Error("\nError en el analisis Sintactico");
    }

    public function ArrayToTree(array $array, bool $showKeyNumber = false){
        foreach ($array as $keyA => $arbol) {
            if($showKeyNumber)
                Debug::print("\n\nArbol $keyA):\n");
            else
                Debug::print("\n\n");
            $arbol->MostrarArbol();
        }
    }

    public function ForceTerminal(Arbol $arbol){
        # El unico elemento que puede ser una variable es el ultimo. 
        $elem = $arbol->GetElemento($arbol->CantidadHijos() -1);
        Debug::print("\nElemento a analizar: ".$elem." (Posicion: ".($arbol->CantidadHijos() -1).")"."\n");

        if (in_array($elem, $this->V)){
            Debug::print("\nLa siguiente produccion es una variable");
            # Verificamos si produce un terminal.
            $noTerminales = array();
            foreach ($this->P as $keyP => $unaProduccion) {
                if(key($unaProduccion) == $elem && !self::PoseeTerminales($unaProduccion[key($unaProduccion)])){
                    $hijo = New Arbol($elem,$unaProduccion[key($unaProduccion)]);
                    array_push($noTerminales, $hijo);
                }
            }
            if(count($noTerminales)){
                Debug::print(Color::Ok("\nProducciones que no generan terminales"));
                Debug::print("\n");
                self::ArrayToTree($noTerminales);
                return $noTerminales;
            }else{
                Debug::print(Color::Error("\nTodas las producciones generan terminales"));
                return 'NOT_TERMINALIZE';
            }
        }else{
            return 'IS_TERMINALIZE';
            Debug::print(Color::Ok("\nEs un terminal"));
        }
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
        Debug::print("\nEl elemento $token puede ser generador por:\n");
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
        Debug::print("\nEl elemento $token puede ser generador por:\n");
        self::ArrayToTree($return);
        return $return;
    }
}