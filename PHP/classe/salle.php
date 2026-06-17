<?php


/*Class des Salles*/


class  Salle {

private $nomSalle;
private $capacite;




public function __construct($nomSalle, $capacite) {
    $this ->nomSalle = $nomSalle;
    $this ->capacite = $capacite;

}








//GETTERS:

public function getnomSalle(){
    return $this->nomSalle;
}


public function getcapacite(){
    return $this->capacite;
}



//SETTER :

public function setnomSalle($nomSalle) {
        $this->nomSalle = $nomSalle;
    }


public function setcapacite($capacite) {
        $this->capacite = $capacite;
    }









}



?>