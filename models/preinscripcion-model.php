<?php

class PreinscripcionModel
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

  /* Crear una preinscripcion */
  public function create($data)
  {
    $id_not_pro = $data->id_not_pro;
    $nombre_padre = strtoupper($data->nombre_padre);
    $nombre_madre = strtoupper($data->nombre_madre);
    $nombre_postulante = strtoupper($data->nombre_postulante);
    $appaterno_postulante = strtoupper($data->appaterno_postulante);
    $apmaterno_postulante = strtoupper($data->apmaterno_postulante);
    $celular_padre = $data->celular_padre;
    $celular_madre = $data->celular_madre;

    $conex = $this->pdo;
    $sql = "INSERT INTO
      preinscripcion
      VALUES (
        NULL,
        \"$id_not_pro\",
        \"$nombre_padre\",
        \"$nombre_madre\",
        \"$nombre_postulante\",
        \"$appaterno_postulante\",
        \"$apmaterno_postulante\",
        \"\",
        \"$celular_padre\",
        \"$celular_madre\",
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
      $result = -1;
      $message = "No se pudo registrar";
      $status = false;
    }

    return $this->response->send($result, $status, $message, []);
  }

  public function getEstudiante($ci, $gestion = 2021)
  {
    $conex = $this->pdo;
    // $gestion = $this->entorno->getGestion();
    $sql = "SELECT
    NP.id_not_pro,
    NP.codigo,
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
    WHERE NP.gestion = $gestion AND NP.boletin = 0 AND EST.ci = '$ci'";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $resultData = $query->fetchAll(PDO::FETCH_OBJ);
      $padres = array();
      $id_not_pro = 0;
      $codigo = '';
      foreach ($resultData as $key => $value) {
        if ($key === 0) {
          $id_not_pro = $value->id_not_pro;
          $codigo = $value->codigo;
        }
        $padres[$value->tipo] = trim($value->appaterno . ' ' . $value->apmaterno . ' ' . $value->nombre);
      }
      if ($this->verifyEnabled($codigo)) {
        $result = array(
          "id_not_pro" => $id_not_pro,
          "padres" => $padres
        );
        $message = 'Información encontrada';
        $status = true;
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

  public function getEstudiantePreInscrito($ci)
  {
    $conex = $this->pdo;
    $sql = "SELECT * FROM estudiantes as EST WHERE EST.ci = '$ci'";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $sql = "SELECT * FROM estudiantes as EST WHERE EST.preinscripcion = '1' AND EST.ci = '$ci'";
      $query = $conex->prepare($sql);
      $query->execute();
      if ($query->rowCount() != 0) {
        $resultData = $query->fetchAll(PDO::FETCH_OBJ);

        $result = $resultData;
        $message = 'Información encontrada';
        $status = true;
      } else {
        $result = -2;
        $message = "El estudiante ya se encuentra inscrito";
        $status = false;
      }
    } else {
      $result = -1;
      $message = "El estudiante no está pre inscrito";
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

  public function getPreinscripcion($id_pre)
  {
    $conex = $this->pdo;
    $sql = "SELECT
      PRE.id_pre,
      PRE.nombre_padre,
      PRE.nombre_madre,
      PRE.nombre_postulante,
      PRE.appaterno_postulante,
      PRE.apmaterno_postulante,
      PRE.celular_postulante,
      PRE.celular_padre,
      PRE.celular_madre,
      PRE.fecha_registro,
      NP.codigo,
      EST.nombre as nombre_estudiante,
      EST.appaterno as appaterno_estudiante,
      EST.apmaterno as apmaterno_estudiante,
      EST.ci as ci_estudiante,
      EST.extension as extension_estudiante
      FROM preinscripcion as PRE
      INNER JOIN nota_prom as NP
      ON PRE.id_not_pro = NP.id_not_pro
      INNER JOIN estudiantes as EST
      ON EST.id_est = NP.id_est
      WHERE PRE.id_pre = $id_pre
    ";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchObject();
      $codigo = explode("-", $result->codigo);
      $result->curso = $codigo[0];
      $result->turno = $codigo[1];
      return $result;
    } else {
      return null;
    }
  }

  /* Genera el contenido del PDF */
  public function generatePreinscripcion($id_pre)
  {
    $preinscripcionData = $this->getPreinscripcion($id_pre);
    /* verificar si existe datos */
    if ($preinscripcionData) {
      /* Generar PDF */
      $htmlInfo = $this->generateTablaInfo($preinscripcionData, $id_pre);
      /* Generamos Documento PDF */
      $this->PDFCreator($htmlInfo);
    } else {
      /* Generar Error */
      $this->generateError();
    }
  }

  /* Genera un PDF con mensaje de Error */
  private function generateError()
  {
    $htmlNotas = $this->generateTablaError();
    $this->PDFCreator($htmlNotas);
  }

  /* Genera la tabla de error */
  private function generateTablaError()
  {
    $html = "
      <br><br><br><br>
      <table border=\"0\" cellpadding=\"6\" cellspacing=\"2\">
        <tr>
          <td colspan=\"3\" align=\"center\" style=\"font-size:16px\"><b>LA PREINSCRIPCIÓN<br>NO EXISTE</b></td>
        </tr>
      </table>
      <br><br>
    ";
    return $html;
  }

  /* Genera la tabla Informacion */
  private function generateTablaInfo($preinscripcionData, $id_pre)
  {
    $nombreInfo = $preinscripcionData->appaterno_estudiante . ' ' . $preinscripcionData->apmaterno_estudiante . ' ' . $preinscripcionData->nombre_estudiante;
    $colegioInfo = checkColegio($preinscripcionData->turno); // helper
    $cursoInfo = checkCurso($preinscripcionData->curso); // helper
    $turnoInfo = checkTurno($preinscripcionData->turno); // helper
    $gradoInfo = checkGrado($preinscripcionData->turno); // helper
    $moment = new \Moment\Moment($preinscripcionData->fecha_registro);
    $fechaInfo = ucfirst($moment->format('l, d M Y'));

    $html = "
      <br><br>
      <table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">
        <tr>
          <td align=\"center\" style=\"font-size:16px;text-decoration:underline;\"><b>FORMULARIO DE PRE - INSCRIPCIÓN<br>GESTIÓN 2022</b></td>
        </tr>
      </table>
      <br><br>
      <table border=\"0\" cellpadding=\"0\" cellspacing=\"2\">
        <tr>
          <td style=\"font-size:12px;text-decoration:underline;\"><b>Formulario: $id_pre</b></td>
        </tr>
      </table>
      <br><br>
      <table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">
        <tr>
          <td style=\"font-size:14px;text-decoration:underline;\"><b>DATOS ESTUDIANTE:</b></td>
        </tr>
      </table>
      <br><br>
      <table border=\"1\" cellpadding=\"4\" cellspacing=\"0\" bordercolor=\"#000000\">
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>NOMBRE:</b></td>
          <td width=\"450\">$nombreInfo</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>CI:</b></td>
          <td width=\"450\">$preinscripcionData->ci_estudiante $preinscripcionData->extension_estudiante</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#F4D160;color:#030f1b;\"><b>UNIDAD EDUCATIVA:</b></td>
          <td width=\"450\" style=\"background-color:#F4D160;color:#030f1b;\">$colegioInfo</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>CURSO:</b></td>
          <td width=\"450\">$cursoInfo</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>GRADO:</b></td>
          <td width=\"450\">$gradoInfo</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>TURNO:</b></td>
          <td width=\"450\">$turnoInfo</td>
        </tr>
      </table>
      <br><br>
      <table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">
        <tr>
          <td style=\"font-size:14px;text-decoration:underline;\"><b>DATOS PADRE, MADRE DE FAMILIA:</b></td>
        </tr>
      </table>
      <br><br>
      <table border=\"1\" cellpadding=\"4\" cellspacing=\"0\" bordercolor=\"#000000\">
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>APELLIDOS Y NOMBRES PADRE:</b></td>
          <td width=\"450\">$preinscripcionData->nombre_padre</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>CELULAR PADRE:</b></td>
          <td width=\"450\">$preinscripcionData->celular_padre</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>APELLIDOS Y NOMBRES MADRE:</b></td>
          <td width=\"450\">$preinscripcionData->nombre_madre</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>CELULAR MADRE:</b></td>
          <td width=\"450\">$preinscripcionData->celular_madre</td>
        </tr>
      </table>
      <br><br>
      <table border=\"0\" cellpadding=\"4\" cellspacing=\"2\">
        <tr>
          <td style=\"font-size:14px;text-decoration:underline;\"><b>DATOS DEL ESTUDIANTE POSTULANTE (HERMANO/A):</b></td>
        </tr>
      </table>
      <br><br>
      <table border=\"1\" cellpadding=\"4\" cellspacing=\"0\" bordercolor=\"#000000\">
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>NOMBRE:</b></td>
          <td width=\"450\">$preinscripcionData->nombre_postulante</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>APELLIDO PATERNO:</b></td>
          <td width=\"450\">$preinscripcionData->appaterno_postulante</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>APELLIDO MATERNO:</b></td>
          <td width=\"450\">$preinscripcionData->apmaterno_postulante</td>
        </tr>
        <tr>
          <td width=\"150\" style=\"background-color:#f5f5f5;color:#030f1b;\"><b>FECHA DE REGISTRO:</b></td>
          <td width=\"450\">$fechaInfo</td>
        </tr>
      </table>
      <br><br>";
    if ($preinscripcionData->turno === "PM" || $preinscripcionData->turno === "SM") {
      $html = $html . "<br><br><br><br><table border=\"0\" cellpadding=\"0\" cellspacing=\"2\">
        <tr>
          <td style=\"font-size:12px;text-decoration:underline;\"><b>Importante: (Solo turno mañana)</b></td>
        </tr>
        <tr>
          <td style=\"font-size:12px;text-decoration:underline;\">Recordarles en el momento de entregar la documentación deben adjuntar:</td>
        </tr>
        <tr>
          <td style=\"font-size:12px;\">-Fotocopia de carnet de los Padres de Familia</td>
        </tr>
        <tr>
          <td style=\"font-size:12px;\">-Fotocopia de carnet del estudiante en nuestra U.E.</td>
        </tr>
        <tr>
          <td style=\"font-size:12px;\">-Fotocopia de carnet del estudiante Postulante a la U.E.</td>
        </tr>
      </table>";
    }
    return $html;
  }

  /* Genera el Documento PDF */
  private function PDFCreator($htmlTemplate)
  {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Colegio Don Bosco Sucre');
    $pdf->SetTitle('Pre - inscripción Gestión 2022');
    $pdf->SetSubject('Pre - inscripción Gestión 2022');
    $pdf->SetKeywords('Colegio, Don, Bosco, Sucre, Notas');
    // set default header data
    $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // set some language-dependent strings (optional)
    if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
      require_once(dirname(__FILE__) . '/lang/eng.php');
      $pdf->setLanguageArray($l);
    }
    // ---------------------------------------------------------
    $pdf->setFontSubsetting(true);
    $pdf->SetFont('dejavusans', '', 9, '', true);
    $pdf->AddPage();
    $pdf->Image(dirname(__FILE__) . '/../utils/images/header_notas.jpg', 5, 5, 200, 0, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
    $pdf->Multicell(0, 2, "\n\n\n\n\n");
    // echo $htmlTemplate;
    // exit();
    $pdf->writeHTML($htmlTemplate, true, false, false, false, '');
    $pdf->Output('preinscripcion.pdf', 'I');
    // echo '<textarea>'.$htmlTemplate.'</textarea>';
    ob_end_flush(); // limpiamos el buffer
    exit();
  }
}
