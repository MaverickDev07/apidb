<?php

class ProfesorModel
{
  private $conexion;
  private $response;

  public function __construct() {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->response = new Response();
    $this->entorno = new Entorno();
  }
  
  /* Obtiene datos el usuario */
  public function getUsuario($usuario) {
    $conex = $this->pdo;
    $sql = "SELECT
      US.usuario,
      US.nombre,
      US.appat,
      US.apmat,
      US.rol
      FROM usuario as US
      WHERE
      US.usuario = \"$usuario\"
    ";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchObject();
      $status = true;
      $message = 'Usuario encontrado';
    } else {
      $result = null;
      $message = "El usuario no existe";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  /* Obtiene datos del profesor */
  public function getProfesor($usuario) {
    $usuarioCurrent = $this->getUsuario($usuario);
    if($usuarioCurrent->status) {
      $usuarioData = $usuarioCurrent->result;
      $nombre = $usuarioData->nombre;
      $appat = $usuarioData->appat;
      $apmat = $usuarioData->apmat;
      $conex = $this->pdo;
      $sql = "SELECT
        *
        FROM profesores as PROF
        WHERE
        PROF.nombre = \"$nombre\" AND
        PROF.appaterno = \"$appat\" AND
        PROF.apmaterno = \"$apmat\" AND
        PROF.activo = 1
      ";
      $query = $conex->prepare($sql);
      $query->execute();
      if ($query->rowCount() != 0) {
        $result = $query->fetchObject();
        $status = true;
        $message = 'Profesor encontrado';
      } else {
        $result = null;
        $message = "El profesor no existe";
        $status = false;
      }
    } else {
      $result = null;
      $message = $usuarioCurrent->message;
      $status = false;
    }
    
    return $this->response->send($result, $status, $message, []);
  }

  /* Obtiene las materias que dicta el profesor */
  public function getMateriasProfesor($usuario, $gestion=2021) {
    $profesorCurrent = $this->getProfesor($usuario);
    // $gestion = $this->entorno->getGestion();
    if($profesorCurrent->status) {
      // tenemos el id del profesor
      $profesorData = $profesorCurrent->result;
      $id_prof = $profesorData->id_prof;
      $conex = $this->pdo;
      $sql = "SELECT
        ASP.id_asg_prof,
        ASP.id_prof,
        ASP.gestion,
        ASP.codigo,
        MAT.id_mat,
        MAT.nombre as materia_nombre,
        MAT.sigla as materia_sigla
        FROM asiginar_profesorm as ASP
        INNER JOIN asiginar_materiacu as AMC
        ON AMC.id_asg_mate = ASP.id_asg_mate
        INNER JOIN materias as MAT
        ON MAT.id_mat = AMC.id_mat
        WHERE ASP.id_prof = $id_prof AND ASP.gestion = $gestion
      ";
      $query = $conex->prepare($sql);
      $query->execute();
      if ($query->rowCount() != 0) {
        $result = array(
          "profesor" => $profesorData,
          "materias" => $query->fetchAll(PDO::FETCH_OBJ)
        );
        $status = true;
        $message = 'Materias encontradas';
      } else {
        $result = null;
        $message = "No hay materias disponibles";
        $status = false;
      }
    } else {
      // no es profesor
      $result = null;
      $message = $profesorCurrent->message;
      $status = false;
    }
    
    return $this->response->send($result, $status, $message, []);
  }

  /* Obtiene las materias que ya tienen calificación de un profesor */
  public function getMateriasCalificadas($id_prof, $gestion=2021) {
    $conex = $this->pdo;
    // $gestion = $this->entorno->getGestion();
    $sql = "SELECT
      DISTINCT APM.id_asg_prof,
      MAT.id_mat, MAT.nombre,
      NT.id_bi, NT.gestion, NT.fecha_subida
      FROM asiginar_profesorm as APM
      LEFT OUTER JOIN nota_trimestre as NT
      ON APM.id_asg_prof = NT.id_asg_prof
      INNER JOIN asiginar_materiacu as AMC
      ON APM.id_asg_mate = AMC.id_asg_mate
      INNER JOIN materias as MAT ON
      AMC.id_mat = MAT.id_mat
      WHERE APM.id_prof = $id_prof AND APM.gestion = $gestion
    ";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      $status = true;
      $message = 'Calificaciones encontradas';
    } else {
      $result = null;
      $message = "No hay calificaciones disponibles";
      $status = false;
    }
    
    return $this->response->send($result, $status, $message, []);
  }

