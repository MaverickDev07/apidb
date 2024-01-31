<?php

use Dotenv\Dotenv;

class DownloadModel
{
  private $env;
  private $conexion;
  private $response;
  private $baseUrl;

  public function __construct()
  {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->response = new Response();
    $this->entorno = new Entorno();

    $dotenv = Dotenv::createImmutable(__DIR__.'/..');
    $env = $dotenv->load();

    $this->baseUrl = 'http://'.$_SERVER['HTTP_HOST'].'/'.$_ENV['API_DONBOSCO_FILES'];
  }

  /* Obtiene datos del estudiante */
  public function checkNotas($gestion=2021) {
    // $gestion = $this->entorno->getGestion();
    $conex = $this->pdo;
    $sql = "SELECT
      DISTINCT(PROF.id_prof),
      PROF.nombre,
      PROF.appaterno,
      PROF.apmaterno,
      ASP.codigo
      FROM asiginar_profesorm as ASP
      INNER JOIN profesores as PROF
      ON PROF.id_prof = ASP.id_prof
      WHERE
      codigo LIKE '%ST%' AND
      gestion = $gestion
      ORDER BY PROF.id_prof ASC";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $profesores = $query->fetchAll(PDO::FETCH_OBJ);
      $filesLink = array();
      foreach ($profesores as $key => $value) {
        $profesorNombre = $value->appaterno.' '.$value->apmaterno.' '.$value->nombre;
        $profesorNombreLink = str_replace(' ', "%20", $profesorNombre);
        $nivel = explode('-', $value->codigo, -1)[1];
        $trimestre = 'T3'; // MODIFICAR POR TRIMESTRE !IMPORTANTE
        $link = $this->baseUrl.'/teacher/'.$gestion.'/'.$nivel.'/'.$trimestre.'/'.$profesorNombreLink.'/NOTAS';
        array_push($filesLink, array(
          "id" => $value->id_prof,
          "profesorNombre" => $profesorNombre,
          "file" => $link,
        ));
      }
      $result = $filesLink;
      $message = "Listado encontrado";
      $status = true;
    } else {
      $result = null;
      $message = "No se encontraron profesores";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  /* TEST */
  public function checkAsignacion($gestion=2021) {
    $conex = $this->pdo;
    $sql = "SELECT
    `asiginar_profesorm`.`id_asg_prof`,
    `asiginar_profesorm`.`id_prof`,
    `asiginar_profesorm`.`gestion`
    FROM `asiginar_profesorm` WHERE `codigo` LIKE '%ST%' AND `gestion` = $gestion
    ORDER BY `asiginar_profesorm`.`id_asg_prof` ASC";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $profesores = $query->fetchAll(PDO::FETCH_OBJ);
      $filesLink = array();
      foreach ($profesores as $key => $value) {
        $queryRow = "UPDATE asiginar_profesorm SET id_prof='$value->id_prof' WHERE id_asg_prof='$value->id_asg_prof';";
        echo $queryRow.'<br>';
      }
      exit();
    }
  }

}
?>
