<?php

class BoletinesModel
{
  private $conexion;
  private $response;
  
  private $PRIMER_TRIMESTRE = 5;
  private $SEGUNDO_TRIMESTRE = 6;
  private $TERCER_TRIMESTRE = 7;
  
  private $PARAMS_PRIMER_TRIMESTRE = "primer";
  private $PARAMS_SEGUNDO_TRIMESTRE  = "segundo";
  private $PARAMS_TERCER_TRIMESTRE  = "tercer";

  private $FILL_COLUMNS = 9; /* número de columnas boletines */
  private $FILL_ROWS_HEAD = 3; /* número de filas de la cabecera del boletin*/
  
  private $WIDTH_CAMPO = 120; /* ancho del campo en la tabla */
  private $WIDTH_AREA = 120; /* ancho del area en la tabla */
  private $WIDTH_MATERIA = 100; /* ancho de las materias en la tabla */
  private $WIDTH_NOTA = 50; /* ancho de las notas en la tabla */

  public function __construct() {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->response = new Response();
  }

  /* Genera el contenido del PDF */
  private function generateContent($curso, $turno, $gestion, $id_est, $trimestre) {
    // Obtenemos las areas -> campos -> materias
    $cursoMaterias = $this->getCamposAreasMaterias($curso, $turno);
    // Obtenemos los trimestres a iterar
    $arrayTrimestres = $this->getTrimestres($trimestre);
    $estudiante = $this->getEstudiante($curso, $turno, $id_est, $gestion);

    if($estudiante) {
      /* Generamos notas */
      $columnIndex = 3; /* 0. Campos, 1.Area, 2. Materia */
      foreach ($arrayTrimestres as $key => $trimestreCurrent) {
        $notas = $this->getNotasEstudiante($id_est, $curso, $turno, $gestion, $trimestreCurrent);
        $notasFilter = $this->filterNotas($cursoMaterias, $notas); // Filtramos las notas
        $notasFilterErrores = $this->filterErrores($notasFilter, $curso, $turno); // Filtramos errores de asignacion
        $notasFilterEspecialidad = $this->filterEspecialidad($notasFilterErrores, $curso, $turno); // Filtramos materias de especialidad
        if($key == 0) {
          $tablaNotas = $this->generateNotas($notasFilterEspecialidad);
        }
        /* Si son notas de Secundaria Mañana sacar sus porcentajes*/
        if($turno == 'SM') {
          $notasPorcentuales = getMateriasPorcentajes($notasFilterEspecialidad, $curso); // helper
          $tablaNotas = $this->addNotasPorcentuales($tablaNotas, $notasFilterEspecialidad, $notasPorcentuales, $columnIndex);
          $columnIndex = $columnIndex + 2;
        } else {
          $tablaNotas = $this->addNotas($tablaNotas, $notasFilterEspecialidad, $columnIndex);
          $columnIndex = $columnIndex + 2;
        }
      }
      /* Generamos tabla informativa */
      $htmlInfo = $this->generateTablaInfo($estudiante, $curso, $turno, $gestion, $trimestre);
      /* Generamos tabla notas */
      $htmlNotas = $this->generateTablaNotas($tablaNotas);
      /* Generamos Documento PDF */
      $this->PDFCreator($htmlInfo.$htmlNotas);
    } else {
      /* No existe estudiante */
      $this->generateError();
    }
  }

  /* Genera el contenido del PDF */
  private function generateContentPages($curso = "1A", $turno = "ST", $trimestre = "primer", $gestion=2021) {
    // Obtenemos las areas -> campos -> materias
    $cursoMaterias = $this->getCamposAreasMaterias($curso, $turno);
    // Obtenemos los trimestres a iterar
    $arrayTrimestres = $this->getTrimestres($trimestre);
    $estudiantes = $this->getEstudiantes($curso, $turno, $gestion);
    $htmlInfoNotas = [];

    if($estudiantes) {
      foreach ($estudiantes as $keyEst => $est) {
        // Generamos notas
        $columnIndex = 3; // 0. Campos, 1.Area, 2. Materia
        foreach ($arrayTrimestres as $key => $trimestreCurrent) {
          $notas = $this->getNotasEstudiante($est->id_est, $curso, $turno, $gestion, $trimestreCurrent);
          $notasFilter = $this->filterNotas($cursoMaterias, $notas); // Filtramos las notas
          $notasFilterErrores = $this->filterErrores($notasFilter, $curso, $turno); // Filtramos errores de asignacion
          $notasFilterEspecialidad = $this->filterEspecialidad($notasFilterErrores, $curso, $turno); // Filtramos materias de especialidad
          if($key == 0) {
            $tablaNotas = $this->generateNotas($notasFilterEspecialidad);
          }
          // Si son notas de Secundaria Mañana sacar sus porcentajes
          if($turno == 'SM') {
            $notasPorcentuales = getMateriasPorcentajes($notasFilterEspecialidad, $curso); // helper
            $tablaNotas = $this->addNotasPorcentuales($tablaNotas, $notasFilterEspecialidad, $notasPorcentuales, $columnIndex);
            $columnIndex = $columnIndex + 2;
          } else {
            $tablaNotas = $this->addNotas($tablaNotas, $notasFilterEspecialidad, $columnIndex);
            $columnIndex = $columnIndex + 2;
          }
        }
        // Generamos tabla informativa
        $htmlInfo = $this->generateTablaInfo($est, $curso, $turno, $gestion, $trimestre);
        // Generamos tabla notas
        $htmlNotas = $this->generateTablaNotas($tablaNotas);
        array_push($htmlInfoNotas, $htmlInfo.$htmlNotas);
      }
      // Generamos Documento PDF
      $this->PDFCreatorPages($htmlInfoNotas);
    } else {
      // No existe estudiante
      $this->generateError();
    }
  }

