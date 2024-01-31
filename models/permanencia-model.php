<?php

class PermanenciaModel
{
  private $conexion;
  private $response;

  public function __construct()
  {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->response = new Response();
    $this->entorno = new Entorno();
  }

  /* Crear una reserva de cupos */
  public function create($data, $gestion = 2022)
  {
    $id_not_pro = $data->id_not_pro;
    $gestion_permanencia = $data->gestion_permanencia;
    /* Resultados Default */
    $result = -1;
    $status = false;

    if ($this->verifyCreateEnabled($id_not_pro, $gestion)) {
      if (!$this->verifyCreateRepeat($id_not_pro, $gestion_permanencia)) {
        $estudiante_nombre = strtoupper($data->estudiante_nombre);
        $estudiante_appaterno = strtoupper($data->estudiante_appaterno);
        $estudiante_apmaterno = strtoupper($data->estudiante_apmaterno);
        $tutor_nombre = strtoupper($data->tutor_nombre);
        $tutor_ci = strtoupper($data->tutor_ci);
        $tutor_celular = strtoupper($data->tutor_celular);
        $curso_actual = strtoupper($data->curso_actual);
        $curso_proximo = strtoupper($data->curso_proximo);
        $turno_actual = strtoupper($data->turno_actual);
        $turno_proximo = $data->turno_proximo;

        if ($turno_actual == "PM" || $turno_actual == "SM") {
          $conex = $this->pdo;
          $sql = "INSERT INTO
            permanencia_form
            VALUES (
              NULL,
              \"$id_not_pro\",
              \"$estudiante_nombre\",
              \"$estudiante_appaterno\",
              \"$estudiante_apmaterno\",
              \"$tutor_nombre\",
              \"$tutor_ci\",
              \"$tutor_celular\",
              \"$curso_actual\",
              \"$turno_actual\",
              \"$curso_proximo\",
              \"$turno_proximo\",
              \"$gestion_permanencia\",
              CURRENT_TIMESTAMP
            )
          ";
          $query = $conex->prepare($sql);
          $idInsert = $query->execute();
          if ($query->rowCount() != 0) {
            $result = $conex->lastInsertId();
            $message = 'Registrado con exito';
            $status = true;
          } else {
            $message = 'Ocurrio un error';
          }
        } else {
          $message = "Formulario es solo válido para el Turno de la mañana";
        }
      } else {
        /* Ya esta registrado */
        $message = "Su permanecia ya esta registrada";
      }
    } else {
      /* No tiene habilitado el boletin, no pago, por eso no se puede registrar */
      $message = "No esta habilitado, comuniquese con el contador.";
    }
    return $this->response->send($result, $status, $message, []);
  }

  private function getNextCourse($curso, $turno)
  {
    $cursoCurrent = intval($curso[0]);
    $turnoCurrent = $turno[0];
    $cursoNext = $cursoCurrent;
    $turnoNext = $turnoCurrent;
    if ($cursoCurrent < 6) {
      /* Si es menor de 6, procedemos a subir de curso, en el mismo turno*/
      $cursoNext = $cursoCurrent + 1;
    } else {
      /* Verificamos el turno (Primaria o Secundaria) */
      if ($turnoCurrent === "P") {
        /* Si es primaria, le pasamos a secundaria */
        $turnoNext = "S";
        $cursoNext = 1;
      }
    }
    return (object) array(
      "curso" => $cursoNext . $curso[1], // curso + seccion
      "turno" => $turnoNext . $turno[1], // turno + (mañana/tarde)
    );
  }

