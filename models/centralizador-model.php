<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CentralizadorModel
{
  private $conexion;
  private $response;

  /* GESTION */
  private $GESTION = 2021;

  private $PRIMER_TRIMESTRE = 5;
  private $SEGUNDO_TRIMESTRE = 6;
  private $TERCER_TRIMESTRE = 7;

  private $PRIMER_TRIMESTRE_LITERAL = "primer";
  private $SEGUNDO_TRIMESTRE_LITERAL  = "segundo";
  private $TERCER_TRIMESTRE_LITERAL  = "tercer";

  public function __construct()
  {
    $this->conexion = new Conexion();
    $this->pdo = $this->conexion->getConexion();
    $this->response = new Response();
    $this->boletin = new BoletinesModel();
  }

  /* Obtiene los trimestres disponibles */
  private function getTrimestres($trimestre)
  {
    if ($trimestre == $this->PRIMER_TRIMESTRE_LITERAL) {
      return $this->PRIMER_TRIMESTRE;
    }
    if ($trimestre == $this->SEGUNDO_TRIMESTRE_LITERAL) {
      return $this->SEGUNDO_TRIMESTRE;
    }
    if ($trimestre == $this->TERCER_TRIMESTRE_LITERAL) {
      return $this->TERCER_TRIMESTRE;
    }
  }

  public function getEstudiantes($curso, $turno, $gestion = 2021)
  {
    $conex = $this->pdo;
    $sql = "SELECT
    * FROM nota_prom as NP
    INNER JOIN estudiantes as EST
    ON NP.id_est = EST.id_est
    WHERE
    NP.codigo LIKE CONCAT('%','$curso-$turno','%')
    AND NP.gestion = $gestion
    ORDER BY EST.appaterno, EST.apmaterno, EST.nombre";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      return $result;
    } else {
      return [];
    }
  }

  public function getEstudiantesParalelo($turno, $paralelo, $gestion = 2021)
  {
    $conex = $this->pdo;
    $sql = "SELECT
    * FROM nota_prom as NP
    INNER JOIN estudiantes as EST
    ON NP.id_est = EST.id_est
    WHERE
    NP.codigo LIKE CONCAT('%','$turno','%')
    AND NP.id_curso LIKE CONCAT('%','$paralelo','%')
    AND NP.gestion = $gestion
    ORDER BY EST.appaterno, EST.apmaterno, EST.nombre";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      return $result;
    } else {
      return [];
    }
  }

  public function getEstudiantesRep($curso, $turno, $trimestre, $gestion = 2021)
  {
    $conex = $this->pdo;
    $sql = "SELECT NP.id_not_pro, NP.codigo, NP.codigo1, NP.debe, NP.repitente, NP.retirado, NP.id_curso, NP.vive, NP.cpse, NP.boletin, NP.prom1, NP.prom2, NP.prom3, NP.prom4, NP.promfinal, NP.gestion, NP.id_est
    FROM nota_prom as NP
    INNER JOIN estudiantes as EST
      ON NP.id_est = EST.id_est
    INNER JOIN nota_trimestre as NT
      ON NP.id_est = NT.id_est
    WHERE NP.codigo LIKE CONCAT('%','$curso-$turno','%') AND NP.gestion = $gestion AND NT.gestion = $gestion AND NT.id_bi = $trimestre
    GROUP BY NP.id_not_pro, EST.id_est, NT.id_nota_trimestre
    ORDER BY EST.appaterno, EST.apmaterno, EST.nombre";
    $query = $conex->prepare($sql);
    $query->execute();
    if ($query->rowCount() != 0) {
      $result = $query->fetchAll(PDO::FETCH_OBJ);
      return $result;
    } else {
      return [];
    }
  }

  private function getNotasEstudiantes($curso, $turno, $trimestre, $cursoMaterias = null, $cursoEstudiantes = null, $gestion = 2021)
  {
    if (is_null($cursoMaterias)) {
      $materias = $this->boletin->getCamposAreasMaterias($curso, $turno);
    } else {
      $materias = $cursoMaterias;
    }
    if (is_null($cursoEstudiantes)) {
      $estudiantes = $this->getEstudiantes($curso, $turno, $gestion);
    } else {
      $estudiantes = $cursoEstudiantes;
    }
    // $gestion = $this->GESTION;
    $notasTrimestre = array();
    foreach ($estudiantes as $key => $value) {
      $id_est = $value->id_est;
      $notas = $this->boletin->getNotasEstudiante($id_est, $curso, $turno, $gestion, $trimestre);
      $notasFilter = $this->boletin->filterNotas($materias, $notas); // Filtramos las notas
      $notasFilterErrores = $this->boletin->filterErrores($notasFilter, $curso, $turno); // Filtramos errores de asignacion
      $notasFilterEspecialidad = $this->boletin->filterEspecialidad($notasFilterErrores, $curso, $turno); // Filtramos materias de especialidad
      array_push($notasTrimestre, $notasFilterEspecialidad);
    }
    return $notasTrimestre;
  }

  private function getNotasEstudiantesReprobados($curso, $turno, $trimestre, $cursoMaterias = null, $cursoEstudiantes = null, $gestion = 2021)
  {
    if (is_null($cursoMaterias)) {
      $materias = $this->boletin->getCamposAreasMaterias($curso, $turno);
    } else {
      $materias = $cursoMaterias;
    }
    if (is_null($cursoEstudiantes)) {
      $estudiantes = $this->getEstudiantes($curso, $turno, $gestion);
    } else {
      $estudiantes = $cursoEstudiantes;
    }
    // $gestion = $this->GESTION;
    $notasTrimestre = array();
    foreach ($estudiantes as $key => $value) {
      $id_est = $value->id_est;
      $notas = $this->boletin->getNotasEstudiante($id_est, $curso, $turno, $gestion, $trimestre);
      $notasFilter = $this->boletin->filterNotasReprobados($materias, $notas); // Filtramos las notas
      $notasFilterErrores = $this->boletin->filterErrores($notasFilter, $curso, $turno); // Filtramos errores de asignacion
      $notasFilterEspecialidad = $this->boletin->filterEspecialidad($notasFilterErrores, $curso, $turno); // Filtramos materias de especialidad
      array_push($notasTrimestre, $notasFilterEspecialidad);
    }
    return $notasTrimestre;
  }

  private function getCentralizadorData($curso = "1A", $turno = "PM", $trimestre = "primer", $gestion = 2021)
  {
    $trimestreCurrent = $this->getTrimestres($trimestre);
    $cursoEstudiantes = $this->getEstudiantes($curso, $turno, $gestion);
    $cursoMaterias = $this->boletin->getCamposAreasMaterias($curso, $turno);
    $notasTrimestre = $this->getNotasEstudiantes($curso, $turno, $trimestreCurrent, $cursoMaterias, $cursoEstudiantes, $gestion);
    return $notasTrimestre;
  }

  private function generateContent($curso, $turno, $trimestre = "primer", $gestion = 2021, $save = false)
  {
    $paralelo = $curso;
    if (strpos($curso, "S") !== false) {
      $curso = substr($curso, -2);
      $cursoEstudiantes = $this->getEstudiantesParalelo($turno, $paralelo, $gestion);
    } else {
      $cursoEstudiantes = $this->getEstudiantes($curso, $turno, $gestion);
    }
    $spreadsheet = new Spreadsheet();
    /******** Generamos el Centralizador de manera Normal ********/
    $sheet = $spreadsheet->getActiveSheet();
    if ($save) {
      $titleTab = 'modelo';
    } else {
      $titleTab = 'Centralizador ' . $curso . ' ' . $turno;
    }
    $sheet->setTitle($titleTab);

    $trimestreCurrent = $this->getTrimestres($trimestre);
    /* Obtenemos los cursoMaterias WORK*/
    $cursoMaterias = $this->boletin->getCamposAreasMaterias($curso, $turno);
    $notasTrimestre = $this->getNotasEstudiantes($curso, $turno, $trimestreCurrent, $cursoMaterias, $cursoEstudiantes, $gestion);
    $estudiantesMaterias = array();
    $indexColumnLast = 0;

    /* Agregamos tamaño a las columnas */
    $this->setDimensionColumns($sheet);
    /* Agregamos formatting cells */
    $this->setFormattingCells($sheet, $curso, $turno, $cursoMaterias, $cursoEstudiantes);
    /* Generamos la cabecera principal */
    $this->generateHeaderInfo($sheet, $curso, $turno, $trimestre, $gestion);
    /* Generamos la cabecera de la tabla */
    $this->generateHeaderTable($sheet, $curso, $turno, $cursoMaterias, $cursoEstudiantes, false, $estudiantesMaterias);
    /* Generamos las notas del estudiante */
    $this->generateNotasInfo($sheet, $curso, $turno, $notasTrimestre, false, $estudiantesMaterias, $indexColumnLast);
    /* Generamos a los 5 mejores estudiantes */
    $this->generateMejoresNotas($sheet, $estudiantesMaterias, $indexColumnLast);

    /******** Generamos el Centralizador de alumnos reprobados o notas faltantes ********/
    $spreadsheet->createSheet();
    $spreadsheet->setActiveSheetIndex(1);
    $sheet2 = $spreadsheet->getActiveSheet();
    $titleTab = 'Reprobados';
    $sheet2->setTitle($titleTab);

    $inc = 0;
    for ($i = 0; $i < count($notasTrimestre); $i++) {
      $inc = 0;
      $v_not_est = $notasTrimestre[$i];
      foreach ($v_not_est as $key => $value) {
        $vacio = false;
        if ($value['total'] === '-')
          $vacio = true;
        $total = (int)$value['total'];
        if ($total >= 52 || $vacio) {
          $inc++;
        }
      }
      if (count($v_not_est) === $inc) {
        array_splice($notasTrimestre, $i, 1);
        array_splice($cursoEstudiantes, $i, 1);
        $i--;
      }
    }

    /* Agregamos tamaño a las columnas */
    $this->setDimensionColumns($sheet2);
    /* Agregamos formatting cells */
    $this->setFormattingCells($sheet2, $curso, $turno, $cursoMaterias, $cursoEstudiantes);
    /* Generamos la cabecera principal */
    $this->generateHeaderInfo($sheet2, $curso, $turno, $trimestre, $gestion);
    /* Generamos la cabecera de la tabla */
    $this->generateHeaderTable($sheet2, $curso, $turno, $cursoMaterias, $cursoEstudiantes);
    /* Generamos las notas del estudiante */
    $this->generateNotasInfo($sheet2, $curso, $turno, $notasTrimestre);

    /******* Activamos el primer sheet *******/
    $spreadsheet->setActiveSheetIndex(0);

    /* BUILD EXCEL */
    $writer = new Xlsx($spreadsheet);
    if ($save) {
      //$writer->save('/home/'.$_ENV['US_SERVER'].'/coder/upload/Centralizador.xlsx');
      $writer->save($_ENV['PATH_UPLOAD_SERVER'] . '/Centralizador.xlsx');
    } else {
      $writer->save('php://output');
    }
  }

  private function setDimensionColumns($sheet)
  {
    $sheet->getRowDimension('11')->setRowHeight(220);
    $sheet->getColumnDimension('A')->setWidth(4);
    $sheet->getColumnDimension('B')->setWidth(16);
    $sheet->getColumnDimension('C')->setWidth(28);
    $sheet->getColumnDimension('D')->setWidth(4);
    $sheet->getColumnDimension('E')->setWidth(4);
    $sheet->getColumnDimension('F')->setWidth(4);
    $sheet->getColumnDimension('G')->setWidth(4);
    $sheet->getColumnDimension('H')->setWidth(4);
    $sheet->getColumnDimension('I')->setWidth(4);
    $sheet->getColumnDimension('J')->setWidth(4);
    $sheet->getColumnDimension('K')->setWidth(4);
    $sheet->getColumnDimension('L')->setWidth(4);
    $sheet->getColumnDimension('M')->setWidth(4);
    $sheet->getColumnDimension('N')->setWidth(4);
    $sheet->getColumnDimension('O')->setWidth(4);
    $sheet->getColumnDimension('P')->setWidth(4);
    $sheet->getColumnDimension('Q')->setWidth(4);
    $sheet->getColumnDimension('R')->setWidth(4);
    $sheet->getColumnDimension('S')->setWidth(4);
    $sheet->getColumnDimension('T')->setWidth(4);
    $sheet->getColumnDimension('U')->setWidth(4);
    $sheet->getColumnDimension('V')->setWidth(4);
    $sheet->getColumnDimension('W')->setWidth(4);
    $sheet->getColumnDimension('X')->setWidth(4);
    $sheet->getColumnDimension('Y')->setWidth(4);
    $sheet->getColumnDimension('Z')->setWidth(4);
    $sheet->getColumnDimension('AA')->setWidth(4);
    $sheet->getColumnDimension('AB')->setWidth(4);
    $sheet->getColumnDimension('AC')->setWidth(4);
    $sheet->getColumnDimension('AD')->setWidth(4);
    $sheet->getColumnDimension('AE')->setWidth(4);
    $sheet->getColumnDimension('AF')->setWidth(4);
    $sheet->getColumnDimension('AG')->setWidth(4);
    $sheet->getColumnDimension('AH')->setWidth(4);
    $sheet->getColumnDimension('AI')->setWidth(4);
    $sheet->getColumnDimension('AJ')->setWidth(4);
    $sheet->getColumnDimension('AK')->setWidth(4);
    $sheet->getColumnDimension('AL')->setWidth(4);
    $sheet->getColumnDimension('AM')->setWidth(4);
    $sheet->getColumnDimension('AN')->setWidth(4);
  }

  private function setFormattingCells($sheet, $curso, $turno, $cursoMaterias, $cursoEstudiantes)
  {
    $columns = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN'];
    $materias = $this->formattedMaterias($curso, $turno, $cursoMaterias);

    $conditional = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
    $conditional->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
    $conditional->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN);
    $conditional->addCondition('51');
    $conditional->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
    $conditional->getStyle()->getFont()->setBold(true);
    $conditionalStyles[] = $conditional;

    $idAreaRepeat = -1;
    $areasCount = 0;
    $totalColumns = 0;
    foreach ($materias as $key => $value) {
      if ($key === 0) { // Agregamos primero
        $idAreaRepeat = $value->id_area;
      }
      if ($key === (count($materias) - 1)) { // Agregamos ultimo
        $totalColumns = $totalColumns + $areasCount;
      }
      /* Verificamos */
      if ($idAreaRepeat !== $value->id_area) {
        if ($areasCount > 1) {
          $areasCount = $areasCount + 1;
          $totalColumns = $totalColumns + $areasCount;
        } else {
          $totalColumns = $totalColumns + $areasCount;
        }
        $idAreaRepeat = $value->id_area;
        $areasCount = 1;
      } else {
        $areasCount = $areasCount + 1;
      }
    }
    $indexRow = 12;
    $totalColumns = $totalColumns + 1; // Agregamos el total

    for ($i = 0; $i < count($cursoEstudiantes); $i++) {
      for ($j = 0; $j < $totalColumns; $j++) {
        $sheet->getStyle("{$columns[$j]}{$indexRow}")->setConditionalStyles($conditionalStyles);
      }
      $indexRow++;
    }
  }

  private function formattedMaterias($curso, $turno, $cursoMaterias)
  {
    $materiasEspecialidad = getControlMaterias($curso, $turno); // helper
    if ($materiasEspecialidad) {
      $formatted = false;
      $materiasFormatted = array();
      foreach ($cursoMaterias as $key => $value) {
        if (is_numeric(array_search($value->id_mat, $materiasEspecialidad))) {
          if (!$formatted) {
            $value->nombre_materia = "TEC.TECNOLOGICA ESPECIALIZADA";
            array_push($materiasFormatted, $value);
            $formatted = true;
          }
        } else {
          array_push($materiasFormatted, $value);
        }
      }
      return $materiasFormatted;
    } else {
      return $cursoMaterias;
    }
  }

  private function formattedMateriasErrores($curso, $turno, $cursoMaterias)
  {
    if ($turno == "SM" && $curso == "1C") {
      /*
        Existe un problema de asignación
        1C-SM deberia tener asignado informatica 14
        tiene asignado informatica 13 (eso esta mal)
      */
      $notasReparadas = [];
      foreach ($cursoMaterias as $key => $val) {
        $value = (object) $val;
        if ($value->id_mat == 13) {
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
      return $cursoMaterias;
    }
  }

  private function generateHeaderInfo($sheet, $curso, $turno, $trimestre, $gestion = 2021)
  {
    $cursoCurrent = checkCurso($curso);
    $nivelCurrent = checkGrado($turno) . ' ' . checkTurno($turno);
    $colegioCurrent = checkColegio($turno);
    /* Agregamos Logo del colegio */
    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Colegio Don Bosco');
    $drawing->setDescription('Documento Oficial de notas, Colegio Don Bosco');
    $drawing->setPath('utils/images/logo-min-min.png');
    $drawing->setCoordinates('A1');
    $drawing->setWidthAndHeight(70, 90);
    $drawing->setOffsetX(30);
    $drawing->getShadow()->setVisible(true);
    $drawing->getShadow()->setDirection(45);
    $drawing->setWorksheet($sheet);
    /* Agregamos titulo */
    $trimestreLiteral = checkTrimestre($trimestre); // helper
    $sheet->mergeCells('C3:R3');
    $sheet->setCellValue('C3', 'CENTRALIZADOR ' . $trimestreLiteral);
    $sheet->getStyle('C3:R3')->applyFromArray(headerTitleStyle());
    /* Agregamos Información */
    $sheet->setCellValue('B6', 'GRADO ESC.:');
    $sheet->getStyle('B6')->applyFromArray(headerInfoStyle());
    $sheet->getStyle('C6')->applyFromArray(headerInfoDetailStyle());
    $sheet->setCellValue('C6', $cursoCurrent);

    $sheet->setCellValue('B7', 'NIVEL:');
    $sheet->getStyle('B7')->applyFromArray(headerInfoStyle());
    $sheet->getStyle('C7')->applyFromArray(headerInfoDetailStyle());
    $sheet->setCellValue('C7', $nivelCurrent);

    $sheet->mergeCells('F6:H6');
    $sheet->setCellValue('F6', 'GESTION:');
    $sheet->getStyle('F6:H6')->applyFromArray(headerInfoStyle());
    $sheet->mergeCells('I6:Q6');
    $sheet->getStyle('I6:Q6')->applyFromArray(headerInfoDetailStyle());
    $sheet->setCellValue('I6', $gestion);

    $sheet->mergeCells('F7:H7');
    $sheet->setCellValue('F7', 'UNID. EDU.:');
    $sheet->getStyle('F7:H7')->applyFromArray(headerInfoStyle());
    $sheet->mergeCells('I7:Q7');
    $sheet->getStyle('I7:Q7')->applyFromArray(headerInfoDetailStyle());
    $sheet->setCellValue('I7', $colegioCurrent);
  }

  private function generateHeaderTable($sheet, $curso, $turno, $cursoMaterias, $cursoEstudiantes, $save = false, &$estudiantesMaterias = array())
  {
    $columns = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN'];
    $indexRow = 12;
    $cursoMateriasFilter = $this->formattedMaterias($curso, $turno, $cursoMaterias);
    $materias = $this->formattedMateriasErrores($curso, $turno, $cursoMateriasFilter);

    /* Generamos el Nro. y Nombres */
    $sheet->mergeCells('A9:A11');
    $sheet->setCellValue('A9', 'Nº');
    $sheet->getStyle('A9:A11')->applyFromArray(headerTableStyle());
    $sheet->mergeCells('B9:C11');
    $sheet->setCellValue('B9', 'NOMBRES');
    $sheet->getStyle('B9:C11')->applyFromArray(headerTableStyle());

    /* Generamos la Lista de Estudiantes */
    foreach ($cursoEstudiantes as $key => $estudiante) {
      $sheet->setCellValue("A{$indexRow}", $key + 1);
      $sheet->getStyle("A{$indexRow}")->applyFromArray(headerTableNameStyle());
      $nombreCompleto = trim($estudiante->appaterno . ' ' . $estudiante->apmaterno . ' ' . $estudiante->nombre);
      $sheet->mergeCells("B{$indexRow}:C{$indexRow}");
      $sheet->getStyle("B{$indexRow}:C{$indexRow}")->applyFromArray(notaStyle());
      $sheet->setCellValue("B{$indexRow}", $nombreCompleto);
      array_push($estudiantesMaterias, (object)array('nro' => $key + 1, 'nombre' => $nombreCompleto, 'nota' => ''));
      $indexRow = $indexRow + 1;
    }

    /* Generamos las areas */
    $camposCount = array();
    $areasRepeat = array();
    $areasCurrent = null;
    $areasCount = 0;
    $indexColumn = 0;
    $columnArea = 0;
    foreach ($materias as $key => $value) {
      if (!is_numeric(array_search($value->id_area, $areasRepeat))) {
        array_push($areasRepeat, $value->id_area); // Agregamos a Area Repetido
        if ($areasCount > 1) {
          if (!is_null($areasCurrent)) {
            /* Agregamos el total de areas por campo */
            array_push($camposCount, array(
              "id_campo" => $areasCurrent->id_campo,
              "sigla_campo" => $areasCurrent->sigla_campo,
              "cells" => $areasCount + 1,
            ));
            /* Agregamos Area */
            $sheet->mergeCells("{$columns[$columnArea]}10:{$columns[$columnArea +$areasCount]}10");
            $sheet->getStyle("{$columns[$columnArea]}10:{$columns[$columnArea +$areasCount]}10")->applyFromArray(headerTableAreaStyle());
            $sheet->setCellValue("{$columns[$columnArea]}10", $areasCurrent->sigla_area);
          }

          /* Agregamos el Promedio */
          $sheet->getStyle("{$columns[$indexColumn]}11")->getAlignment()->setTextRotation(90);
          $sheet->getStyle("{$columns[$indexColumn]}11")->applyFromArray(headerTablePromedioStyle());
          $sheet->setCellValue("{$columns[$indexColumn]}11", 'PROMEDIO');
          /* Pasamos a la siguiente columna */
          $indexColumn = $indexColumn + 1;
          $sheet->getStyle("{$columns[$indexColumn]}11")->getAlignment()->setTextRotation(90);
          $sheet->getStyle("{$columns[$indexColumn]}11")->applyFromArray(headerTableMateriaStyle());
          $sheet->setCellValue("{$columns[$indexColumn]}11", $value->nombre_materia);
          /* Reseteamos contador */
          $areasCount = 1;
          $columnArea = $indexColumn;
        } else {
          if (!is_null($areasCurrent)) {
            /* Agregamos el total de areas por campo */
            array_push($camposCount, array(
              "id_campo" => $areasCurrent->id_campo,
              "sigla_campo" => $areasCurrent->sigla_campo,
              "cells" => $areasCount,
            ));
            /* Agregamos Area */
            $sheet->getStyle("{$columns[$columnArea]}10")->applyFromArray(headerTableAreaStyle());
            $sheet->setCellValue("{$columns[$columnArea]}10", $areasCurrent->sigla_area);
          }
          $columnArea = $indexColumn;
          $areasCurrent = $value; // Actualizamos el Area Actual

          /* Pasamos a la siguiente columna */
          $sheet->getStyle("{$columns[$indexColumn]}11")->getAlignment()->setTextRotation(90);
          $sheet->getStyle("{$columns[$indexColumn]}11")->applyFromArray(headerTableMateriaStyle());
          $sheet->setCellValue("{$columns[$indexColumn]}11", $value->nombre_materia);
          $areasCount = 1;
        }
        if ($key === (count($materias) - 1)) { // Agregamos el ultimo a la columna de promedios
          /* Agregamos el total de areas por campo */
          array_push($camposCount, array(
            "id_campo" => $areasCurrent->id_campo,
            "sigla_campo" => $areasCurrent->sigla_campo,
            "cells" => $areasCount,
          ));
          /* Agregamos la ultima Area */
          $sheet->getStyle("{$columns[$columnArea]}10")->applyFromArray(headerTableAreaStyle());
          $sheet->setCellValue("{$columns[$columnArea]}10", $areasCurrent->sigla_area);
        }
        $areasCurrent = $value; // Actualizamos el Area Actual
      } else {
        /* Agregamos la materia */
        $sheet->getStyle("{$columns[$indexColumn]}11")->getAlignment()->setTextRotation(90);
        $sheet->getStyle("{$columns[$indexColumn]}11")->applyFromArray(headerTableMateriaStyle());
        $sheet->setCellValue("{$columns[$indexColumn]}11", $value->nombre_materia);
        $areasCount = $areasCount + 1;
      }
      //$sheet->setCellValue("{$columns[$indexColumn]}10", $value->id_area);
      $indexColumn = $indexColumn + 1;
    }
    $sheet->mergeCells("{$columns[$indexColumn]}9:{$columns[$indexColumn]}11");
    $sheet->getStyle("{$columns[$indexColumn]}9:{$columns[$indexColumn]}11")->getAlignment()->setTextRotation(90);
    $sheet->getStyle("{$columns[$indexColumn]}9:{$columns[$indexColumn]}11")->applyFromArray(headerTablePromedioStyle());
    $sheet->setCellValue("{$columns[$indexColumn]}9", "TOTAL");

    if (!$save) {
      $sheet->mergeCells("{$columns[$indexColumn + 2]}9:{$columns[$indexColumn + 2]}11");
      $sheet->getStyle("{$columns[$indexColumn + 2]}9:{$columns[$indexColumn + 2]}11")->getAlignment()->setTextRotation(90);
      $sheet->getStyle("{$columns[$indexColumn + 2]}9:{$columns[$indexColumn + 2]}11")->applyFromArray(headerTablePromedioCamposStyle());
      $sheet->setCellValue("{$columns[$indexColumn + 2]}9", "Comunidad y Sociedad");
      $sheet->mergeCells("{$columns[$indexColumn + 3]}9:{$columns[$indexColumn + 3]}11");
      $sheet->getStyle("{$columns[$indexColumn + 3]}9:{$columns[$indexColumn + 3]}11")->getAlignment()->setTextRotation(90);
      $sheet->getStyle("{$columns[$indexColumn + 3]}9:{$columns[$indexColumn + 3]}11")->applyFromArray(headerTablePromedioCamposStyle());
      $sheet->setCellValue("{$columns[$indexColumn + 3]}9", "Ciencia, tecnología y producción");
      $sheet->mergeCells("{$columns[$indexColumn + 4]}9:{$columns[$indexColumn + 4]}11");
      $sheet->getStyle("{$columns[$indexColumn + 4]}9:{$columns[$indexColumn + 4]}11")->getAlignment()->setTextRotation(90);
      $sheet->getStyle("{$columns[$indexColumn + 4]}9:{$columns[$indexColumn + 4]}11")->applyFromArray(headerTablePromedioCamposStyle());
      $sheet->setCellValue("{$columns[$indexColumn + 4]}9", "Vida, tierra y territorio");
      $sheet->mergeCells("{$columns[$indexColumn + 5]}9:{$columns[$indexColumn + 5]}11");
      $sheet->getStyle("{$columns[$indexColumn + 5]}9:{$columns[$indexColumn + 5]}11")->getAlignment()->setTextRotation(90);
      $sheet->getStyle("{$columns[$indexColumn + 5]}9:{$columns[$indexColumn + 5]}11")->applyFromArray(headerTablePromedioCamposStyle());
      $sheet->setCellValue("{$columns[$indexColumn + 5]}9", "Cosmos y pensamiento");
    }

    /* Agregamos los campos */
    $indexColumn = 0;
    $camposCells = 0;
    $camposCurrent = null;
    $idCamposRepeat = -1;
    foreach ($camposCount as $key => $value) {
      if ($key === 0) {
        $idCamposRepeat = $value['id_campo'];
        $camposCurrent = $value;
      }
      if ($key === (count($camposCount) - 1)) {
        $camposCells = $camposCells + $value['cells'];
        $sigla = $camposCurrent['sigla_campo'];
        $sheet->mergeCells("{$columns[$indexColumn]}9:{$columns[$indexColumn +$camposCells - 1]}9");
        $sheet->getStyle("{$columns[$indexColumn]}9:{$columns[$indexColumn +$camposCells - 1]}9")->applyFromArray(headerTableCampoStyle());
        $sheet->setCellValue("{$columns[$indexColumn]}9", $sigla);
      }
      if ($idCamposRepeat !== $value['id_campo']) {
        $sigla = $camposCurrent['sigla_campo'];
        $sheet->mergeCells("{$columns[$indexColumn]}9:{$columns[$indexColumn +$camposCells - 1]}9");
        $sheet->getStyle("{$columns[$indexColumn]}9:{$columns[$indexColumn +$camposCells - 1]}9")->applyFromArray(headerTableCampoStyle());
        $sheet->setCellValue("{$columns[$indexColumn]}9", $sigla);
        $indexColumn = $indexColumn + $camposCells;
        $idCamposRepeat = $value['id_campo'];
        $camposCurrent = $value;
        $camposCells = $value['cells'];
      } else {
        $camposCells = $camposCells + $value['cells'];
      }
    }
  }

  /* Centralizador */
  private function generateNotasInfo($sheet, $curso, $turno, $notasTrimestre, $save = false, &$estudiantesMaterias = array(), &$indexColumnLast = 0)
  {
    $columns = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN'];
    $indexRow = 12;
    $promediosCampos = array();
    $totalCamposCS = '=AVERAGE(';
    $totalCamposCTP = '=AVERAGE(';
    $totalCamposVTT = '=AVERAGE(';
    $totalCamposCP = '=AVERAGE(';
    $columsTotales = array();
    if (!$save) {
      /* Calculamos Promedios Campos */
      $promediosCampos = $this->generarPromediosCampos($sheet, $turno);
    }
    foreach ($notasTrimestre as $keyNT => $value) {
      $idArea = -1;
      $columnMateria = array();
      $indexColumn = 0;
      $promedioColumns = array();

      foreach ($value as $key => $current) {
        //$isProm = false;
        /* Notas de un estudiante */
        $column = $columns[$indexColumn];
        if ($current['id_area'] !== $idArea) {
          /* Si el Area es diferente a -1, realizamos la generación de la formula */
          if ($idArea !== -1) {
            if (count($columnMateria) !== 1) {
              /* Esta Area tiene mas de dos materias */
              if ($turno === "SM") {
                $formulaExcel = '=ROUND(SUM(';
                /* Si es Secundaria mañana calculamos por porcentajes */
                foreach ($columnMateria as $key => $currentColumn) {
                  $columnCurrent = $currentColumn['column'];
                  $idMateriaCurrent = $currentColumn['id_mat'];
                  if ($key === 0) {
                    $formulaExcel = $formulaExcel . $columnCurrent . $indexRow . '*' . getMateriaPorcentaje($curso, $idMateriaCurrent) . '/100';
                  } else {
                    $formulaExcel = $formulaExcel . ' + ' . $columnCurrent . $indexRow . '*' . getMateriaPorcentaje($curso, $idMateriaCurrent) . '/100';
                  }
                }
              } else {
                $formulaExcel = '=ROUND(AVERAGE(';
                foreach ($columnMateria as $key => $currentColumn) {
                  $columnCurrent = $currentColumn['column'];
                  if ($key === 0) {
                    $formulaExcel = $formulaExcel . $columnCurrent . $indexRow;
                  } else {
                    $formulaExcel = $formulaExcel . ',' . $columnCurrent . $indexRow;
                  }
                }
              }
              $formulaExcel = $formulaExcel . '),0)';
              $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioStyle());
              $sheet->setCellValue($column . $indexRow, $formulaExcel);
              array_push($promedioColumns, $column . $indexRow); // Agregamos columnIndex

              $indexColumn = $indexColumn + 1;
              $column = $columns[$indexColumn];
            } else {
              /* Es solo una materia por Area */
              $columnCurrent = $columnMateria[0]['column'];
              array_push($promedioColumns, $columnCurrent . $indexRow); // Agregamos columnIndex
              //$isProm = true; // No tomar en cuenta los promedios
            }
          }
          if ($key === (count($value) - 1)) { // Agregamos el ultimo a la columna de promedios
            array_push($promedioColumns, $column . $indexRow);
            //$isProm = true; // No tomar en cuenta los promedios
          }
          $idArea = $current['id_area'];
          /* Control de Columna y Materia */
          $columnMateria = array(array(
            "column" => $column,
            "id_mat" => $current['id_mat']
          ));
          /* Agregamos Nota actual */
          if ($current['total'] === '-' || $current['total'] === 0 || $current['total'] === "0") {
            /* Agregamos Vacio */
            $sheet->getStyle($column . $indexRow)->applyFromArray(notaStyle());
          } else {
            /* Agregamos Nota */
            $sheet->getStyle($column . $indexRow)->applyFromArray(notaStyle());
            $sheet->setCellValue($column . $indexRow, $current['total']);
          }
        } else {
          /* Control de Columna y Materia */
          array_push($columnMateria, array(
            "column" => $column,
            "id_mat" => $current['id_mat']
          ));
          /* Agregamos Notas */
          if ($current['total'] === '-' || $current['total'] === 0 || $current['total'] === "0") {
            /* Agregamos Vacio */
            $sheet->getStyle($column . $indexRow)->applyFromArray(notaStyle());
          } else {
            /* Agregamos Nota */
            $sheet->getStyle($column . $indexRow)->applyFromArray(notaStyle());
            $sheet->setCellValue($column . $indexRow, $current['total']);
          }
        }

        $indexColumn = $indexColumn + 1;
      }
      /* Agregamos Promedio TOTAL */
      $column = $columns[$indexColumn];
      $formulaPromedio = '=AVERAGE(';
      foreach ($promedioColumns as $key => $promedio) {
        if ($key === 0) {
          $formulaPromedio = $formulaPromedio . $promedio;
        } else {
          $formulaPromedio = $formulaPromedio . ',' . $promedio;
        }
      }
      $formulaPromedio = $formulaPromedio . ')';
      $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioStyle());
      $sheet->setCellValue($column . $indexRow, $formulaPromedio);
      if (count($estudiantesMaterias) > 0)
        $estudiantesMaterias[$keyNT]->nota = $column . $indexRow;
      /*** FIN Promedio TOTAL ***/
      //exit();

      /*** Agregar Promedios de los Campos ***/
      if (!$save) {
        // Armamos la formula para el campo CS
        $indexColumn += 2;
        $column = $columns[$indexColumn];
        $formPromCS = '=AVERAGE(';
        foreach ($promediosCampos[0] as $key => $promColumn) {
          if ($key === 0) {
            $formPromCS = $formPromCS . $promColumn . $indexRow;
          } else {
            $formPromCS = $formPromCS . ',' . $promColumn . $indexRow;
          }
        }
        $formPromCS = $formPromCS . ')';
        $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioCamposStyle());
        $sheet->setCellValue($column . $indexRow, $formPromCS);
        //array_push($totalCamposCS, $column.$indexRow);
        //$totalCamposCS = $totalCamposCS.$column.$indexRow.' <> ';
        if ($keyNT === 0) {
          $totalCamposCS = $totalCamposCS . $column . $indexRow;
          array_push($columsTotales, $column);
        } else {
          $totalCamposCS = $totalCamposCS . ',' . $column . $indexRow;
        }

        // Armamos la formula para el campo CTP
        $indexColumn++;
        $column = $columns[$indexColumn];
        $formPromCTP = '=AVERAGE(';
        foreach ($promediosCampos[1] as $key => $promColumn) {
          if ($key === 0) {
            $formPromCTP = $formPromCTP . $promColumn . $indexRow;
          } else {
            $formPromCTP = $formPromCTP . ',' . $promColumn . $indexRow;
          }
        }
        $formPromCTP = $formPromCTP . ')';
        $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioCamposStyle());
        $sheet->setCellValue($column . $indexRow, $formPromCTP);
        //
        if ($keyNT === 0) {
          $totalCamposCTP = $totalCamposCTP . $column . $indexRow;
          array_push($columsTotales, $column);
        } else {
          $totalCamposCTP = $totalCamposCTP . ',' . $column . $indexRow;
        }

        // Armamos la formula para el campo VTT
        $indexColumn++;
        $column = $columns[$indexColumn];
        $formPromVTT = '=AVERAGE(';
        foreach ($promediosCampos[2] as $key => $promColumn) {
          if ($key === 0) {
            $formPromVTT = $formPromVTT . $promColumn . $indexRow;
          } else {
            $formPromVTT = $formPromVTT . ',' . $promColumn . $indexRow;
          }
        }
        $formPromVTT = $formPromVTT . ')';
        $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioCamposStyle());
        $sheet->setCellValue($column . $indexRow, $formPromVTT);
        //
        if ($keyNT === 0) {
          $totalCamposVTT = $totalCamposVTT . $column . $indexRow;
          array_push($columsTotales, $column);
        } else {
          $totalCamposVTT = $totalCamposVTT . ',' . $column . $indexRow;
        }

        // Armamos la formula para el campo CP
        $indexColumn++;
        $column = $columns[$indexColumn];
        $formPromCP = '=AVERAGE(';
        foreach ($promediosCampos[3] as $key => $promColumn) {
          if ($key === 0) {
            $formPromCP = $formPromCP . $promColumn . $indexRow;
          } else {
            $formPromCP = $formPromCP . ',' . $promColumn . $indexRow;
          }
        }
        $formPromCP = $formPromCP . ')';
        $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioCamposStyle());
        $sheet->setCellValue($column . $indexRow, $formPromCP);
        //
        if ($keyNT === 0) {
          $totalCamposCP = $totalCamposCP . $column . $indexRow;
          array_push($columsTotales, $column);
        } else {
          $totalCamposCP = $totalCamposCP . ',' . $column . $indexRow;
        }
      }
      /*** FIN Promedios de Campos ***/

      $indexColumnLast = $indexColumn;
      $indexRow = $indexRow + 1;
    }
    $totalCamposCS = $totalCamposCS . ')';
    $totalCamposCTP = $totalCamposCTP . ')';
    $totalCamposVTT = $totalCamposVTT . ')';
    $totalCamposCP = $totalCamposCP . ')';

    foreach ($columsTotales as $key => $column) {
      $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioStyle());
      if ($key === 0)
        $sheet->setCellValue($column . $indexRow, $totalCamposCS);
      if ($key === 1)
        $sheet->setCellValue($column . $indexRow, $totalCamposCTP);
      if ($key === 2)
        $sheet->setCellValue($column . $indexRow, $totalCamposVTT);
      if ($key === 3)
        $sheet->setCellValue($column . $indexRow, $totalCamposCP);
    }
    //$sheet->setCellValue('Q48', $column.$indexRow.' <> '.$totalCamposCS);
  }

  private function generateMejoresNotas($sheet, $estudiantesMaterias, $indexColumnLast)
  {
    $columns = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU'];
    $indexRow = 12;
    $indexColumn = $indexColumnLast + 2;
    $long = count($estudiantesMaterias);

    $columnLugar = $columns[$indexColumn];
    $indexColumn++;
    $columnNombres = $columns[$indexColumn];
    $indexColumn++;
    $columnNota = $columns[$indexColumn];
    //$test = '';

    for ($i = 0; $i < $long - 1; $i++) {
      for ($j = 0; $j < $long - 1; $j++) {
        if ($sheet->getCell($estudiantesMaterias[$j]->nota)->getCalculatedValue() < $sheet->getCell($estudiantesMaterias[$j + 1]->nota)->getCalculatedValue()) {
          $aux = $estudiantesMaterias[$j];
          $estudiantesMaterias[$j] = $estudiantesMaterias[$j + 1];
          $estudiantesMaterias[$j + 1] = $aux;
        }
        // $test = $test.' '.$sheet->getCell($estudiantesMaterias[$j]->nota)->getCalculatedValue().' '.$sheet->getCell($estudiantesMaterias[$j+1]->nota)->getValue();
      }
    }

    // Primera Fila
    $sheet->mergeCells("{$columnLugar}{$indexRow}:{$columnNota}{$indexRow}");
    $sheet->getStyle("{$columnLugar}{$indexRow}:{$columnNota}{$indexRow}")->applyFromArray(basicStyle());
    $sheet->setCellValue($columnLugar . $indexRow, 'Mejores estudiantes');

    // Segunda Fila
    $indexRow++;
    $sheet->getStyle($columnLugar . $indexRow)->applyFromArray(basicStyle());
    $sheet->setCellValue($columnLugar . $indexRow, 'Lugar');
    $sheet->getStyle($columnNombres . $indexRow)->applyFromArray(basicStyle());
    $sheet->setCellValue($columnNombres . $indexRow, 'Apellidos y Nombres');
    $sheet->getStyle($columnNota . $indexRow)->applyFromArray(basicStyle());
    $sheet->setCellValue($columnNota . $indexRow, 'Nota');


    $sheet->getColumnDimension($columnLugar)->setWidth(6);
    $sheet->getColumnDimension($columnNombres)->setWidth(45);
    $sheet->getColumnDimension($columnNota)->setWidth(6);
    foreach ($estudiantesMaterias as $key => $value) {
      if ($key > 4)
        break;

      $indexRow++;
      $sheet->getStyle($columnLugar . $indexRow)->applyFromArray(basicStyle());
      $sheet->setCellValue($columnLugar . $indexRow, $key + 1);

      $sheet->getStyle($columnNombres . $indexRow)->applyFromArray(basicStyle());
      $sheet->setCellValue($columnNombres . $indexRow, $value->nro . ' - ' . $value->nombre);

      $sheet->getStyle($columnNota . $indexRow)->applyFromArray(basicStyle());
      $sheet->setCellValue($columnNota . $indexRow, '=' . $value->nota);
    }

    //$sheet->setCellValue($column.'12', 'Oh siii');
  }

  private function generarPromediosCampos($sheet, $turno)
  {
    $columns = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN'];
    $indexCampos = 9;
    $indexCampo = 0;
    $indexCampoUltimos = 1;
    $rangeCS = array();
    $rangeCTP = array();
    $rangeVTT = array();
    $rangeCP = array();
    $promedioCS = array();
    $promedioCTP = array();
    $promedioVTT = array();
    $promedioCP = array();
    $promediosCampos = array();

    foreach ($columns as $key => $column) {
      $value = $sheet->getCell($column . $indexCampos)->getValue();
      if ($value === 'TOTAL') break;

      if ($value)
        $indexCampo++;
      if ($indexCampo === 1) {
        array_push($rangeCS, $column);
      }
      if ($indexCampo === 2) {
        array_push($rangeCTP, $column);
      }
      if ($indexCampo === 3) {
        if ($turno === "PM" || $turno === "PT") {
          if ($indexCampoUltimos === 2) {
            array_push($rangeCP, $column);
          }
          if ($indexCampoUltimos === 1) {
            array_push($rangeVTT, $column);
            $indexCampoUltimos++;
          }
        } else {
          array_push($rangeVTT, $column);
        }
      }
      if ($indexCampo === 4) {
        array_push($rangeCP, $column);
      }
    }

    $promedioCS = $this->promedioRange($sheet, $rangeCS);
    $promedioCTP = $this->promedioRange($sheet, $rangeCTP);
    $promedioVTT = $this->promedioRange($sheet, $rangeVTT);
    $promedioCP = $this->promedioRange($sheet, $rangeCP);

    array_push($promediosCampos, $promedioCS);
    array_push($promediosCampos, $promedioCTP);
    array_push($promediosCampos, $promedioVTT);
    array_push($promediosCampos, $promedioCP);

    return $promediosCampos;
  }

  private function promedioRange($sheet, $range)
  {
    $indexAreas = 10;
    $promedio = array();
    foreach ($range as $key => $column) {
      $value2 = '';
      if ($key + 1 <= (count($range) - 1)) {
        if ($range[$key + 1]) {
          $value2 = $sheet->getCell($range[$key + 1] . $indexAreas)->getValue();
        }
      }

      if ($value2 || $key === (count($range) - 1))
        array_push($promedio, $column);
    }
    return $promedio;
  }

  public function generarCentralizador($curso = "1A", $turno = "PM", $trimestre = "primer", $gestion = 2021)
  {
    $this->generateContent($curso, $turno, $trimestre, $gestion, $save = false);
  }

  public function generarCentralizadorSave($curso = "1A", $turno = "PM", $trimestre = "primer", $gestion = 2021)
  {
    $this->generateContent($curso, $turno, $trimestre, $gestion, $save = true);

    //$result = array("path" => '/home/'.$_ENV['US_SERVER'].'/coder/upload/Centralizador.xlsx');
    $result = array("path" => $_ENV['PATH_UPLOAD_SERVER'] . '/Centralizador.xlsx');
    $status = true;
    $message = "Centralizador Guardado";
    return $this->response->send($result, $status, $message, []);
  }

  public function generarCentralizadorApplication($curso = "1A", $turno = "PM", $trimestre = "primer", $gestion = 2021)
  {
    $trimestreCurrent = $this->getTrimestres($trimestre);
    $cursoEstudiantes = $this->getEstudiantes($curso, $turno, $gestion);
    $notasEstudiantes = $this->getCentralizadorData($curso, $turno, $trimestre, $gestion);

    $cursoMaterias = $this->boletin->getCamposAreasMaterias($curso, $turno);
    $notasTrimestre = $this->getNotasEstudiantes($curso, $turno, $trimestreCurrent, $cursoMaterias, $cursoEstudiantes, $gestion);

    $countJo = 0;
    $inc = 0;
    foreach ($notasTrimestre as $key1 => $v_not_est) {
      $inc = 0;
      foreach ($v_not_est as $key2 => $value) {
        if ($value['total'] === '-' || $value['total'] === 0 || $value['total'] === "0" || $value['total'] <= 51) {
          $inc++;
        }
      }
      $countJo = count($v_not_est);
      if ($key1 == 15)
        break;
      if (count($v_not_est) == $inc) {
        array_splice($notasTrimestre, $key1, 1);
        array_splice($cursoEstudiantes, $key1, 1);
      }
    }
    //$notasTrimestre = $this->getNotasEstudiantes($curso, $turno, $trimestreCurrent, $cursoMaterias, $cursoEstudiantes, $gestion);

    $result = array(
      "notas" => $notasEstudiantes,
      "estudiantes" => $cursoEstudiantes,
      "count" => $countJo,
      "inc" => $inc
    );
    $status = true;
    $message = "Notas del curso " . $curso . " - " . $turno;
    return $this->response->send($result, $status, $message, []);
  }

  /* Acumulador functions */
  private function generateHeaderInfoAcumulador($sheet, $curso, $turno, $gestion = 2021)
  {
    $cursoCurrent = checkCurso($curso);
    $nivelCurrent = checkGrado($turno) . ' ' . checkTurno($turno);
    $colegioCurrent = checkColegio($turno);
    /* Agregamos Logo del colegio */
    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Colegio Don Bosco');
    $drawing->setDescription('Documento Oficial de notas, Colegio Don Bosco');
    $drawing->setPath('utils/images/logo-min-min.png');
    $drawing->setCoordinates('A1');
    $drawing->setWidthAndHeight(70, 90);
    $drawing->setOffsetX(30);
    $drawing->getShadow()->setVisible(true);
    $drawing->getShadow()->setDirection(45);
    $drawing->setWorksheet($sheet);
    $sheet->mergeCells('C3:R3');
    $sheet->setCellValue('C3', 'ACUMULADOR ANUAL');
    $sheet->getStyle('C3:R3')->applyFromArray(headerTitleStyle());
    /* Agregamos Información */
    $sheet->setCellValue('B6', 'GRADO ESC.:');
    $sheet->getStyle('B6')->applyFromArray(headerInfoStyle());
    $sheet->getStyle('C6')->applyFromArray(headerInfoDetailStyle());
    $sheet->setCellValue('C6', $cursoCurrent);

    $sheet->setCellValue('B7', 'NIVEL:');
    $sheet->getStyle('B7')->applyFromArray(headerInfoStyle());
    $sheet->getStyle('C7')->applyFromArray(headerInfoDetailStyle());
    $sheet->setCellValue('C7', $nivelCurrent);

    $sheet->mergeCells('F6:H6');
    $sheet->setCellValue('F6', 'GESTION:');
    $sheet->getStyle('F6:H6')->applyFromArray(headerInfoStyle());
    $sheet->mergeCells('I6:Q6');
    $sheet->getStyle('I6:Q6')->applyFromArray(headerInfoDetailStyle());
    $sheet->setCellValue('I6', $gestion);

    $sheet->mergeCells('F7:H7');
    $sheet->setCellValue('F7', 'UNID. EDU.:');
    $sheet->getStyle('F7:H7')->applyFromArray(headerInfoStyle());
    $sheet->mergeCells('I7:Q7');
    $sheet->getStyle('I7:Q7')->applyFromArray(headerInfoDetailStyle());
    $sheet->setCellValue('I7', $colegioCurrent);
  }

  private function generateHeaderTableAcumulador($sheet, $curso, $turno, $cursoMaterias, $cursoEstudiantes)
  {
    $columns = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN'];
    $indexRow = 12;
    $cursoMateriasFilter = $this->formattedMaterias($curso, $turno, $cursoMaterias);
    $materias = $this->formattedMateriasErrores($curso, $turno, $cursoMateriasFilter);

    /* Generamos el Nro. y Nombres */
    $sheet->mergeCells('A9:A11');
    $sheet->setCellValue('A9', 'Nº');
    $sheet->getStyle('A9:A11')->applyFromArray(headerTableStyle());
    $sheet->mergeCells('B9:C11');
    $sheet->setCellValue('B9', 'NOMBRES');
    $sheet->getStyle('B9:C11')->applyFromArray(headerTableStyle());

    /* Generamos la Lista de Estudiantes */
    foreach ($cursoEstudiantes as $key => $estudiante) {
      $sheet->setCellValue("A{$indexRow}", $key + 1);
      $sheet->getStyle("A{$indexRow}")->applyFromArray(headerTableNameStyle());
      $nombreCompleto = trim($estudiante->appaterno . ' ' . $estudiante->apmaterno . ' ' . $estudiante->nombre);
      $sheet->mergeCells("B{$indexRow}:C{$indexRow}");
      $sheet->getStyle("B{$indexRow}:C{$indexRow}")->applyFromArray(notaStyle());
      $sheet->setCellValue("B{$indexRow}", $nombreCompleto);
      $indexRow = $indexRow + 1;
    }

    /* Generamos las areas */
    $camposCount = array();
    $areasRepeat = array();
    $areasCurrent = null;
    $areasCount = 0;
    $indexColumn = 0;
    $columnArea = 0;
    $rowArea = 12;

    $areasControl = 0;

    foreach ($materias as $key => $value) {
      if ($key == 0) {
        array_push($areasRepeat, $value->id_area);
        $areasCurrent = $value;
      }
      if (!is_numeric(array_search($value->id_area, $areasRepeat))) {
        if ($areasControl > 1) {
          $areasCount = $areasCount + $areasControl;
        }
        array_push($areasRepeat, $value->id_area); // Agregamos a Area Repetido
        $areasControl = 1; // reiniciamos el area control

        $sheet->mergeCells("{$columns[$indexColumn]}9:{$columns[$indexColumn]}10");
        $sheet->getStyle("{$columns[$indexColumn]}9:{$columns[$indexColumn]}10")->applyFromArray(headerTablePromedioStyle());
        $sheet->setCellValue("{$columns[$indexColumn]}9", $areasCurrent->sigla_area);

        $sheet->getStyle("{$columns[$indexColumn]}11")->getAlignment()->setTextRotation(90);
        $sheet->getStyle("{$columns[$indexColumn]}11")->applyFromArray(headerTablePromedioStyle());
        $sheet->setCellValue("{$columns[$indexColumn]}11", $areasCurrent->nombre_area);

        /* Agrega formulas a las celdas */
        foreach ($cursoEstudiantes as $indexEst => $estudiante) {
          $indexEstudiante = $rowArea + $indexEst;
          $rowColumn = $columns[$indexColumn + $areasCount] . "" . $indexEstudiante;
          $formula = "=AVERAGE('Centralizador $curso $turno - 1T'!$rowColumn,'Centralizador $curso $turno - 2T'!$rowColumn,'Centralizador $curso $turno - 3T'!$rowColumn)";
          $sheet->getStyle("{$columns[$indexColumn]}{$indexEstudiante}")->applyFromArray(notaStyle());
          $sheet->setCellValue("{$columns[$indexColumn]}{$indexEstudiante}", $formula);
        }

        /* Reseteamos contador */
        $indexColumn = $indexColumn + 1;
        $areasCurrent = $value;

        if ($key === (count($materias) - 1)) { // Agregamos el ultimo a la columna de promedios          
          /* Agregamos la ultima Area */
          $sheet->mergeCells("{$columns[$indexColumn]}9:{$columns[$indexColumn]}10");
          $sheet->getStyle("{$columns[$indexColumn]}9:{$columns[$indexColumn]}10")->applyFromArray(headerTablePromedioStyle());
          $sheet->setCellValue("{$columns[$indexColumn]}9", $areasCurrent->sigla_area);

          $sheet->getStyle("{$columns[$indexColumn]}11")->getAlignment()->setTextRotation(90);
          $sheet->getStyle("{$columns[$indexColumn]}11")->applyFromArray(headerTablePromedioStyle());
          $sheet->setCellValue("{$columns[$indexColumn]}11", $areasCurrent->nombre_area);

          /* Agrega formulas a las celdas */
          foreach ($cursoEstudiantes as $indexEst => $estudiante) {
            $indexEstudiante = $rowArea + $indexEst;
            $rowColumn = $columns[$indexColumn + $areasCount] . "" . $indexEstudiante;
            $formula = "=AVERAGE('Centralizador $curso $turno - 1T'!$rowColumn,'Centralizador $curso $turno - 2T'!$rowColumn,'Centralizador $curso $turno - 3T'!$rowColumn)";
            $sheet->getStyle("{$columns[$indexColumn]}{$indexEstudiante}")->applyFromArray(notaStyle());
            $sheet->setCellValue("{$columns[$indexColumn]}{$indexEstudiante}", $formula);
          }

          $indexColumn = $indexColumn + 1;
        }
      } else {
        $areasControl = $areasControl + 1;
      }
    }

    // exit();


    foreach ($cursoEstudiantes as $indexEst => $estudiante) {
      $indexEstudiante = $rowArea + $indexEst;
      /* Generamos la formula de promedio */
      $formulaPromedio = '=ROUND(AVERAGE(';
      for ($iterator = 0; $iterator < $indexColumn; $iterator++) {
        $colRow = $columns[$iterator] . $indexEstudiante;
        if ($iterator === 0) {
          $formulaPromedio = $formulaPromedio . $colRow;
        } else {
          $formulaPromedio = $formulaPromedio . ',' . $colRow;
        }
      }
      $formulaPromedio = $formulaPromedio . '),0)';
      $sheet->getStyle("{$columns[$indexColumn]}{$indexEstudiante}")->applyFromArray(notaPromedioStyle());
      $sheet->setCellValue("{$columns[$indexColumn]}{$indexEstudiante}", $formulaPromedio);
    }

    $sheet->mergeCells("{$columns[$indexColumn]}9:{$columns[$indexColumn]}11");
    $sheet->getStyle("{$columns[$indexColumn]}9:{$columns[$indexColumn]}11")->getAlignment()->setTextRotation(90);
    $sheet->getStyle("{$columns[$indexColumn]}9:{$columns[$indexColumn]}11")->applyFromArray(headerTablePromedioStyle());
    $sheet->setCellValue("{$columns[$indexColumn]}9", "TOTAL");
  }

  private function generateNotasAcumulador($sheet, $curso, $turno, $notasTrimestre, &$estudiantesMaterias = array(), &$indexColumnLast = 0)
  {
    $columns = ['D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN'];
    $indexRow = 12;
    $promediosCampos = array();
    $totalCamposCS = '=AVERAGE(';
    $totalCamposCTP = '=AVERAGE(';
    $totalCamposVTT = '=AVERAGE(';
    $totalCamposCP = '=AVERAGE(';
    $columsTotales = array();
    /* Calculamos Promedios Campos */
    $promediosCampos = $this->generarPromediosCampos($sheet, $turno);
    foreach ($notasTrimestre as $keyNT => $value) {
      $idArea = -1;
      $columnMateria = array();
      $indexColumn = 0;
      $promedioColumns = array();

      foreach ($value as $key => $current) {
        /* Notas de un estudiante */
        $column = $columns[$indexColumn];
        if ($current['id_area'] !== $idArea) {
          /* Si el Area es diferente a -1, realizamos la generación de la formula */
          if ($idArea !== -1) {
            if (count($columnMateria) !== 1) {
              /* Esta Area tiene mas de dos materias */
              if ($turno === "SM") {
                $formulaExcel = '=ROUND(SUM(';
                /* Si es Secundaria mañana calculamos por porcentajes */
                foreach ($columnMateria as $key => $currentColumn) {
                  $columnCurrent = $currentColumn['column'];
                  $idMateriaCurrent = $currentColumn['id_mat'];
                  if ($key === 0) {
                    $formulaExcel = $formulaExcel . $columnCurrent . $indexRow . '*' . getMateriaPorcentaje($curso, $idMateriaCurrent) . '/100';
                  } else {
                    $formulaExcel = $formulaExcel . ' + ' . $columnCurrent . $indexRow . '*' . getMateriaPorcentaje($curso, $idMateriaCurrent) . '/100';
                  }
                }
              } else {
                $formulaExcel = '=ROUND(AVERAGE(';
                foreach ($columnMateria as $key => $currentColumn) {
                  $columnCurrent = $currentColumn['column'];
                  if ($key === 0) {
                    $formulaExcel = $formulaExcel . $columnCurrent . $indexRow;
                  } else {
                    $formulaExcel = $formulaExcel . ',' . $columnCurrent . $indexRow;
                  }
                }
              }
              $formulaExcel = $formulaExcel . '),0)';
              $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioStyle());
              $sheet->setCellValue($column . $indexRow, $formulaExcel); // PROMEDIO POR ÁREAS
              array_push($promedioColumns, $column . $indexRow); // Agregamos columnIndex

              $indexColumn = $indexColumn + 1;
              $column = $columns[$indexColumn];
            } else {
              /* Es solo una materia por Area */
              $columnCurrent = $columnMateria[0]['column'];
              array_push($promedioColumns, $columnCurrent . $indexRow); // Agregamos columnIndex
            }
          }
          if ($key === (count($value) - 1)) { // Agregamos el ultimo a la columna de promedios
            array_push($promedioColumns, $column . $indexRow);
          }
          $idArea = $current['id_area'];
          /* Control de Columna y Materia */
          $columnMateria = array(array(
            "column" => $column,
            "id_mat" => $current['id_mat']
          ));
          /* Agregamos Nota actual */
          $rowColumn = $column . $indexRow;
          $formula = "=AVERAGE('Centralizador $curso $turno - 1T'!$rowColumn,'Centralizador $curso $turno - 2T'!$rowColumn,'Centralizador $curso $turno - 3T'!$rowColumn)";
          $sheet->getStyle($rowColumn)->applyFromArray(notaStyle());
          $sheet->setCellValue($rowColumn, $formula); // Nota Trimestral Normal $current['total']
        } else {
          /* Control de Columna y Materia */
          array_push($columnMateria, array(
            "column" => $column,
            "id_mat" => $current['id_mat']
          ));
          /* Agregamos Notas De Áreas */
          $rowColumn = $column . $indexRow;
          $formula = "=AVERAGE('Centralizador $curso $turno - 1T'!$rowColumn,'Centralizador $curso $turno - 2T'!$rowColumn,'Centralizador $curso $turno - 3T'!$rowColumn)";
          $sheet->getStyle($column . $indexRow)->applyFromArray(notaStyle());
          $sheet->setCellValue($column . $indexRow, $formula); // Nota Trimestral Área $current['total']
        }

        $indexColumn = $indexColumn + 1;
      }
      /* Agregamos Promedio TOTAL */
      $column = $columns[$indexColumn];
      $formulaPromedio = '=AVERAGE(';
      foreach ($promedioColumns as $key => $promedio) {
        if ($key === 0) {
          $formulaPromedio = $formulaPromedio . $promedio;
        } else {
          $formulaPromedio = $formulaPromedio . ',' . $promedio;
        }
      }
      $formulaPromedio = $formulaPromedio . ')';
      $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioStyle());
      $sheet->setCellValue($column . $indexRow, $formulaPromedio); // PROMEDIO TOTAL ANUAL
      if (count($estudiantesMaterias) > 0)
        $estudiantesMaterias[$keyNT]->nota = $column . $indexRow;
      /*** FIN Promedio TOTAL ***/


      /*** Agregar Promedios de los Campos ***/
      // Armamos la formula para el campo CS
      $indexColumn += 2;
      $column = $columns[$indexColumn];
      $formPromCS = '=AVERAGE(';
      foreach ($promediosCampos[0] as $key => $promColumn) {
        if ($key === 0) {
          $formPromCS = $formPromCS . $promColumn . $indexRow;
        } else {
          $formPromCS = $formPromCS . ',' . $promColumn . $indexRow;
        }
      }
      $formPromCS = $formPromCS . ')';
      $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioCamposStyle());
      $sheet->setCellValue($column . $indexRow, $formPromCS);
      if ($keyNT === 0) {
        $totalCamposCS = $totalCamposCS . $column . $indexRow;
        array_push($columsTotales, $column);
      } else {
        $totalCamposCS = $totalCamposCS . ',' . $column . $indexRow;
      }

      // Armamos la formula para el campo CTP
      $indexColumn++;
      $column = $columns[$indexColumn];
      $formPromCTP = '=AVERAGE(';
      foreach ($promediosCampos[1] as $key => $promColumn) {
        if ($key === 0) {
          $formPromCTP = $formPromCTP . $promColumn . $indexRow;
        } else {
          $formPromCTP = $formPromCTP . ',' . $promColumn . $indexRow;
        }
      }
      $formPromCTP = $formPromCTP . ')';
      $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioCamposStyle());
      $sheet->setCellValue($column . $indexRow, $formPromCTP);
      //
      if ($keyNT === 0) {
        $totalCamposCTP = $totalCamposCTP . $column . $indexRow;
        array_push($columsTotales, $column);
      } else {
        $totalCamposCTP = $totalCamposCTP . ',' . $column . $indexRow;
      }

      // Armamos la formula para el campo VTT
      $indexColumn++;
      $column = $columns[$indexColumn];
      $formPromVTT = '=AVERAGE(';
      foreach ($promediosCampos[2] as $key => $promColumn) {
        if ($key === 0) {
          $formPromVTT = $formPromVTT . $promColumn . $indexRow;
        } else {
          $formPromVTT = $formPromVTT . ',' . $promColumn . $indexRow;
        }
      }
      $formPromVTT = $formPromVTT . ')';
      $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioCamposStyle());
      $sheet->setCellValue($column . $indexRow, $formPromVTT);
      //
      if ($keyNT === 0) {
        $totalCamposVTT = $totalCamposVTT . $column . $indexRow;
        array_push($columsTotales, $column);
      } else {
        $totalCamposVTT = $totalCamposVTT . ',' . $column . $indexRow;
      }

      // Armamos la formula para el campo CP
      $indexColumn++;
      $column = $columns[$indexColumn];
      $formPromCP = '=AVERAGE(';
      foreach ($promediosCampos[3] as $key => $promColumn) {
        if ($key === 0) {
          $formPromCP = $formPromCP . $promColumn . $indexRow;
        } else {
          $formPromCP = $formPromCP . ',' . $promColumn . $indexRow;
        }
      }
      $formPromCP = $formPromCP . ')';
      $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioCamposStyle());
      $sheet->setCellValue($column . $indexRow, $formPromCP);
      //
      if ($keyNT === 0) {
        $totalCamposCP = $totalCamposCP . $column . $indexRow;
        array_push($columsTotales, $column);
      } else {
        $totalCamposCP = $totalCamposCP . ',' . $column . $indexRow;
      }
      /*** FIN Promedios de Campos ***/

      $indexColumnLast = $indexColumn;
      $indexRow = $indexRow + 1;
    }
    $totalCamposCS = $totalCamposCS . ')';
    $totalCamposCTP = $totalCamposCTP . ')';
    $totalCamposVTT = $totalCamposVTT . ')';
    $totalCamposCP = $totalCamposCP . ')';

    foreach ($columsTotales as $key => $column) {
      $sheet->getStyle($column . $indexRow)->applyFromArray(notaPromedioStyle());
      if ($key === 0)
        $sheet->setCellValue($column . $indexRow, $totalCamposCS);
      if ($key === 1)
        $sheet->setCellValue($column . $indexRow, $totalCamposCTP);
      if ($key === 2)
        $sheet->setCellValue($column . $indexRow, $totalCamposVTT);
      if ($key === 3)
        $sheet->setCellValue($column . $indexRow, $totalCamposCP);
    }
    //$sheet->setCellValue('Q48', $column.$indexRow.' <> '.$totalCamposCS);
  }

  public function generateAcumulador($curso = "1A", $turno = "PM", $gestion = 2021, $save = false)
  {
    $spreadsheet = new Spreadsheet();
    /* Obtenemos los cursoMaterias */
    $cursoEstudiantes = $this->getEstudiantes($curso, $turno, $gestion);
    $cursoMaterias = $this->boletin->getCamposAreasMaterias($curso, $turno);

    /******** Generamos el Centralizador del Primer Trimestre ********/
    $sheet = $spreadsheet->getActiveSheet();
    $titleTab = 'Centralizador ' . $curso . ' ' . $turno . ' - 1T';
    $sheet->setTitle($titleTab);
    $estudiantesMaterias = array();
    $indexColumnLast = 0;

    $notasPrimerTrimestre = $this->getNotasEstudiantes($curso, $turno, $this->PRIMER_TRIMESTRE, $cursoMaterias, $cursoEstudiantes, $gestion);
    /* Agregamos tamaño a las columnas */
    $this->setDimensionColumns($sheet);
    /* Agregamos formatting cells */
    $this->setFormattingCells($sheet, $curso, $turno, $cursoMaterias, $cursoEstudiantes);
    /* Generamos la cabecera principal */
    $this->generateHeaderInfo($sheet, $curso, $turno, $this->PRIMER_TRIMESTRE_LITERAL, $gestion);
    /* Generamos la cabecera de la tabla */
    $this->generateHeaderTable($sheet, $curso, $turno, $cursoMaterias, $cursoEstudiantes, false, $estudiantesMaterias);
    /* Generamos las notas del estudiante */
    $this->generateNotasInfo($sheet, $curso, $turno, $notasPrimerTrimestre, false, $estudiantesMaterias, $indexColumnLast);
    /* Generamos a los 5 mejores estudiantes */
    $this->generateMejoresNotas($sheet, $estudiantesMaterias, $indexColumnLast);

    /******* Generamos el Centralizador del Segundo Trimestre *******/
    $estudiantesMaterias = array();
    $spreadsheet->createSheet();
    $spreadsheet->setActiveSheetIndex(1);

    $sheet2 = $spreadsheet->getActiveSheet();
    $titleTab = 'Centralizador ' . $curso . ' ' . $turno . ' - 2T';
    $sheet2->setTitle($titleTab);
    $notasSegundoTrimestre = $this->getNotasEstudiantes($curso, $turno, $this->SEGUNDO_TRIMESTRE, $cursoMaterias, $cursoEstudiantes, $gestion);
    /* Agregamos tamaño a las columnas */
    $this->setDimensionColumns($sheet2);
    /* Agregamos formatting cells */
    $this->setFormattingCells($sheet2, $curso, $turno, $cursoMaterias, $cursoEstudiantes);
    /* Generamos la cabecera principal */
    $this->generateHeaderInfo($sheet2, $curso, $turno, $this->SEGUNDO_TRIMESTRE_LITERAL, $gestion);
    /* Generamos la cabecera de la tabla */
    $this->generateHeaderTable($sheet2, $curso, $turno, $cursoMaterias, $cursoEstudiantes, false, $estudiantesMaterias);
    /* Generamos las notas del estudiante */
    $this->generateNotasInfo($sheet2, $curso, $turno, $notasSegundoTrimestre, false, $estudiantesMaterias, $indexColumnLast);
    /* Generamos a los 5 mejores estudiantes */
    $this->generateMejoresNotas($sheet2, $estudiantesMaterias, $indexColumnLast);

    /******* Generamos el Centralizador del Tercer Trimestre *******/
    $estudiantesMaterias = array();
    $spreadsheet->createSheet();
    $spreadsheet->setActiveSheetIndex(2);

    $sheet3 = $spreadsheet->getActiveSheet();
    $titleTab = 'Centralizador ' . $curso . ' ' . $turno . ' - 3T';
    $sheet3->setTitle($titleTab);
    $notasTercerTrimestre = $this->getNotasEstudiantes($curso, $turno, $this->TERCER_TRIMESTRE, $cursoMaterias, $cursoEstudiantes, $gestion);
    /* Agregamos tamaño a las columnas */
    $this->setDimensionColumns($sheet3);
    /* Agregamos formatting cells */
    $this->setFormattingCells($sheet3, $curso, $turno, $cursoMaterias, $cursoEstudiantes);
    /* Generamos la cabecera principal */
    $this->generateHeaderInfo($sheet3, $curso, $turno, $this->TERCER_TRIMESTRE_LITERAL, $gestion);
    /* Generamos la cabecera de la tabla */
    $this->generateHeaderTable($sheet3, $curso, $turno, $cursoMaterias, $cursoEstudiantes, false, $estudiantesMaterias);
    /* Generamos las notas del estudiante */
    $this->generateNotasInfo($sheet3, $curso, $turno, $notasTercerTrimestre, false, $estudiantesMaterias, $indexColumnLast);
    /* Generamos a los 5 mejores estudiantes */
    $this->generateMejoresNotas($sheet3, $estudiantesMaterias, $indexColumnLast);

    /******* Generamos el Acumulador *******/
    $estudiantesMaterias = array();
    $spreadsheet->createSheet();
    $spreadsheet->setActiveSheetIndex(3);

    $sheet4 = $spreadsheet->getActiveSheet();
    $titleTab = 'Acumulador ' . $curso . ' ' . $turno;
    $sheet4->setTitle($titleTab);

    /* Agregamos tamaño a las columnas */
    $this->setDimensionColumns($sheet4);
    $this->setFormattingCells($sheet4, $curso, $turno, $cursoMaterias, $cursoEstudiantes);
    $this->generateHeaderInfoAcumulador($sheet4, $curso, $turno, $gestion);
    /* Generamos la cabecera de la tabla */
    $this->generateHeaderTable($sheet4, $curso, $turno, $cursoMaterias, $cursoEstudiantes, false, $estudiantesMaterias);
    $this->generateNotasAcumulador($sheet4, $curso, $turno, $notasPrimerTrimestre, $estudiantesMaterias, $indexColumnLast);
    $this->generateMejoresNotas($sheet4, $estudiantesMaterias, $indexColumnLast);
    // $this->generateHeaderTableAcumulador($sheet4, $curso, $turno, $cursoMaterias, $cursoEstudiantes);


    /******* Activamos el primer sheet *******/
    $spreadsheet->setActiveSheetIndex(0);
    $writer = new Xlsx($spreadsheet);

    if ($save) {
      $writer->save($_ENV['PATH_UPLOAD_SERVER'] . '/Acumulador.xlsx');
      $result = array("path" => $_ENV['PATH_UPLOAD_SERVER'] . '/Acumulador.xlsx');
      $status = true;
      $message = "Acumulador Guardado";
      return $this->response->send($result, $status, $message, []);
    } else {
      /* BUILD EXCEL */
      $writer->save('php://output');
    }
  }
}
