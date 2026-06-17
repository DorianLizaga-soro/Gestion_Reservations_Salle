<?php



/*Class des reservations*/


class Reservation {


private $date;
private $heureDebut;
private $heureFin;
private $association;
private $type;
private $salle;
private $responsable;
private $statut;





public function __construct($date, $heureDebut, $heureFin, $association, $type, $salle, $responsable, $statut) {
    
   
    $this ->date = $date;
    $this ->heureDebut = $heureDebut;
    $this ->heureFin = $heureFin;
    $this ->association = $association;
    $this ->type = $type;
    $this ->salle = $salle;
    $this ->responsable = $responsable;
    $this ->statut = $statut;
    

}






//GETTERS:


public function getdate(){
    return $this->date;
}


public function getheureDebut(){
    return $this->heureDebut;
}

public function getheureFin(){
    return $this->heureFin;
}


public function getassociation(){
    return $this->association;
}


public function gettype(){
    return $this->type;
}


public function getsalle(){
    return $this->salle;
}


public function getresponsable(){
    return $this->responsable;
}


public function getstatut(){
    return $this->statut;
}








//SETTER :


public function setdate($date) {
        $this->date = $date;
    }


public function setheureDebut($heureDebut) {
        $this->heureDebut = $heureDebut;
    }


public function setheureFin($heureFin) {
        $this->heureFin = $heureFin;
    }


public function setassociation($association) {
        $this->association = $association;
    }


public function settype($type) {
        $this->type = $type;
    }


public function setsalle($salle) {
        $this->salle = $salle;
    }


public function setresponsable($responsable) {
        $this->responsable = $responsable;
    }


public function setstatut($statut) {
        $this->statut = $statut;
    }








}




?>