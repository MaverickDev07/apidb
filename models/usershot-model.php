<?php

class UsershotModel
{
  private $conexion;
  private $response;
  private $table = 'usuario';

  public function __construct()
  {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->fpdo = $this->conexion->getFluent();
    $this->response = new Response();
  }

  // public function getAll()
  // {
  //   /* 1. Consultamos */
  //   $conex = $this->pdo;
  //   $sql = "SELECT 
  //   US.idUsershot, US.firstname, US.lastname, US.cellphone, US.picture as pictureProfile,
  //   '' as pictureTicket, GA.directory, GA.filename, US.createdAt, US.updatedAt 
  //   FROM usershot as US
  //   LEFT JOIN galery as GA
  //   ON US.galeryId = GA.idGalery
  //   ORDER BY US.galeryId DESC";
  //   $query = $conex->prepare($sql);
  //   $query->execute();
  //   $result = null;
  //   /* 2. encriptar IDs */
  //   if ($query->rowCount() != 0) {
  //     $result = $query->fetchAll(PDO::FETCH_OBJ);
  //     foreach ($result as $key => $value) {
  //       $value->idUsershot = $this->hashids->encode($value->idUsershot);
  //       $value->pictureTicket = $this->galery->getFullPath(
  //         $value->directory,
  //         $value->filename
  //       );
  //       unset($value->directory);
  //       unset($value->filename);
  //     }
  //     $status = true;
  //     $message = "Lista de cuentas habilitadas";
  //   } else {
  //     $result = [];
  //     $status = false;
  //     $message = "No existen registros";
  //   }
  //   /* 3. retornar valores en un array Response */
  //   return $this->response->send($result, $status, $message, []);
  // }

  // public function getId($id)
  // {
  //   /* 1. Validamos el ID */
  //   $idShot = $this->hashids->decode($id);
  //   if (count($idShot) != 0) {
  //     $idUsershot = intval($idShot[0]);
  //   } else {
  //     $idUsershot = 0;
  //   }
  //   /* 2. Consulta */
  //   $conex = $this->pdo;
  //   $sql = "SELECT 
  //   US.idUsershot, US.firstname, US.lastname,  US.picture as pictureProfile,
  //   '' as pictureTicket,GA.directory, GA.filename, US.createdAt, US.updatedAt 
  //   FROM usershot as US
  //   LEFT JOIN galery as GA
  //   ON US.galeryId = GA.idGalery
  //   WHERE US.idUsershot = {intval($idUsershot)}
  //   ORDER BY US.idUsershot ASC LIMIT 1";
  //   $query = $conex->prepare($sql);
  //   $query->execute();

  //   $result = null;
  //   /* 3. encriptar IDs */
  //   if ($query->rowCount() != 0) {
  //     $result = $query->fetchObject();
  //     $result->ticketNumber = $this->generateNumberTicket($result->idUsershot);
  //     $result->idUsershot = $this->hashids->encode($result->idUsershot);
  //     $result->pictureTicket = $this->galery->getFullPath(
  //       $result->directory,
  //       $result->filename
  //     );
  //     unset($result->directory);
  //     unset($result->filename);
  //     $status = true;
  //     $message = "Usuario de Reserva encontrado";
  //   } else {
  //     $result = [];
  //     $status = false;
  //     $message = "No existen registros";
  //   }
  //   /* 4. retornar valores en un array Response */
  //   return $this->response->send($result, $status, $message, []);
  // }

  // /* data = firstname, lastname, email, cellphone, picture */
  // public function create($data)
  // {
  //   /* 1. Verificamos Recaptcha */
  //   if (checkCaptcha($data->secret)) {
  //     /* 2. Verificamos si ya se registro el email */
  //     $where = [
  //       "email" => $data->email,
  //     ];
  //     $queryUser = $this->fpdo
  //       ->from($this->table)
  //       ->where($where)
  //       ->orderBy('idUsershot DESC')
  //       ->limit(1)
  //       ->execute();
  //     if ($queryUser->rowCount() == 0) {
  //       /* Insertamos nuevo usuario */
  //       $values = [
  //         'firstname' => $data->firstname,
  //         'lastname' => $data->lastname,
  //         'email' => $data->email,
  //         'cellphone' => $data->cellphone,
  //         'picture' => $data->picture,
  //         'galeryId' => null,
  //         'createdAt' => new FluentLiteral("CURRENT_TIMESTAMP"),
  //         'updatedAt' => new FluentLiteral("CURRENT_TIMESTAMP"),
  //       ];
  //       $query = $this->fpdo->insertInto($this->table)->values($values);
  //       $idInsert = $query->execute();
  //       $result = [
  //         "idInsert" => $this->hashids->encode($idInsert),
  //         "ticketNumber" => $this->generateNumberTicket($idInsert),
  //       ];
  //       $message = 'Insertado con exito';
  //       $status = true;
  //     } else {
  //       $queryResult = $queryUser->fetchObject();
  //       $idInsert = $queryResult->idUsershot;
  //       $result = [
  //         "idInsert" => $this->hashids->encode($idInsert),
  //         "ticketNumber" => $this->generateNumberTicket($idInsert),
  //       ];
  //       $message = "El email ya fue registrado";
  //       $status = false;
  //     }
  //   } else {
  //     $result = -1;
  //     $message = "Recaptcha no válido";
  //     $status = false;
  //   }

  //   return $this->response->send($result, $status, $message, []);
  // }