  /* Genera el tabla de notas */
  private function generateTablaNotas($tabla) {
    $htmlGenerate = '';
    for($index=0; $index < count($tabla); $index++) {
      $rows = $tabla[$index]['contentRow'];
      $htmlGenerate = $htmlGenerate.$tabla[$index]['start'];
      for($rowIndex=0; $rowIndex < count($rows); $rowIndex++) {
        $htmlGenerate = $htmlGenerate.$tabla[$index]['contentRow'][$rowIndex]['start'];
        //$htmlGenerate = $htmlGenerate.'<div style="vertical-align: middle;">';
        $htmlGenerate = $htmlGenerate.$tabla[$index]['contentRow'][$rowIndex]['contentColumn'];
        //$htmlGenerate = $htmlGenerate.'</div>';
        $htmlGenerate = $htmlGenerate.$tabla[$index]['contentRow'][$rowIndex]['end'];
      }
      $htmlGenerate = $htmlGenerate.$tabla[$index]['end'];
    }
    /* Generamos tabla de notas */
    return $this->generateTablaTemplate($htmlGenerate);
  }

  /* Genera la tabla con campos, areas y materias */
  private function generateTablaInfo($estudiante, $curso, $turno, $gestion, $trimestre) {
    $nombreInfo = $estudiante->appaterno.' '.$estudiante->apmaterno.' '.$estudiante->nombre;
    $colegioInfo = checkColegio($turno); // helper
    $cursoInfo = checkCurso($curso); // helper
    $turnoInfo = checkTurno($turno); // helper
    $gradoInfo = checkGrado($turno); // helper
    $trimestreInfo = checkTrimestre($trimestre); // helper
    $html = "
      <table border=\"0\" cellpadding=\"6\" cellspacing=\"2\">
        <tr>
          <td colspan=\"3\" align=\"center\" style=\"font-size:16px;text-decoration:underline;\"><b>RENDIMIENTO ACADÉMICO<br>$trimestreInfo</b></td>
        </tr>
        <tr>
          <td width=\"400\"><b>UNIDAD EDUCATIVA:</b> $colegioInfo</td>
        </tr>
        <tr>
          <td width=\"150\"><b>CURSO:</b> $cursoInfo</td>
          <td width=\"150\"><b>GRADO:</b> $gradoInfo</td>
          <td width=\"150\"><b>TURNO:</b> $turnoInfo</td>
          <td width=\"150\"><b>GESTION:</b> $gestion</td>
        </tr>
        <tr>
          <td colspan=\"3\"><b>NOMBRE:</b> $nombreInfo</td>
        </tr>
      </table>
      <br><br>
    ";
    return $html;
  }

  /* Genera la tabla de error */
  private function generateTablaError() {
    $html = "
      <br><br><br><br>
      <table border=\"0\" cellpadding=\"6\" cellspacing=\"2\">
        <tr>
          <td colspan=\"3\" align=\"center\" style=\"font-size:16px\"><b>BOLETÍN<br>NO HABILITADO</b></td>
        </tr>
      </table>
      <br><br>
    ";
    return $html;
  }