  /* Obtiene las materias de un profesor y cuales fueron calificadas */
  public function getMateriasNotas($usuario, $gestion=2021) {
    $materiasProfesorCurrent = $this->getMateriasProfesor($usuario, $gestion);
    $materiasProfesorData = $materiasProfesorCurrent->result;
    if($materiasProfesorCurrent->status) {
      /* Revisión de notas */
      $id_prof = $materiasProfesorData["profesor"]->id_prof;
      $materias = $materiasProfesorData["materias"];

      $materiasCalificadasCurrent = $this->getMateriasCalificadas($id_prof, $gestion);
      $materiasCalificadasData = $materiasCalificadasCurrent->result;
      if($materiasCalificadasCurrent->status) {
        $materiasCalificadas = $materiasCalificadasData;
        /* Creamos un array de trimestres */
        $trimestres = array(
          $this->entorno->getPrimerTrimestre(),
          $this->entorno->getSegundoTrimestre(),
          $this->entorno->getTercerTrimestre()
        );
        /* Array de Resultado */
        $result = array();
        $status = true;
        $message = "Materias encontradas";
    
        foreach ($trimestres as $key => $trimestre_current) {
          $materias_trimestre = array();
          foreach ($materias as $key => $materia_current) {
            $isQual = false;
            $fechaCurrent = "";
            foreach ($materiasCalificadas as $key => $calificada_current) {
              if($calificada_current->id_asg_prof == $materia_current->id_asg_prof) {
                if($calificada_current->id_bi == $trimestre_current) {
                  $isQual = true;
                  $m = new \Moment\Moment($calificada_current->fecha_subida);
                  $fechaCurrent = ucfirst($m->format('l, d M Y'));
                  continue;
                }
              }
            }
            $codigo = explode("-", $materia_current->codigo);
            array_push($materias_trimestre, array(
              "id_asg_prof" => $materia_current->id_asg_prof,
              "id_prof" => $materia_current->id_prof,
              "id_mat" => $materia_current->id_mat,
              "materia_nombre" => $materia_current->materia_nombre,
              "materia_sigla" => $materia_current->materia_sigla,
              "materia_nota" => $isQual,
              "materia_fecha" => $fechaCurrent,
              "materia_trimestre" => $trimestre_current,
              "codigo" => $materia_current->codigo,
              "curso_nombre" => checkCurso($codigo[0]), // helper
              "curso_sigla" => $codigo[0],
              "turno" => $codigo[1],
              "id_turno" => $this->entorno->getNivelID($codigo[1]),
              "gestion" => $materia_current->gestion,
            ));
          }
          array_push($result, $materias_trimestre);
        }

      } else {
        $result = null;
        $status = false;
        $message = $materiasCalificadasCurrent->message;
      }
    } else {
      $result = null;
      $status = false;
      $message = $materiasProfesorCurrent->message;
    }

    return $this->response->send($result, $status, $message, []);
  }

  /* Obtiene las notas subidas de una materia y trimestre */

  public function getNotasTrimestre($id_trimestre, $id_asg_prof, $gestion=2021) {
    $conex = $this->pdo;
    // $gestion = $this->entorno->getGestion();
    $sql = "SELECT
      ES.id_est,
      ES.nombre,
      ES.appaterno,
      ES.apmaterno,
      ES.ci,
      ES.genero,
      NT.*
      FROM nota_trimestre as NT
      INNER JOIN estudiantes as ES
      ON ES.id_est = NT.id_est
      WHERE
      NT.id_asg_prof = $id_asg_prof AND
      NT.id_bi = $id_trimestre AND
      NT.gestion = $gestion AND
      NT.total > 0
      ORDER BY ES.appaterno, ES.apmaterno, ES.nombre ASC";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      $status = true;
      $message = 'Notas trimestre encontradas';
    } else {
      $result = null;
      $message = "No hay notas disponibles";
      $status = false;
    }
    
    return $this->response->send($result, $status, $message, []);
  }

}
?>
