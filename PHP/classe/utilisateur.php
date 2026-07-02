<?php

class Utilisateurs {

    private $idUtilisateur;   
    private $email;
    private $password;
    private $idAssociation;
    private $role;
    

    public function __construct($idUtilisateur,$email,$password,$idAssociation,$role) {

        $this->idUtilisateur=$idUtilisateur;
        $this->email=$email;
        $this->password=$password;
        $this->idAssociation=$idAssociation;
        $this->role=$role;

    }


    public function getIdUtilisateur() {
        return $this->idUtilisateur;
    }


    public function setIdUtilisateur($idUtilisateur) {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password_get_info($password, PASSWORD_BCRYPT);
    }

    public function getIdAssociation() {
        return $this->association;
    }

    public function setIdAssociation($idAssociation) {
        $this->idAssociation = $idAssociation;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function verifierPassword($passwordSaisi) {
        return password_verify($passwordSaisi, $this->password);
    }

    public function roleMembre($role) {
        return $this->role === $role;
    }






}











?>