  /* Genera el template de la tabla */
  private function generateTablaTemplate($content) {
    /* La tabla tiene 9 Columnas */
    //  style='border: 1px solid black;border-collapse: collapse;font-size: 12px;'
    return "
    <table border=\"1\" cellpadding=\"2\" cellspacing=\"0\" bordercolor=\"#000000\">
      <thead>
        <tr style=\"background-color:#28527A;color:#fff;\">
          <td width=\"$this->WIDTH_CAMPO\" rowspan=\"3\"><b>CAMPOS DE SABER Y CONOC.</b></td>
          <td width=\"$this->WIDTH_AREA\" valign=\"middle\" rowspan=\"3\"><b>AREAS<br>CURRICULARES</br></td>
          <td width=\"$this->WIDTH_MATERIA\" valign=\"middle\" rowspan=\"3\"><b>MATERIAS</b></td>
          <td width=\"".($this->WIDTH_NOTA * 6)."\" align=\"center\" colspan=\"6\"><b>VALORACION CUANTITATIVA</b></td>
        </tr>
        <tr style=\"background-color:#8AC4CF;color:#030f1b;\">
          <td align=\"center\" colspan=\"2\" bordercolor=\"#000000\"><b>1ER TRIMESTRE</b></td>
          <td align=\"center\" colspan=\"2\" bordercolor=\"#000000\"><b>2DO TRIMESTRE</b></td>
          <td align=\"center\" colspan=\"2\" bordercolor=\"#000000\"><b>3ER TRIMESTRE</b></td>
        </tr>
        <tr style=\"background-color:#F4D160;color:#030f1b;\">
          <td align=\"center\" bordercolor=\"#000000\"><b>1ER</b></td>
          <td align=\"center\" bordercolor=\"#000000\"><b>P.A</b></td>
          <td align=\"center\" bordercolor=\"#000000\"><b>2DO</b></td>
          <td align=\"center\" bordercolor=\"#000000\"><b>P.A</b></td>
          <td align=\"center\" bordercolor=\"#000000\"><b>3ER</b></td>
          <td align=\"center\" bordercolor=\"#000000\"><b>P.A</b></td>
        </tr>
      </thead>
      <tbody>$content</tbody>
    </table>";
  }

  /* Genera el Documento PDF */
  private function PDFCreator($htmlTemplate) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Colegio Don Bosco Sucre');
    $pdf->SetTitle('Boletin de Notas');
    $pdf->SetSubject('Notas de estudiante');
    $pdf->SetKeywords('Colegio, Don, Bosco, Sucre, Notas');
    // set default header data
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
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
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    // ---------------------------------------------------------
    $pdf->setFontSubsetting(true);
    $pdf->SetFont('dejavusans', '', 9, '', true);
    // Start First Page Group
    //$pdf->startPageGroup();
    $pdf->AddPage();
    $pdf->Image(dirname(__FILE__).'/../utils/images/header_notas.jpg', 5, 5, 200, 0, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
    $pdf->Multicell(0,2,"\n\n\n\n\n");
    // echo $htmlTemplate;
    // exit();
    $pdf->writeHTML($htmlTemplate, true, false, false, false, '');

    $pdf->Output('example_001.pdf', 'I');
    // echo '<textarea>'.$htmlTemplate.'</textarea>';
    ob_end_flush(); // limpiamos el buffer
    exit();
  }

  /* Genera el Documento PDF Boletines por curso */
  private function PDFCreatorPages($htmlInfoNotas) {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Colegio Don Bosco Sucre');
    $pdf->SetTitle('Boletin de Notas');
    $pdf->SetSubject('Notas de estudiante');
    $pdf->SetKeywords('Colegio, Don, Bosco, Sucre, Notas');
    // set default header data
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
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
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }
    // ---------------------------------------------------------
    $pdf->setFontSubsetting(true);
    $pdf->SetFont('dejavusans', '', 9, '', true);
    // Start First Page Group
    //$pdf->startPageGroup();

    foreach ($htmlInfoNotas as $key => $htmlTemplate) {
      $pdf->AddPage();
      $pdf->Image(dirname(__FILE__).'/../utils/images/header_notas.jpg', 5, 5, 200, 0, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
      $pdf->Multicell(0,2,"\n\n\n\n\n");
      // echo $htmlTemplate;
      // exit();
      $pdf->writeHTML($htmlTemplate, true, false, false, false, '');
    }

    $pdf->Output('boletines.pdf', 'I');
    ob_end_flush(); // limpiamos el buffer
    exit();
  }

