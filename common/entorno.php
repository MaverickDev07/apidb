<?php

class Entorno
{
  private $conexion;
  private $response;

  /* ROLES */
  private $ADMINISTRADOR = "ADMINISTRADOR";
  private $DIRECTOR = "DIRECTOR";
  private $PROFESOR = "PROFESOR";
  private $SECRETARIA = "SECRETARIA";
  private $KARDIXTA = "KARDIXTA";
  private $INSCRIPTOR = "INSCRIPTOR";

  /* TRIMESTRES */
  private $PRIMER_TRIMESTRE = 5;
  private $SEGUNDO_TRIMESTRE = 6;
  private $TERCER_TRIMESTRE = 7;

  /* NIVELES */
  private $PRIMARIA_TARDE = 1;
  private $SECUNDARIA_TARDE = 2;
  private $PRIMARIA_MANIANA = 3;
  private $SECUNDARIA_MANIANA = 4;

  /* GESTION */
  private $GESTION = 2021;

  public function __construct() {
  }

  public function getRolAdministrador() {
    return $this->ADMINISTRADOR;
  }

  public function getRolDirector() {
    return $this->DIRECTOR;
  }

  public function getRolProfesor() {
    return $this->PROFESOR;
  }

  public function getRolSecretaria() {
    return $this->SECRETARIA;
  }

  public function getRolKardixta() {
    return $this->KARDIXTA;
  }

  public function getRolInscriptor() {
    return $this->INSCRIPTOR;
  }

  public function getPrimerTrimestre() {
    return $this->PRIMER_TRIMESTRE;
  }

  public function getSegundoTrimestre() {
    return $this->SEGUNDO_TRIMESTRE;
  }

  public function getTercerTrimestre() {
    return $this->TERCER_TRIMESTRE;
  }

  public function getNivelID($nivel) {
    if($nivel == "PT") {
      return $this->PRIMARIA_TARDE;
    }
    if($nivel == "ST") {
      return $this->SECUNDARIA_TARDE;
    }
    if($nivel == "PM") {
      return $this->PRIMARIA_MANIANA;
    }
    if($nivel == "SM") {
      return $this->SECUNDARIA_MANIANA;
    }
  }

  public function getGestion() {
    return $this->GESTION;
  }

}
?>
