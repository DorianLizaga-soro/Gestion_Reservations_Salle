<?php

require_once("PHP/classe/association.php");
require_once("PHP/classe/salle.php");
require_once("PHP/classe/reservation.php");











/*associations*/


$Association1=new Association("Club de Tennis Monestié","Bleu");
$Association2=new Association("Association Culturelle Les Arts","Vert");
$Association3=new Association("Amicale des Retraités","Orange");
$Association4=new Association("Club de Pétanque du Terroir","Violet");
$Association5=new Association("Danse Traditionnelle Occitane","Rose");
$Association6=new Association("Association Parents d'Élèves","BleuClair");
$Association7=new Association("Chorale Voix du Sud","Jaune");
$Association8=new Association("Club Informatique Senior","Rouge");
$Association9=new Association("Yoga & Bien-être","VertClair");


$Associations=array($Association1, $Association2, $Association3, $Association4, $Association5, $Association5, $Association6, $Association7, $Association8, $Association9);

print_r($Associations);









/*reservations*/














/*salles*/


$Salle1=new Salle("Salle de reunion","50");
$Salle2=new Salle("Bar","50");
$Salle3=new Salle("Réfectoire ","50");



$Salles=array($Salle1, $Salle2, $Salle3);













?>