  /* Genera la tabla con campos, areas, materias y notas (en blanco) */
  public function generateNotas($notas) {
    $arrayNotas = [];
    $arrayRepeatCampo = [];
    $arrayRepeatArea = [];
    
    /* Recorremos las filas */
    for($index=0; $index < count($notas); $index++) {
      $notaValue = (array) $notas[$index];
      $row = array(
        "start" => "<tr>",
        "contentRow" => array(),
        "end" => "</tr>"
      );
      /* Campo */
      if(!is_numeric(array_search($notaValue["id_campo"], $arrayRepeatCampo))) {
        /* get campo_rowspan */
        $campo_rowspan = $this->getRepeatCampo($notaValue["id_campo"], $notas);
        $column = array(
          "start" => "<td width=\"$this->WIDTH_CAMPO\" rowspan=\"$campo_rowspan\">",
          "contentColumn" => $notaValue["nombre_campo"],
          "end" => "</td>",
        );
        array_push($arrayRepeatCampo, $notaValue["id_campo"]); // Agregamos a Campo Repetido
        array_push($row['contentRow'], $column);
      }
      else {
        /* Columna Vacia */
        $columnEmpty = array(
          "start" => "",
          "contentColumn" => "",
          "end" => "",
        );
        array_push($row['contentRow'], $columnEmpty); /* Campo Vacio */
      }
      /* Area */
      if(!is_numeric(array_search($notaValue["id_area"], $arrayRepeatArea))) {
        /* rowspan */
        $area_rowspan = $this->getRepeatArea($notaValue["id_area"], $notas);
        /* Area */
        $columnArea = array(
          "start" => "<td width=\"$this->WIDTH_AREA\" rowspan=\"$area_rowspan\">",
          "contentColumn" => $notaValue["nombre_area"],
          "end" => "</td>",
        );
        /* Materia */
        $columnMateria = array(
          "start" => "<td width=\"$this->WIDTH_MATERIA\">",
          "contentColumn" => $notaValue["nombre_materia"],
          "end" => "</td>",
        );
        /* Nota y Promedio 1T */
        $columnNota1T = array(
          "start" => "<td width=\"$this->WIDTH_NOTA\">",
          "contentColumn" => "",
          "end" => "</td>",
        );
        $columnPromedio1T = array(
          "start" => "<td width=\"$this->WIDTH_NOTA\" rowspan=\"$area_rowspan\">",
          "contentColumn" => "",
          "end" => "</td>",
        );
        /* Nota y Promedio 2T */
        $columnNota2T = array(
          "start" => "<td width=\"$this->WIDTH_NOTA\">",
          "contentColumn" => "",
          "end" => "</td>",
        );
        $columnPromedio2T = array(
          "start" => "<td width=\"$this->WIDTH_NOTA\" rowspan=\"$area_rowspan\">",
          "contentColumn" => "",
          "end" => "</td>",
        );
        /* Nota y Promedio 3T */
        $columnNota3T = array(
          "start" => "<td width=\"$this->WIDTH_NOTA\">",
          "contentColumn" => "",
          "end" => "</td>",
        );
        $columnPromedio3T = array(
          "start" => "<td width=\"$this->WIDTH_NOTA\" rowspan=\"$area_rowspan\">",
          "contentColumn" => "",
          "end" => "</td>",
        );
        array_push($row['contentRow'], $columnArea); /* Area */
        array_push($row['contentRow'], $columnMateria);
        array_push($row['contentRow'], $columnNota1T);
        array_push($row['contentRow'], $columnPromedio1T );
        array_push($row['contentRow'], $columnNota2T );
        array_push($row['contentRow'], $columnPromedio2T );
        array_push($row['contentRow'], $columnNota3T);
        array_push($row['contentRow'], $columnPromedio3T );

        array_push($arrayRepeatArea, $notaValue["id_area"]); // Agregamos a Area Repetido
      } else {
        /* Columna Vacia */
        $columnEmpty = array(
          "start" => "",
          "contentColumn" => "",
          "end" => "",
        );
        /* Materia */
        $columnMateria = array(
          "start" => "<td width=\"$this->WIDTH_MATERIA\">",
          "contentColumn" => $notaValue["nombre_materia"],
          "end" => "</td>",
        );
        $columnNota1T = array(
          "start" => "<td width=\"$this->WIDTH_NOTA\">",
          "contentColumn" => "",
          "end" => "</td>",
        );
        $columnNota2T= array(
          "start" => "<td width=\"$this->WIDTH_NOTA\">",
          "contentColumn" => "",
          "end" => "</td>",
        );
        $columnNota3T= array(
          "start" => "<td width=\"$this->WIDTH_NOTA\">",
          "contentColumn" => "",
          "end" => "</td>",
        );
        array_push($row['contentRow'], $columnEmpty); /* Area Vacio */
        array_push($row['contentRow'], $columnMateria);
        array_push($row['contentRow'], $columnNota1T);
        array_push($row['contentRow'], $columnEmpty); /* Promedio 1T Vacio */
        array_push($row['contentRow'], $columnNota2T);
        array_push($row['contentRow'], $columnEmpty); /* Promedio 2T Vacio */
        array_push($row['contentRow'], $columnNota3T);
        array_push($row['contentRow'], $columnEmpty); /* Promedio 3T Vacio */
      }
      array_push($arrayNotas, $row);
    }
    unset($arrayRepeatCampo);
    unset($arrayRepeatArea);
    return $arrayNotas;
  }

