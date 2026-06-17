<?php


/*Class des associations*/


class  Association {

private $nomAssociation;
private $couleurAssociation;





 public function __construct($nomAssociation,$couleurAssociation) {
        $this->nomAssociation = $nomAssociation;
        $this->couleurAssociation = $couleurAssociation;
       

    }






//GETTERS:

public function getNomAssociation(){
    return $this->nomAssociation;
}



public function getcouleurAssociation(){
    return $this->couleurAssociation;
}





//SETTER :

public function setNomAssociation($NomAssociation) {
        $this->NomAssociation = $NomAssociation;
    }



public function setcouleurAssociation($couleurAssociation) {
        $this->couleurAssociation = $couleurAssociation;
    }


















}


?>