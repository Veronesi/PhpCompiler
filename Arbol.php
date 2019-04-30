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
            if(gettype($hijo) == "Arbol"){
                $count += $hijo->CantidadHijos();
            }else{
                $count++;
            }
        }
        return $count;       
    }
    public function DeleteEpsilon(int $posicion): int{
        $count = 0;
        foreach ($this->hijos as $keyH => $hijo) {
            if(gettype($hijo) == "Arbol"){
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
        return 0;
    }


    public function SetChild(Arbol $nodo , int $posicion): int{
        $count = 0;
        foreach ($this->hijos as $key => $hijo) {
            if(gettype($hijo) == "Arbol"){
                $count += $hijo->SetChild($nodo, ($posicion - $count));
            }else{
                if($count == $posicion)
                    $this->hijos[$count] = $nodo;
                else
                    # Pasamos al proximo elemento.
                    $count++;
            }
        }
        return $count;
    }
}