  /* Agrega notas y promedios (sin porcentajes) a la tabla */
  private function addNotas($tabla, $notas, $columnIndex) {
    /* Colocamos las notas */
    foreach ($notas as $key => $notaValue) {
      $tabla[$key]["contentRow"][$columnIndex]["contentColumn"] = $notaValue["total"];
    }
    $arrayRepeat = [];
    $arrayAreas = [];
    /* Calculamos el promedio */
    foreach ($notas as $key => $value) {
      if(!is_numeric(array_search($value["id_area"], $arrayRepeat))) {
        array_push($arrayRepeat, $value["id_area"]);
        array_push($arrayAreas, array(
          "id_area" => $value["id_area"],
          "rows" => 1,
          "materias" => array(array(
            "total" => $value["total"],
          )),
        ));
      } else {
        $index = array_search($value["id_area"], $arrayRepeat);
        $arrayAreas[$index]["rows"] = $arrayAreas[$index]["rows"] + 1;
        array_push($arrayAreas[$index]["materias"], array(
          "total" => $value["total"],
        ));
      }
    }

    $notasIndex = 0;
    /* Llenamos las notas promedios */
    foreach ($arrayAreas as $key => $area) {
      $suma = 0;
      $notasVacias = 0;
      foreach($area["materias"] as $key => $value) {
        // Si tiene una nota vacia no sumar
        if($value["total"] != "-") {
          $suma = $suma + $value["total"];
        } else {
          $notasVacias = $notasVacias + 1;
        }
      }
      $notasSumadas = $area["rows"] - $notasVacias;
      if($notasSumadas != 0) {
        $promedio = round($suma/$notasSumadas);
        $tabla[$notasIndex]["contentRow"][$columnIndex + 1]["contentColumn"] = $promedio;
      } else {
        $tabla[$notasIndex]["contentRow"][$columnIndex + 1]["contentColumn"] = '-';
      }
      $notasIndex = $notasIndex + $area["rows"];
    }

    unset($arrayRepeat);
    unset($arrayAreas);
    return $tabla;
  }
  
  /* Agregas notas y promedios con porcentajes a la tabla*/
  private function addNotasPorcentuales($tabla, $notas, $notasPorcentuales, $columnIndex) {
    foreach ($notas as $key => $notaValue) {
      $tabla[$key]["contentRow"][$columnIndex]["contentColumn"] = $notaValue["total"];
    }
    $arrayRepeat = [];
    $arrayAreas = [];

    /* Calculamos el promedio */
    foreach ($notasPorcentuales as $key => $value) {
      if(!is_numeric(array_search($value->id_area, $arrayRepeat))) {
        array_push($arrayRepeat, $value->id_area);
        array_push($arrayAreas, array(
          "id_area" => $value->id_area,
          "rows" => 1,
          "materias" => array(array(
            "total" => $value->total,
          )),
        ));
      } else {
        $index = array_search($value->id_area, $arrayRepeat);
        $arrayAreas[$index]["rows"] = $arrayAreas[$index]["rows"] + 1;
        array_push($arrayAreas[$index]["materias"], array(
          "total" => $value->total,
        ));
      }
    }

    $notasIndex = 0;
    /* Llenamos las notas promedios */
    foreach ($arrayAreas as $key => $area) {
      $suma = 0;
      $notasVacias = 0;
      foreach($area["materias"] as $key => $value) {
        if($value["total"] != "-" && $value["total"] != "0") {
          $valueAdd = floatval(trim($value["total"]));
          $suma = $suma + $valueAdd;
        } else {
          $notasVacias = $notasVacias + 1;
        }
      }
      if($notasVacias == 0){
        $tabla[$notasIndex]["contentRow"][$columnIndex + 1]["contentColumn"] = round($suma);
      } else {
        $tabla[$notasIndex]["contentRow"][$columnIndex + 1]["contentColumn"] = "-";
      }
      $notasIndex = $notasIndex + $area["rows"];
    }

    unset($arrayRepeat);
    unset($arrayAreas);
    return $tabla;
  }

