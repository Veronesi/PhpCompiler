<?php
namespace PhpCompiler;

class Arbol{
    public $nodo;
    public $hijos;
    function __construct(string $nodo = "",array $hijos = array()){
        $this->nodo = $nodo;
        $this->hijos = $hijos;
    }
    public function CantidadHijos(): int{
        $count = 0;
        foreach ($this->hijos as $key => $hijo) {
            if(gettype($hijo) == "object"){
                $count += $hijo->CantidadHijos();
            }else{
                if($hijo != 'EPSILON')
                    $count++;
            }
        }
        return $count;       
    }
    public function DeleteEpsilon(int $posicion): int{
        $count = 0;
        foreach ($this->hijos as $keyH => $hijo) {
            if(gettype($hijo) == "object"){
                if($hijo->CantidadHijos() == 1 & $posicion = ($count + 1) ){
                    unset($this->hijos[$keyH]);
                }else{
                    $count += $hijo->DeleteEpsilon($posicion - $count);
                }
            }else{
                $contador++;
            }
         }
         return $count;
    }
    public function GetElemento(int $posicion){
        $contador = 0;
        while($contador <= $posicion){
            if(gettype($this->hijos[$contador]) == "object"){
                
                if($this->hijos[$contador]->CantidadHijos() > ($posicion - $contador)){
                    # Esta aca dentro
                    $nodo =  $this->hijos[$contador]->GetElemento($posicion - $contador);
                    return $nodo;
                }else
                    $posicion-= $this->hijos[$contador]->CantidadHijos() -1;
                    $contador++;
            }elseif($contador < $posicion){
                $contador++;
            }elseif($contador == $posicion){
                return $this->hijos[$contador];
            }
        }  
        #return 0;
        return "FULL_STACK";
    }


    public function SetChild(Arbol $nodo , int $posicion): int{
        $count = 0;
        foreach ($this->hijos as $key => $hijo) {
            if(gettype($hijo) == "object"){
                $count += $hijo->SetChild($nodo, ($posicion - $count));
            }else{
                if($count == $posicion){
                    $this->hijos[$count] = $nodo;
                }else
                    # Pasamos al proximo elemento.
                    $count++;
            }
        }
        return $count;
    }
    
    public function MostrarArbol($posicion = 0, $lineas = "",$cod = ""){
        print "\n".$cod.$this->nodo;
        $lineas2.= "│   ".$lineas;
        foreach ($this->hijos as $key => $hijo) {
            $cod = "├───";
            if($key == count($this->hijos) -1){
                $cod = "└───";
                $lineas2.= "   ".$lineas;
            }
            if(gettype($hijo) == "object"){
                $hijo->MostrarArbol($posicion++,$lineas2, $cod);
            }else{
                print "\n".$lineas.$cod.$hijo;
            }
        }
    }
}