<?php

class SelectorModel
{
  private $conexion;
  private $response;

  public function __construct()
  {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->response = new Response();
  }

  private function selectorResponse($result = [])
  {
    $resultResponse = [];
    $nivelCurrent = 0;
    $nivelIterator = -1;
    foreach ($result as $key => $value) {
      if($value->id_nivel != $nivelCurrent) {
        $nivelCurrent = $value->id_nivel;
        $nivelIterator = $nivelIterator + 1;
        $resultResponse[$nivelIterator] = array(
          "id_nivel" => $value->id_nivel,
          "nivel_nombre" => $value->nivel_nombre,
          "nivel_turno" => $value->nivel_turno,
          "nivel_codigo" => $value->nivel_codigo,
          "nivel_colegio" => $value->colegio_nombre,
          "nivel_cursos" => array(),
        );
      }
      array_push($resultResponse[$nivelIterator]["nivel_cursos"], array(
        "curso_nombre" => $value->curso_nombre,
        "curso_codigo" => $value->curso_codigo,
      ));
    }
    return $resultResponse;
  }

  public function getSelectorAll()
  {
    $conex = $this->pdo;
    $sql = "SELECT
      NC.id_nivel,
      N.codigo as nivel_codigo,
      N.nivel as nivel_nombre,
      N.turno as nivel_turno,
      C.codigo as curso_codigo,
      C.nombre as curso_nombre,
      COL.nombre as colegio_nombre
    FROM nivel_curso as NC
    INNER JOIN cursos as C
    ON NC.cod_curso = C.codigo
    INNER JOIN niveles as N
    ON NC.id_nivel = N.id_nivel
    INNER JOIN colegio as COL
    ON N.id_col = COL.id_col
    WHERE NC.activo = 1
    ORDER BY NC.id_nivel, C.id_curso ASC";
    $queryUser = $conex->prepare($sql);
    $queryUser->execute();
    if ($queryUser->rowCount() != 0) {
      $resultUsers = $queryUser->fetchAll(PDO::FETCH_OBJ);
      $result = $this->selectorResponse($resultUsers);
      $message = 'Lista';
      $status = true;
    } else {
      $result = -1;
      $message = "No hay Usuarios";
      $status = false;
    }

    return $this->response->send($result, $status, $message, []);
  }
}
?>
