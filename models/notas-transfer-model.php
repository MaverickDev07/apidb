<?php

class NotasTransferModel
{
  private $conexion;
  private $response;

  public function __construct() {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->response = new Response();
    /* Boletines Instance */
    $this->boletin = new BoletinesModel();
    $this->entorno = new Entorno();
  }
  /* Obtiene Datos de un Estudiante */
  public function getEstudiante($id_est, $gestion=2021) {
    // $gestion = $this->entorno->getGestion();
    $conex = $this->pdo;
    $sql = "SELECT
      EST.id_est,
      EST.rude,
      EST.ci,
      EST.nombre,
      EST.appaterno,
      EST.apmaterno,
      EST.genero,
      EST.inscrito,
      NP.codigo,
      NP.boletin,
      NP.id_not_pro,
      RN.fnacimiento as fecha_nacimiento
    FROM estudiantes as EST
    INNER JOIN nota_prom as NP
    ON EST.id_est = NP.id_est
    INNER JOIN reguistro_nacimiento as RN
    ON NP.id_est = RN.id_est
    WHERE EST.id_est = $id_est
    AND NP.gestion = $gestion";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result =  $query->fetchObject();
      $codigo = explode('-', $result->codigo);
      $result->ci = trim($result->ci);
      $result->cursoTurno = array(
        "curso_sigla" => $codigo[0],
        "turno_sigla" => $codigo[1],
        "curso_nombre" => checkCurso($codigo[0]), // helper
        "turno_nombre" => checkTurno($codigo[1]), // helper
        "nivel_nombre" => checkGrado($codigo[1]) // helper
      );
      $message = 'Estudiante encontrado';
      $status = true;
    } else {
      $result = -1;
      $message = "El estudiante no existe";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  /* Obtiene Profesores asignados a un curso */
  public function getProfesoresCurso($curso, $turno, $gestion=2021) {
    $cursoTurno = $curso.'-'.$turno;
    // $gestion = $this->entorno->getGestion();
    $conex = $this->pdo;
    $sql = "SELECT
      ASP.id_asg_prof,
      AM.id_mat,
      PROF.id_prof,
      PROF.nombre,
      PROF.appaterno,
      PROF.apmaterno,
      ASP.codigo,
      ASP.gestion
      FROM asiginar_profesorm as ASP
      INNER JOIN asiginar_materiacu as AM
      ON ASP.id_asg_mate = AM.id_asg_mate
      INNER JOIN profesores as PROF
      ON ASP.id_prof = PROF.id_prof
      WHERE
      AM.codigo LIKE CONCAT('%','$cursoTurno','%') AND
      ASP.gestion = $gestion
      ORDER BY AM.id_mat ASC";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      return $query->fetchAll(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  }

  /* Buscar Estudiante por turno y curso */
  public function buscarEstudiante($turno = "", $curso = "", $studentName = "", $gestion=2021) {
    $conex = $this->pdo;
    /* Validamos parametros para realizar la condicion */
    $where = array("NP.gestion = $gestion");
    $orderBy = "";
    $concat = "";
    if($turno !== "all") {
      if($curso !== "all") {
        array_push($where, "NP.codigo LIKE CONCAT('%','$curso','-','$turno','%')");
      } else {
        array_push($where, "NP.codigo LIKE CONCAT('%','$turno','%')");
      }
      $orderBy = "busqueda.appaterno, busqueda.apmaterno, busqueda.nombre";
    }
    if($studentName !== "" ) {
      if(is_numeric($studentName)) {
        array_push($where, "CAST(busqueda.info as CHAR) LIKE '$studentName%'");
        $concat = "CONCAT(codigo, ' ', appaterno, ' ', apmaterno, ' ', nombre)";
      } else {
        array_push($where, "busqueda.info LIKE '%$studentName%'");
        $concat = "CONCAT(appaterno, ' ', apmaterno, ' ', nombre)";
      }
      $orderBy = "NP.codigo";
    } else {
      $concat = "CONCAT(appaterno, ' ', apmaterno, ' ', nombre)";
    }

    $whereQuery = implode(" AND ", $where);

    $sql = "SELECT
      DISTINCT(busqueda.id_est),
      busqueda.ci,
      busqueda.nombre,
      busqueda.appaterno,
      busqueda.apmaterno,
      busqueda.genero,
      busqueda.inscrito,
      busqueda.codigo as codigo_pago,
      NP.codigo,
      NP.boletin,
      NP.id_not_pro,
      RN.fnacimiento as fecha_nacimiento
      FROM (
      SELECT 
        id_est,
        ci,
        nombre,
        appaterno,
        apmaterno,
        codigo,
        genero,
        inscrito,
        $concat as info
      FROM estudiantes
    ) as busqueda
    INNER JOIN nota_prom as NP
    ON busqueda.id_est = NP.id_est
    INNER JOIN reguistro_nacimiento as RN
    ON NP.id_est = RN.id_est
    WHERE $whereQuery
    ORDER BY $orderBy";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $resultFilter = $query->fetchAll(PDO::FETCH_OBJ);
      $result = array();
      foreach ($resultFilter as $key => $value) {
        $codigo = explode('-', $value->codigo);
        $value->ci = trim($value->ci);
        if($value->boletin == 0) {
          $value->boletin = true;
        } else {
          $value->boletin = false;
        }
        $value->cursoTurno = array(
          "curso_sigla" => $codigo[0],
          "turno_sigla" => $codigo[1],
          "curso_nombre" => checkCurso($codigo[0]), // helper
          "turno_nombre" => checkTurno($codigo[1]), // helper
          "nivel_nombre" => checkGrado($codigo[1]) // helper
        );
        array_push($result, $value);
      }
      $message = 'Lista';
      $status = true;
    } else {
      $result = -1;
      $message = "No hay resultados";
      $status = false;
    }

    return $this->response->send($result, $status, $message, []);
  }

  /* Agrega los profesores a las materias */
  private function joinProfesoresMaterias($profesores, $materias) {
    $join = array();
    foreach ($materias as $key => $materia) {
      $profesorMateria = array();
      foreach ($profesores as $key => $profesor) {
        if($materia['id_mat'] === $profesor["id_mat"]) {
          $profesorMateria[] = $profesor;
        }
      }
      $materiaJoin = $materia;
      $materiaJoin["profesores"] = $profesorMateria;
      $join[] = $materiaJoin;
    }
    return $join;
  }

  /* Obtiene las notas de un estudiante con las materias asignadas y profesores asignados de un trimestre */
  public function getEstudianteNotaTrimestre($id_est, $curso, $turno, $trimestre, $gestion) {
    $materias = $this->boletin->getCamposAreasMaterias($curso, $turno);
    $notas = $this->boletin->getNotasEstudiante($id_est, $curso, $turno, $gestion, $trimestre);
    $notasFilter = $this->boletin->filterNotas($materias, $notas); // Filtramos las notas
    $notasFilterErrores = $this->boletin->filterErrores($notasFilter, $curso, $turno); // Filtramos errores de asignacion
    $notasFilterEspecialidad = $this->boletin->filterEspecialidad($notasFilterErrores, $curso, $turno); // Filtramos materias de especialidad
    $profesores = $this->getProfesoresCurso($curso, $turno, $gestion);
    $result = $this->joinProfesoresMaterias($profesores, $notasFilterEspecialidad);
    $status = true;
    $message = "Notas encontradas";
    return $this->response->send($result, $status, $message, []);
  }

  /* Guarda la nota del estudiante */
  public function saveNotaMateria($data, $gestion=2021) {
    // $gestion = $this->entorno->getGestion();
    $id_bi = $data->id_bi;
    $id_area = $data->id_area;
    $id_mat = $data->id_mat;
    $id_prof = $data->id_prof;
    $id_asg_prof = $data->id_asg_prof;
    $id_est = $data->id_est;
    $cod_curso = $data->cod_curso;
    $cod_nivel = $data->cod_nivel;
    $total = $data->total;
  }

}
?>
