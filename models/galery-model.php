<?php

class GaleryModel
{
  private $env;
  private $conexion;
  private $table = 'galery';
  private $response;
  private $baseUrl;

  public function __construct()
  {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->fpdo = $this->conexion->getFluent();
    $this->response = new Response();
    $this->baseUrl = $_SERVER['HTTP_HOST'];
  }

  public function saveImage(
    $directory = null,
    $dataImage,
    $format = null,
    $withThumb = null
  ) {
    /* Initial Validation */
    if (null === $directory) {
      $directory = '';
    }
    if (null === $format) {
      $format = 'jpeg';
    }
    if (null === $withThumb) {
      $withThumb = true;
    }
    /* Directory Validation */
    if ($directory === '') {
      $url_picture = 'upload';
    } else {
      $url_picture = 'upload/' . $directory;
    }

    /* Create in database */
    $nameImage = uniqid() . '.' . $format;
    $values = [
      'directory' => $directory,
      'filename' => $nameImage,
      'createdAt' => new FluentLiteral("CURRENT_TIMESTAMP"),
    ];
    $query = $this->fpdo->insertInto($this->table)->values($values);
    $idInsert = $query->execute();

    /* Create directory if it doesn't exist */
    if (is_dir($url_picture) === false) {
      $old = umask(0);
      mkdir($url_picture, 0777);
      umask($old);
    }
    define('UPLOAD_DIRECTION', $url_picture . '/');

    $image = base64_decode(
      str_replace('data:image/' . $format . ';base64,', '', $dataImage)
    );
    /* Save image original size */
    $formImage = imagecreatefromstring($image);
    if ($format === 'jpeg') {
      imagejpeg($formImage, UPLOAD_DIRECTION . $nameImage, 100);
    } else {
      imagealphablending($formImage, true);
      imagesavealpha($formImage, true);
      imagepng($formImage, UPLOAD_DIRECTION . $nameImage);
    }
    imagedestroy($formImage);

    /* Save thumbnail */
    if ($withThumb) {
      $thumb = new ImageResizer();
      $thumb->smart_resize_image(
        null,
        $image,
        75,
        75,
        false,
        UPLOAD_DIRECTION . 'thumb_' . $nameImage
      );
    }

    return [
      "idGalery" => $idInsert,
      "filename" => $nameImage,
    ];
  }

  public function deleteImage($idGalery)
  {
    /* 1. Construimos nuestra condición */
    $where = [
      "idGalery" => intval($idGalery),
    ];
    /* 2. Verificamos la imagen */
    $queryGalery = $this->fpdo
      ->from($this->table)
      ->where($where)
      ->orderBy('idGalery DESC')
      ->limit(1)
      ->execute();
    if ($queryGalery->rowCount() != 0) {
      /* 3. Eliminamos de la base de datos */
      $resultGalery = $queryGalery->fetchObject();
      $query = $this->fpdo->deleteFrom('galery')->where($where);
      $query->execute();

      /* 4. Eliminamos el directorio */
      if ($resultGalery->directory === '') {
        $url_picture = 'upload';
      } else {
        $url_picture = 'upload/' . $resultGalery->directory;
      }
      define('UPLOAD_DIRECTION', $url_picture . '/');
      $files = [
        UPLOAD_DIRECTION . $resultGalery->filename,
        UPLOAD_DIRECTION . 'thumb_' . $resultGalery->filename,
      ];

      foreach ($files as $file) {
        if (file_exists($file)) {
          unlink($file) or die("Couldn't delete file");
        }
      }
    }
  }

  public function getFullPath($directory, $filename)
  {
    $HTTP_BASE = 'http://localhost/apidonbosco/public/api/';
    $BASE_IMAGE = 'upload';
    if ($directory === null && $filename === null) {
      return $HTTP_BASE . $BASE_IMAGE . '/default.png';
    }
    if ($directory === '') {
      $file = $filename;
    } else {
      $file = $directory . '/' . $filename;
    }
    return $HTTP_BASE . $BASE_IMAGE . '/' . $file;
  }

  public function saveDirect($data){
    $imageThumb = ($data->thumb === 'true' );
    $imageFormat = $data->format;
    $imageResult = $this->saveImage($data->image, $imageThumb, $imageFormat);
    return $this->response->send(
      $imageResult,
      true,
      "Imagen Guardada",
      []
    );
  }
  public function deleteDirect($data){
    $where = array(
      "id_galery"=>intval($data->idGalery),
    );
    $query = $this->fpdo->from($this->table)->where($where)->execute();
    if($query->rowCount()!=0){
      $result = $query->fetchObject();
      $imageResult = $this->deleteImage($where['id_galery'], $result->url_image);
      $status = true;
      $message = "Eliminado";
    } else {
      $result = null;
      $status = false;
      $message = "La imagen no existe";
    }

    return $this->response->send(
      $result,
      $status,
      $message,
      []
    );
  }
}
?>
