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
        $produccionesPosibles = array();

        # Recorremos los Tokens
        for($i = 0; $i < count($this->tokens); $i++){
            # Primer elemento
            if($i == 0){
                foreach (self::GetGeneradores(key($this->tokens[0])) as $keyP => $Produccion) {
                    array_push($produccionesPosibles, $Produccion);
                }
            }else{
                # Recorremos las posibles producciones:
                foreach ($produccionesPosibles as $keyPp => $ProduccionPosible) {
                    $nodo = $ProduccionPosible->GetElemento($i);
                    # Si es un terminal:
                    if(in_array($nodo, $this->T)){
                        if($nodo == 'EPSILON'){
                            # Eliminamos el nodo que genera a EPSILON
                            $ProduccionPosible->DeleteEpsilon($i);
                            $i--;
                        }elseif($nodo != key($this->tokens[$i])){
                            if(count($produccionesPosibles) == 1){
                                print "\n".Color::Advertencia("Error de Analisis").": error sintactico, no se esperaba '".$this->tokens[$i][key($this->tokens[$i])]."' en ".__DIR__."\\codigoFuente.f"." en Linea ".$this->tokens[$i]['line']."\n";
                            }
                            unset($produccionesPosibles[$keyPp]);
                        }
                    # Si es una Variable.
                    }elseif(in_array($nodo, $this->V)){
                        foreach ($this->P as $keyP => $Produccion){
                            if(key($Produccion) == $nodo){
                                # Copiamos el arbol a reemplazar.
                                $arbolViejo = clone $ProduccionPosible;
                                $arbolViejo->SetChild(new Arbol($nodo, $Produccion[key($Produccion)]), $i);
                                array_push($produccionesPosibles, $arbolViejo);
                            }
                        }
                        # Eliminamos el arbol viejo
                        unset($produccionesPosibles[$keyPp]);

                        # Disminuimos en 1 $i para volver a verificarlo.
                        $i--;
                    }elseif($nodo == 0){
                        # Todos son terminales. 
                        # Buscamos que producen la raiz de los arbol.
                        foreach ($this->P as $keyP => $Produccion){
                            if($produccionesPosibles[$keyPp]->nodo == $Produccion[key($Produccion)][0]){
                                # Copiamos el arbol a reemplazar.
                                $arbolViejo = clone $ProduccionPosible;
                                $nuevaRaiz = new Arbol(key($Produccion), $Produccion[key($Produccion)]); 
                                $nuevaRaiz->SetChild($arbolViejo, 0);
                                array_push($produccionesPosibles, $nuevaRaiz);
                            }
                        }
                        # Eliminamos el arbol viejo
                        unset($produccionesPosibles[$keyPp]);

                        # Disminuimos en 1 $i para volver a verificarlo.
                        $i--;
                    }
                }
            }
        }
        #if(count($produccionesPosibles)) : print_r($produccionesPosibles); endif;
        print $produccionesPosibles[3]->MostrarArbol();
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