  /* Habilitar Boletin */
  public function enabledBoletin($id_not_pro) {
    $conex = $this->pdo;
    $sql = "UPDATE
      nota_prom
    SET boletin = 0
    WHERE id_not_pro = $id_not_pro";
    $query = $conex->prepare($sql);
    $idInsert = $query->execute();

    if ($query->rowCount() != 0) {
      $result = $idInsert;
      $message = 'Boletín habilitado';
      $status = true;
    } else {
      $result = -1;
      $message = "No se puede habilitar el boletín";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  /* Deshabilitar Boletin */
  public function disabledBoletin($id_not_pro) {
    $conex = $this->pdo;
    $sql = "UPDATE
      nota_prom
    SET boletin = 1
    WHERE id_not_pro = $id_not_pro";
    $query = $conex->prepare($sql);
    $idInsert = $query->execute();

    if ($query->rowCount() != 0) {
      $result = $idInsert;
      $message = 'Boletín deshabilitado';
      $status = true;
    } else {
      $result = -1;
      $message = "No se puede deshabilitar el boletín";
      $status = false;
    }
    return $this->response->send($result, $status, $message, []);
  }

  /* Obtiene los trimestres disponibles */
  private function getTrimestres($trimestre) {
    if($trimestre == $this->PARAMS_PRIMER_TRIMESTRE) {
      return [$this->PRIMER_TRIMESTRE];
    }
    if($trimestre == $this->PARAMS_SEGUNDO_TRIMESTRE) {
      return [$this->PRIMER_TRIMESTRE, $this->SEGUNDO_TRIMESTRE];
    }
    if($trimestre == $this->PARAMS_TERCER_TRIMESTRE) {
      return [$this->PRIMER_TRIMESTRE, $this->SEGUNDO_TRIMESTRE, $this->TERCER_TRIMESTRE];
    }
  }
  private function getTrimestreAid($trimestre) {
    if($trimestre == $this->PARAMS_PRIMER_TRIMESTRE) {
      return $this->PRIMER_TRIMESTRE;
    }
    if($trimestre == $this->PARAMS_SEGUNDO_TRIMESTRE) {
      return $this->SEGUNDO_TRIMESTRE;
    }
    if($trimestre == $this->PARAMS_TERCER_TRIMESTRE) {
      return $this->TERCER_TRIMESTRE;
    }
  }

  /* Obtiene cantidad de campos repetidos */
  private function getRepeatCampo($id_campo, $notas) {
    $countAreas = 0;
    foreach ($notas as $key => $value) {
      $valueFormatted = (array) $value;
      if($valueFormatted["id_campo"] == $id_campo) {
        $countAreas = $countAreas + 1;
      }
    }
    return $countAreas;
  }

  /* Obtiene cantidad de areas repetidas */
  private function getRepeatArea($id_area, $notas) {
    $countAreas = 0;
    foreach ($notas as $key => $value) {
      $valueFormatted = (array) $value;
      if($valueFormatted["id_area"] == $id_area) {
        $countAreas = $countAreas + 1;
      }
    }
    return $countAreas;
  }

  /* Filtra las notas generales */
  public function filterNotas($cursoMaterias, $notas) {
    $resultArray = array();
    foreach ($cursoMaterias as $key => $materia) {
      $search = false;
      foreach ($notas as $key => $value) {
        if($value->id_mat == $materia->id_mat) {
          if($value->total == "0") {
            array_push($resultArray, array(
              "id_campo" => $materia->id_campo,
              "nombre_campo" => $materia->nombre_campo,
              "id_area" => $materia->id_area,
              "nombre_area" => $materia->nombre_area,
              "id_mat" => $materia->id_mat,
              "nombre_materia" => $materia->nombre_materia,
              "total" => "-"
            ));
          } else {
            //if($value->total <= "51") {
            array_push($resultArray, array(
              "id_campo" => $materia->id_campo,
              "nombre_campo" => $materia->nombre_campo,
              "id_area" => $materia->id_area,
              "nombre_area" => $materia->nombre_area,
              "id_mat" => $materia->id_mat,
              "nombre_materia" => $materia->nombre_materia,
              "total" => $value->total
            ));
            //}
          }
          $search = true;
        }
      }
      if(!$search) {
        /* Si no encuentra la materia, no tendra nota */
        array_push($resultArray, array(
          "id_campo" => $materia->id_campo,
          "nombre_campo" => $materia->nombre_campo,
          "id_area" => $materia->id_area,
          "nombre_area" => $materia->nombre_area,
          "id_mat" => $materia->id_mat,
          "nombre_materia" => $materia->nombre_materia,
          "total" => "-"
        ));
      }
    }
    return $resultArray;
  }

  /* Filtra las notas generales */
  public function filterNotasReprobados($cursoMaterias, $notas) {
    $resultArray = array();
    foreach ($cursoMaterias as $key => $materia) {
      $search = false;
      foreach ($notas as $key => $value) {
        if($value->id_mat == $materia->id_mat) {
          if($value->total == "0") {
            array_push($resultArray, array(
              "id_campo" => $materia->id_campo,
              "nombre_campo" => $materia->nombre_campo,
              "id_area" => $materia->id_area,
              "nombre_area" => $materia->nombre_area,
              "id_mat" => $materia->id_mat,
              "nombre_materia" => $materia->nombre_materia,
              "total" => "-"
            ));
          } else {
            if($value->total <= "51") {
              array_push($resultArray, array(
                "id_campo" => $materia->id_campo,
                "nombre_campo" => $materia->nombre_campo,
                "id_area" => $materia->id_area,
                "nombre_area" => $materia->nombre_area,
                "id_mat" => $materia->id_mat,
                "nombre_materia" => $materia->nombre_materia,
                "total" => $value->total
              ));
            }
          }
          $search = true;
        }
      }
      if(!$search) {
        /* Si no encuentra la materia, no tendra nota */
        array_push($resultArray, array(
          "id_campo" => $materia->id_campo,
          "nombre_campo" => $materia->nombre_campo,
          "id_area" => $materia->id_area,
          "nombre_area" => $materia->nombre_area,
          "id_mat" => $materia->id_mat,
          "nombre_materia" => $materia->nombre_materia,
          "total" => "-"
        ));
      }
    }
    return $resultArray;
  }

  /* Filtra errores */
  public function filterErrores($notas, $curso, $turno) {
    if($turno == "SM" && $curso == "1C")
    {
      /*
        Existe un problema de asignación
        1C-SM deberia tener asignado informatica 14
        tiene asignado informatica 13 (eso esta mal)
      */
      $notasReparadas = [];
      foreach ($notas as $key => $val) {
        $value = (object) $val;
        if($value->id_mat == 13) {
          $newValue = $value;
          $newValue->id_mat = "14";
          $newValue->id_area = "9";
          $newValue->nombre_area = "TECNICA TECNOLOGICA GENERALES";
          array_push($notasReparadas, $newValue);
        } else {
          array_push($notasReparadas, $value);
        }
      }
      return $notasReparadas;
    } else {
      return $notas;
    }
  }

  /* Filtra especialidades y Materias repetidas */
  public function filterEspecialidad($notas_entry, $curso, $turno) {
    $notas = (array) $notas_entry;
    $materiasEspecialidad = getEspecialidad($curso, $turno); // helper
    $materiasFilterA = array(); // Array con ids no repetidos
    $materiasRepeat = [];

    /* filtramos materias con ID repetido */
    for($key=0; $key < count($notas); $key++) {
      /* Verificamos si el ID actual esta en el array de repetidos */
      $notaValue = (array) $notas[$key];
      if(!is_numeric(array_search($notaValue["id_mat"], $materiasRepeat))) {
        /* Si no esta seguimos filtramos */
        $value = $notaValue;
        $idSearch = $value["id_mat"]; // ID Actual
        $total = -1;
        $valueInsert = null;
        /* Buscamos repetidos */
        for($index=$key; $index < count($notas); $index++){
          $notaIndex = (array) $notas[$index];
          if($idSearch == $notaIndex["id_mat"]) {
            if($notaIndex["total"] > $total) {
              $total = $notaIndex["total"];
              $valueInsert = $notaIndex;
              /* Si encontramos repetidos, agregamos al array de ids repetidos */
              array_push($materiasRepeat, $notaIndex["id_mat"]);
            }
          }
        }
        array_push($materiasFilterA, $valueInsert);
      }
    }

    if (count($materiasEspecialidad) !== 0) {
      $materiaA = $materiasEspecialidad[0];
      $materiaB = $materiasEspecialidad[1];
      $materiasFilterB = array(); // Array con especialidades filtradas
      $materiasRepeat = [];
      /* filtramos las materias de especialidad */
      for($index=0; $index < count($materiasFilterA); $index++) {
        $valueA = $materiasFilterA[$index];
        /* Verificamos el ID de la especialidad si ya se verifico */
        if(!is_numeric(array_search($valueA["id_mat"], $materiasRepeat))) {
          /* Si no esta seguimos filtramos */
          if($valueA["id_mat"] == $materiaA || $valueA["id_mat"] == $materiaB) {
            // echo "Ingresa al filter ".$index."<br>";
            $valueInsert = null;
            if($valueA["id_mat"] == $materiaA) { // controlamos la siguiente materia a comparar
              // comparamos con la segunda especialidad
              $materia_compare = $materiaB;
            } else {
              // comparamos con la primera especialidad
              $materia_compare = $materiaA;
            }
            foreach($materiasFilterA as $key => $valueB) {
              if($valueB["id_mat"] == $materia_compare) {
                /* comparamos valores de las dos especialidades */
                if($valueA["total"] > $valueB["total"]) {
                  $valueInsert = $valueA;
                } else {
                  $valueInsert = $valueB;
                }
                /* Si encontramos la especialidad, agregamos al array de ids repetidos */
                array_push($materiasRepeat, $valueB["id_mat"]);
              }
            }
            array_push($materiasFilterB, $valueInsert);
          } else {
            array_push($materiasFilterB, $valueA);
          }
        }
      }
      return $materiasFilterB;
    } else {
      return $materiasFilterA;
    }
  }

  /* Obtiene los campos -> areas -> materias */
  public function getCamposAreasMaterias($curso, $turno) {
    $materiasQuery = getMateriasQuery($curso, $turno, 'MA.id_mat = '); // helper
    $conex = $this->pdo;
    $sql = "SELECT
      CA.nombre as nombre_campo,
      CA.id_campo,
      CA.sigla as sigla_campo,
      AR.nombre as nombre_area,
      AR.id_area,
      AR.sigla as sigla_area,
      MA.nombre as nombre_materia,
      MA.id_mat
    FROM campo as CA
    INNER JOIN areas as AR
    ON CA.id_campo = AR.id_campo
    INNER JOIN materias as MA
    ON MA.id_area = AR.id_area
    WHERE
    ($materiasQuery)
    ORDER BY CA.id_campo, AR.id_area";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      return $result;
    } else {
      return [];
    }
  }

  /* Obtiene notas del estudiante */
  public function getNotasEstudiante($id_est, $curso, $turno, $gestion, $trimestre) {
    $materiasQuery = getMateriasQuery($curso, $turno, 'NT.id_mat = '); // helper
    $conex = $this->pdo;
    $sql = "SELECT
      NT.id_mat,
      MAT.nombre,
      NT.total,
      NT.id_bi,
      NT.id_est,
      AR.id_area,
      AR.nombre as nombre_area,
      CAM.id_campo, 
      CAM.nombre as nombre_campo
    FROM nota_trimestre as NT
    INNER JOIN materias as MAT
    ON NT.id_mat = MAT.id_mat
    INNER JOIN areas as AR
    ON MAT.id_area = AR.id_area
    INNER JOIN campo as CAM
    ON AR.id_campo = CAM.id_campo
    WHERE NT.gestion = $gestion AND NT.id_est = $id_est AND NT.id_bi = $trimestre AND
    ($materiasQuery)
    ORDER BY CAM.id_campo, AR.id_area";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      return $result;
    } else {
      return [];
    }
  }

