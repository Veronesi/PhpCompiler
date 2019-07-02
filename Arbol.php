<?php
namespace PhpCompiler;

include_once('color.php');
include_once('Debug.php');
#fix it
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
        $ninterno = 0;
        foreach ($this->hijos as $key => $hijo) {
            if(gettype($hijo) == "object"){
                $ninterno++;
                $count += $hijo->SetChild($nodo, ($posicion - $count));
            }else{
                if($count == $posicion){
                    $this->hijos[$ninterno] = $nodo;
                }else
                    $ninterno++;
                    # Pasamos al proximo elemento.
                    if($hijo != 'EPSILON')
                        $count++;
            }
        }
        return $count;
    }

    public function MostrarArbol($nivel = 0, $cuerpo = ""){

        Debug::print(Color::Blue($this->nodo));
        foreach ($this->hijos as $keyH => $hijo) {
            if(gettype($hijo) == "object"){
                if($keyH == (count($this->hijos) -1) ){
                    Debug::print("\n".$cuerpo."└───");
                    $hijo->MostrarArbol($nivel + 1,$cuerpo."    ");
                }
                else{
                    Debug::print("\n".$cuerpo."├───");
                    $hijo->MostrarArbol($nivel + 1,$cuerpo."│   ");
                }
            }else{
                if(preg_match('/\</',$hijo)){
                    if($keyH == (count($this->hijos) -1) )
                        Debug::print("\n".$cuerpo."└───".Color::Advertencia($hijo));
                    else
                        Debug::print("\n".$cuerpo."├───".Color::Advertencia($hijo));
                }else{
                    if($hijo == 'EPSILON'){
                        if($keyH == (count($this->hijos) -1) )
                        Debug::print("\n".$cuerpo."└───".Color::EPSILON($hijo));
                    else
                        Debug::print("\n".$cuerpo."├───".Color::EPSILON($hijo));
                    }else{
                        if($keyH == (count($this->hijos) -1) )
                        Debug::print("\n".$cuerpo."└───".Color::Yellow($hijo));
                    else
                        Debug::print("\n".$cuerpo."├───".Color::Yellow($hijo));
                    }
                }
            }
        }
    }
}