  /* Obtener datos del estudiante */
  public function getEstudiante($ci, $gestion = 2022)
  {
    $conex = $this->pdo;
    //$gestion = $this->entorno->getGestion();
    $sql = "SELECT
    NP.id_not_pro,
    NP.codigo,
    EST.id_est,
    EST.nombre as estudiante_nombre,
    EST.appaterno as estudiante_appaterno,
    EST.apmaterno as estudiante_apmaterno,
    EST.ci as estudiante_ci,
    EST.codigo as codigo_pago,
    REG.tipo,
    PAD.nombre,
    PAD.appaterno,
    PAD.apmaterno
    FROM estudiantes as EST
    INNER JOIN nota_prom as NP
    ON NP.id_est = EST.id_est
    INNER JOIN reguistro_tutor as REG
    ON REG.id_est = EST.id_est
    INNER JOIN padres as PAD
    ON PAD.id = REG.id_padre
    WHERE NP.boletin=0 AND NP.gestion = $gestion AND EST.ci = '$ci'";
    $query = $conex->prepare($sql);
    $query->execute();

    if ($query->rowCount() != 0) {
      $resultData = $query->fetchAll(PDO::FETCH_OBJ);
      $padres = array();
      $estudiante = array();
      $cursoTurno = array();
      $id_not_pro = 0;
      $codigo = '';
      foreach ($resultData as $key => $value) {
        if ($key === 0) {
          $id_not_pro = $value->id_not_pro;
          $codigo = $value->codigo;
          /* Datos del estudiante */
          $estudiante = array(
            "nombre" => $value->estudiante_nombre,
            "appaterno" => $value->estudiante_appaterno,
            "apmaterno" => $value->estudiante_apmaterno,
            "ci" => $value->estudiante_ci,
          );
          /* Datos del curso actual */
          $codigoExplode = explode('-', $codigo);
          $cursoTurno = array(
            "curso_sigla" => $codigoExplode[0],
            "turno_sigla" => $codigoExplode[1],
            "curso_nombre" => checkCurso($codigoExplode[0]), // helper
            "turno_nombre" => checkTurno($codigoExplode[1]), // helper
            "grado_nombre" => checkGrado($codigoExplode[1]) // helper
          );
          /* Datos del siguiente curso */
          $codigoNext = $this->getNextCourse($codigoExplode[0], $codigoExplode[1]);
          $cursoTurnoProximo = array(
            "curso_sigla" => $codigoNext->curso,
            "turno_sigla" => $codigoNext->turno,
            "curso_nombre" => checkCurso($codigoNext->curso), // helper
            "turno_nombre" => checkTurno($codigoNext->turno), // helper
            "grado_nombre" => checkGrado($codigoNext->turno) // helper
          );
        }
        $padres[$value->tipo] = trim($value->appaterno . ' ' . $value->apmaterno . ' ' . $value->nombre);
      }
      $gestion_permanencia = $gestion + 1;

      if ($this->verifyEnabled($codigo)) {
        $result = array(
          "id_not_pro" => $id_not_pro,
          "estudiante" => $estudiante,
          "curso_turno" => $cursoTurno,
          "curso_turno_proximo" => $cursoTurnoProximo,
          "padres" => $padres,
        );
        $status = true;
        if (!$this->verifyCreateRepeat($id_not_pro, $gestion_permanencia)) {
          $message = 'Información encontrada';
        } else {
          $message = "Su permanecia ya esta registrada";
        }
      } else {
        $result = -1;
        $message = "Estudiantes de la promoción, no pueden pre inscribir.";
        $status = false;
      }
    } else {
      $result = -1;
      $message = "El carnet no se encuentra habilitado";
      $status = false;
    }

    return $this->response->send($result, $status, $message, []);
  }

  private function verifyEnabled($codigo)
  {
    $codigoArray = explode('-', $codigo);
    if ($codigoArray[0] === '6A' || $codigoArray[0] === '6B' || $codigoArray[0] === '6C') {
      if ($codigoArray[1] === 'ST' || $codigoArray[1] === 'SM') { // Verificamos el turno
        return false; // Si estan en la promo --> deshabilitado
      } else {
        return true; // Si no estan en la promo --> habilitado
      }
    } else {
      return true; // habilitado
    }
  }

  private function verifyCreateRepeat($id_not_pro, $gestion_permanencia)
  {
    $conex = $this->pdo;
    $sql = "SELECT *
      FROM permanencia_form
      WHERE id_not_pro = $id_not_pro AND gestion_permanencia = $gestion_permanencia
    ";
    $query = $conex->prepare($sql);
    $idInsert = $query->execute();
    if ($query->rowCount() != 0) {
      return true;
    } else {
      return false;
    }
  }

  private function verifyCreateEnabled($id_not_pro, $gestion = 2022)
  {
    $conex = $this->pdo;
    // $gestion = $this->entorno->getGestion();
    $sql = "SELECT *
      FROM nota_prom
      WHERE id_not_pro = $id_not_pro
      AND boletin = 0
      AND gestion = $gestion
    ";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      return true;
    } else {
      return false;
    }
  }
}