  /* Obtiene si tiene el boletin habilitado */
  public function getBoletin($id_est, $gestion) {
    $conex = $this->pdo;
    $sql = "SELECT *
      FROM nota_prom
      WHERE gestion = $gestion
      AND id_est = $id_est
      AND boletin = 0
      ORDER BY id_not_pro";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      /* (boletin = 0, esta habilitado || boletin = 1, deshabilitado) */
      return true;
    } else {
      return false;
    }
  }

  /* Obtiene datos del estudiante */
  public function getEstudiante($curso, $turno, $id_est, $gestion) {
    $conex = $this->pdo;
    $sql = "SELECT
        EST.id_est,
        EST.appaterno,
        EST.apmaterno,
        EST.nombre
      FROM nota_prom as NP
      INNER JOIN estudiantes as EST
      ON NP.id_est = EST.id_est
      WHERE NP.codigo LIKE '%$curso-$turno%'
      AND NP.gestion = $gestion
      AND EST.inscrito = 1
      AND EST.id_est = $id_est
      ORDER BY EST.appaterno, EST.apmaterno, EST.nombre ASC";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      /* (boletin = 0, esta habilitado || boletin = 1, deshabilitado) */
      $result = $query->fetchObject();
      return $result;
    } else {
      return false;
    }
  }

  /* Obtiene datos del estudiante */
  public function getEstudiantes($curso, $turno, $gestion) {
    $conex = $this->pdo;
    $sql = "SELECT
        EST.id_est,
        EST.appaterno,
        EST.apmaterno,
        EST.nombre
      FROM nota_prom as NP
      INNER JOIN estudiantes as EST
      ON NP.id_est = EST.id_est
      WHERE NP.codigo LIKE '%$curso-$turno%'
      AND NP.gestion = $gestion
      AND EST.inscrito = 1
      ORDER BY EST.appaterno, EST.apmaterno, EST.nombre ASC";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      return $result;
    } else {
      return false;
    }
  }

  /* Genera un PDF con mensaje de Error */
  private function generateError() {
    $htmlNotas = $this->generateTablaError();
    $this->PDFCreator($htmlNotas);
  }

  /* Funcion de entrada para generar el boletin */
  /*
    POSIBLES ERRORES
    - tener distinas especialidades en los trimestres en SM
  */
  public function generarBoletin($trimestre, $estudiante) {
    /**
      PARAMETRO: trimestre [number]
      FORMATO: [ID_BIMESTRE]
      EJEMPLO: 5
      --------------------------------------------------
      PARAMETRO: estudiante [string]
      FORMATO: [CURSO]-[TURNO]-[GESTION]-[ID_ESTUDIANTE]
      EJEMPLO: 2A-SM-2021-438
    **/
    $separeParams = explode("-", $estudiante);
    $curso = $separeParams[0];
    $turno = $separeParams[1];
    $gestion = $separeParams[2];
    $id_est = $separeParams[3];
    ob_start(); // activamos el buffer
    if($this->getBoletin($id_est, $gestion)){
      /* Generar el boletin */
      $this->generateContent($curso, $turno, $gestion, $id_est, $trimestre);
    } else {
      /* No tiene habilitado el boletin */
      $this->generateError();
    }
  }

  public function generarBoletinTrimestreCurso($curso = "1A", $turno = "PM", $trimestre = "primer", $gestion=2021) {
    ob_start(); // activamos el buffer
    /* Generar el boletin */
    $this->generateContentPages($curso, $turno, $trimestre, $gestion);
  }

}
?>