  // public function setTicket($data)
  // {
  //   /* 1. Validamos el ID */
  //   $idShot = $this->hashids->decode($data->idUsershot);
  //   if (count($idShot) != 0) {
  //     $idUsershot = intval($idShot[0]);
  //   } else {
  //     $idUsershot = 0;
  //   }
  //   /* 2. Verificamos si existe usuario */
  //   $where = [
  //     "idUsershot" => $idUsershot,
  //   ];
  //   $queryUser = $this->fpdo
  //     ->from($this->table)
  //     ->where($where)
  //     ->orderBy('idUsershot DESC')
  //     ->limit(1)
  //     ->execute();
  //   if ($queryUser->rowCount() != 0) {
  //     /* Insertamos Imagen */
  //     $galery = new GaleryModel();
  //     $resultGalery = $galery->saveImage(
  //       /*directory*/ $this->directory,
  //       /*base64*/ $data->imageTicket,
  //       /*format*/ 'png',
  //       /*thumb*/ false
  //     );
  //     $galeryID = intval($resultGalery['idGalery']);
  //     /* Insertamos nuevo usuario */
  //     $values = [
  //       'galeryId' => $galeryID,
  //     ];
  //     $query = $this->fpdo
  //       ->update($this->table)
  //       ->set($values)
  //       ->where('idUsershot', $idUsershot);
  //     $idInsert = $this->hashids->encode($query->execute());
  //     $result = ["idInsert" => $idInsert];
  //     $message = 'Ticket generado con éxito';
  //     $status = true;
  //   } else {
  //     $result = -1;
  //     $message = "Usuario no válido";
  //     $status = false;
  //   }

  //   return $this->response->send($result, $status, $message, []);
  // }

  // /* data = idUsershot, firstname, lastname, cellphone */
  // public function update($data)
  // {
  //   /* 1. Validamos el ID */
  //   $idShot = $this->hashids->decode($data->idUsershot);
  //   if (count($idShot) != 0) {
  //     $idUsershot = intval($idShot[0]);
  //   } else {
  //     $idUsershot = 0;
  //   }
  //   /* 2. Verificamos si existe usuario */
  //   $where = [
  //     "idUsershot" => $idUsershot,
  //   ];
  //   $queryUser = $this->fpdo
  //     ->from($this->table)
  //     ->where($where)
  //     ->orderBy('idUsershot DESC')
  //     ->limit(1)
  //     ->execute();
  //   if ($queryUser->rowCount() != 0) {
  //     /* Insertamos nuevo usuario */
  //     $values = [
  //       'firstname' => $data->firstname,
  //       'lastname' => $data->lastname,
  //       'cellphone' => $data->cellphone,
  //       'updatedAt' => new FluentLiteral("CURRENT_TIMESTAMP"),
  //     ];
  //     $query = $this->fpdo
  //       ->update($this->table)
  //       ->set($values)
  //       ->where('idUsershot', $idUsershot);
  //     $idInsert = $this->hashids->encode($query->execute());
  //     $result = ["idInsert" => $idInsert];
  //     $message = 'Modificado con exito';
  //     $status = true;
  //   } else {
  //     $result = -1;
  //     $message = "El usuario no esta registrado";
  //     $status = false;
  //   }

  //   return $this->response->send($result, $status, $message, []);
  // }

  // /* data = idUsershot */
  // public function delete($data)
  // {
  //   /* 1. Validamos el ID */
  //   $idShot = $this->hashids->decode($data->idUsershot);
  //   if (count($idShot) != 0) {
  //     $idUsershot = intval($idShot[0]);
  //   } else {
  //     $idUsershot = 0;
  //   }
  //   $where = [
  //     "idUsershot" => $idUsershot,
  //   ];
  //   /* 2. Verificamos al usuario */
  //   $queryUser = $this->fpdo
  //     ->from($this->table)
  //     ->where($where)
  //     ->orderBy('idUsershot DESC')
  //     ->limit(1)
  //     ->execute();
  //   if ($queryUser->rowCount() != 0) {
  //     /* Eliminamos Imagen */
  //     $resultUser = $queryUser->fetchObject();
  //     if ($resultUser->galeryId) {
  //       /* 2. Realizamos la peticion para eliminar */
  //       $this->galery->deleteImage($resultUser->galeryId);
  //     }
  //     $query = $this->fpdo->deleteFrom($this->table)->where($where);
  //     $idRemove = $this->hashids->encode($query->execute());
  //     $result = ["idRemove" => $idRemove];
  //     $message = 'Eliminado';
  //     $status = true;
  //   } else {
  //     $result = -1;
  //     $message = "El usuario no esta registrado";
  //     $status = false;
  //   }

  //   return $this->response->send($result, $status, $message, []);
  // }

  // /* Utils */

  // public function generateNumberTicket($id)
  // {
  //   /* Generar ticket hasta los 33444 personas */
  //   $numberTicket = round(intval($id) * 2.99) . "";
  //   $sizeTicket = strlen($numberTicket);

  //   if ($sizeTicket < 5) {
  //     $ticket = str_repeat("0", 5 - $sizeTicket) . $numberTicket;
  //   } elseif ($sizeTicket > 5) {
  //     $ticket = substr($numberTicket, 0, 5);
  //   } else {
  //     $ticket = $numberTicket;
  //   }

  //   return $ticket;
  // }

  /* Modelo de prueba */
  public function test()
  {
    $conex = $this->pdo;
    $sql = "SELECT 
    idCod, usuario
    FROM usuario";
    $queryUser = $conex->prepare($sql);
    $queryUser->execute();
    if ($queryUser->rowCount() != 0) {
      $resultUsers = $queryUser->fetchAll(PDO::FETCH_OBJ);
      $result = $resultUsers;
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
