<?php

function getPM($curso)
{
  $cursosMaterias = array(
    "1A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "1B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "2A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "2B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "3A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "3B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "4A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25, 27, 29],
    "4B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25, 27, 29],
    "5A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25, 27, 29, 37],
    "5B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25, 27, 29, 37],
    "6A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25, 27, 29, 37],
    "6B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25, 27, 29, 37],
  );
  if (isset($cursosMaterias[$curso])) {
    return $cursosMaterias[$curso];
  }
  return [];
}

function getPT($curso)
{
  $cursosMaterias = array(
    "1A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "1B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "2A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "2B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "3A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "3B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "4A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "4B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "5A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "5B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "6A" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
    "6B" => [1, 3, 5, 9, 10, 11, 12, 13, 20, 25],
  );
  if (isset($cursosMaterias[$curso])) {
    return $cursosMaterias[$curso];
  }
  return [];
}

function getSM($curso)
{
  $cursosMaterias = array(
    "1A" => [1, 4, 5, 9, 10, 11, 12, 14, 15, 16, 24, 25, 21, 27, 29, 38, 39],
    "1B" => [1, 4, 5, 9, 10, 11, 12, 14, 15, 16, 24, 25, 21, 27, 29, 38, 39],
    "1C" => [1, 4, 5, 9, 10, 11, 12, 14, 15, 16, 24, 25, 21, 27, 29, 38, 39],
    "2A" => [1, 4, 5, 9, 10, 11, 12, 14, 15, 16, 24, 25, 21, 27, 29, 38, 39],
    "2B" => [1, 4, 5, 9, 10, 11, 12, 14, 15, 16, 24, 25, 21, 27, 29, 38, 39],
    "2C" => [1, 4, 5, 9, 10, 11, 12, 14, 15, 16, 24, 25, 21, 27, 29, 38, 39],
    "3A" => [1, 4, 5, 9, 10, 11, 12, 21, 22, 23, 24, 25, 19, 27, 29, 34],
    "3B" => [1, 4, 5, 9, 10, 11, 12, 21, 22, 23, 24, 25, 19, 27, 29, 34],
    "4A" => [1, 4, 5, 9, 10, 11, 12, 21, 22, 23, 24, 25, 19, 27, 29, 34],
    "4B" => [1, 4, 5, 9, 10, 11, 12, 21, 22, 23, 24, 25, 19, 27, 29, 34],
    "5A" => [1, 4, 5, 9, 10, 11, 12, 19, 21, 22, 23, 24, 25, 27, 29, 31, 32, 34], // 18,
    "5B" => [1, 4, 5, 9, 10, 11, 12, 19, 21, 22, 23, 24, 25, 26, 27, 32, 33, 34], // 18,
    "5C" => [1, 4, 6, 7, 8, 9, 10, 11, 12, 19, 21, 22, 23, 24, 25, 27, 34, 44], // 18, // 44 reemplaza a 35
    "6A" => [1, 4, 5, 9, 10, 11, 12, 18, 19, 21, 22, 23, 24, 25, 27, 29, 31, 32],
    "6B" => [1, 4, 5, 9, 10, 11, 12, 18, 19, 21, 22, 23, 24, 25, 26, 27, 32, 33],
    "6C" => [1, 4, 6, 7, 8, 9, 10, 11, 12, 18, 19, 21, 22, 23, 24, 25, 27, 44], // 44 reemplaza a 35
  );
  if (isset($cursosMaterias[$curso])) {
    return $cursosMaterias[$curso];
  }
  return [];
}

function getST($curso)
{
  $cursosMaterias = array(
    "1A" => [1, 4, 5, 9, 10, 11, 12, 14, 24, 25, 21, 41, 40, 42], //
    "1B" => [1, 4, 5, 9, 10, 11, 12, 14, 24, 25, 21, 42, 40, 41], //
    "2A" => [1, 4, 5, 9, 10, 11, 12, 14, 24, 25, 21, 42, 40, 41], //
    "2B" => [1, 4, 5, 9, 10, 11, 12, 14, 24, 25, 21, 42, 40, 41], //
    "2C" => [1, 5, 21, 41, 42, 9, 10, 4, 11, 14, 24, 25, 40, 12], //
    "3A" => [1, 4, 5, 9, 10, 11, 12, 21, 22, 23, 24, 25, 18, 40, 19], //
    "3B" => [1, 4, 5, 9, 10, 11, 12, 21, 22, 23, 24, 25, 18, 40, 19], //
    "4A" => [1, 4, 5, 9, 10, 11, 12, 21, 22, 23, 24, 25, 18, 35, 19], //
    "4B" => [1, 4, 5, 9, 10, 11, 12, 21, 22, 23, 24, 25, 18, 35, 19], //
    "5A" => [1, 4, 5, 9, 10, 11, 12, 18, 21, 22, 23, 24, 25, 35, 19], //
    "5B" => [1, 4, 5, 9, 10, 11, 12, 19, 21, 22, 23, 24, 25, 35, 18], //
    "6A" => [1, 4, 6, 9, 10, 11, 12, 18, 19, 21, 22, 23, 24, 35, 25], //
    "6B" => [1, 4, 9, 10, 11, 12, 19, 21, 22, 23, 24, 25, 6, 35, 18], //
  );
  if (isset($cursosMaterias[$curso])) {
    return $cursosMaterias[$curso];
  }
  return [];
}

function getMaterias($curso, $turno)
{
  if ($turno == 'PM') {
    return getPM($curso); // Primaria Mañana
  }
  if ($turno == 'PT') {
    return getPT($curso); // Primaria Tarde
  }
  if ($turno == 'SM') {
    return getSM($curso); // Secundaria Mañana
  }
  if ($turno == 'ST') {
    return getST($curso); // Secundaria Tarde
  }
  return [];
}

function getMateriasQuery($curso, $turno, $query)
{
  $materias = getMaterias($curso, $turno);
  $materiasFilter = [];
  foreach ($materias as $key => $value) {
    array_push($materiasFilter, $query . $value);
  }
  $materiasQuery = implode(" OR ", $materiasFilter);
  return $materiasQuery;
}

function getEspecialidad($curso, $turno)
{
  /* [19,34] 19: Electronica, 34: Diseño Grafico*/
  $especialidadA = ["3ASM", "3BSM", "4ASM", "4BSM", "5ASM", "5BSM", "5CSM"];
  /* [18,19] 18: Sitemas informaticos, 19: Electronica */
  $especialidadB = ["6ASM", "6BSM", "6CSM", "3AST", "3BST", "4AST", "4BST", "5AST", "5BST", "5CST", "6AST", "6BST", "6CST"];
  $cursoTurno = $curso . $turno;
  if (is_numeric(array_search($cursoTurno, $especialidadA))) {
    return [19, 34];
  }
  if (is_numeric(array_search($cursoTurno, $especialidadB))) {
    return [18, 19];
  }
  return [];
}

function getControlMaterias($curso, $turno)
{
  /*
      SE CONTROLA LAS MATERIAS ESPECIALES QUE TIENEN LOS CURSOS
    */
  $cursoTurno = $curso . $turno;
  /* [19,34] 19: Electronica, 34: Diseño Grafico*/
  $especialidadA = ["3ASM", "3BSM", "4ASM", "4BSM", "5ASM", "5BSM", "5CSM"];
  /* [18,19] 18: Sitemas informaticos, 19: Electronica */
  $especialidadB = ["6ASM", "6BSM", "6CSM", "3AST", "3BST", "4AST", "4BST", "5AST", "5BST", "5CST", "6AST", "6BST", "6CST"];

  if (is_numeric(array_search($cursoTurno, $especialidadA))) {
    return [19, 34];
  }
  if (is_numeric(array_search($cursoTurno, $especialidadB))) {
    return [18, 19];
  }
  return false;
}

/* Metodo para Secundaria Mañana BOLETIN */
function getMateriasPorcentajes($notas, $curso)
{
  $notasPorcentajes = json_decode(json_encode($notas));
  /* Inicial A */
  if (is_numeric(array_search($curso, array("1A", "1B", "1C", "2A", "2B", "2C")))) {
    foreach ($notasPorcentajes as $key => $value) {
      if ($value->total != '-') {
        if ($value->id_mat == 12) {
          // MATEMATICA
          $value->total = $value->total * 0.7;
        }
        if ($value->id_mat == 29) {
          // GEOMETRIA
          $value->total = $value->total * 0.3;
        }

        if ($value->id_mat == 38) {
          // FISICA
          $value->total = $value->total * 0.3;
        }
        if ($value->id_mat == 39) {
          // QUIMICA
          $value->total = $value->total * 0.3;
        }
        if ($value->id_mat == 14) {
          // INFORMATICA
          $value->total = $value->total * 0.4;
        }

        if ($value->id_mat == 1) {
          // LENGUAJE
          $value->total = $value->total * 0.5;
        }
        if ($value->id_mat == 27) {
          // GRAMATICA
          $value->total = $value->total * 0.5;
        }

        /*if ($value->id_mat == 10) {
            // EDUCACION MUSICAL
            $value->total = $value->total * 0.5;
          }
          if ($value->id_mat == 43) {
            // TALLER DE BANDA
            $value->total = $value->total * 0.5;
          }*/
      }
    }
  }
  /* Inicial B */
  if (is_numeric(array_search($curso, array("3A", "3B", "4A", "4B")))) {
    foreach ($notasPorcentajes as $key => $value) {
      if ($value->total != '-') {
        if ($value->id_mat == 12) {
          // MATEMATICA
          $value->total = $value->total * 0.7;
        }
        if ($value->id_mat == 29) {
          // GEOMETRIA
          $value->total = $value->total * 0.3;
        }
        if ($value->id_mat == 1) {
          // LENGUAJE
          $value->total = $value->total * 0.5;
        }
        if ($value->id_mat == 27) {
          // GRAMATICA
          $value->total = $value->total * 0.5;
        }
      }
    }
  }
  /* Area Exactas */
  if (is_numeric(array_search($curso, array("5A", "6A")))) {
    foreach ($notasPorcentajes as $key => $value) {
      if ($value->total != '-') {
        if ($value->id_mat == 12) {
          // MATEMATICA
          $value->total = $value->total * 0.8;
        }
        if ($value->id_mat == 29) {
          // GEOMETRIA
          $value->total = $value->total * 0.2;
        }
        if ($value->id_mat == 22) {
          // FISICA
          $value->total = $value->total * 0.75;
        }
        if ($value->id_mat == 31) {
          // LAB FISICA
          $value->total = $value->total * 0.25;
        }
        if ($value->id_mat == 23) {
          // QUIMICA
          $value->total = $value->total * 0.75;
        }
        if ($value->id_mat == 32) {
          // LAB QUIMICA
          $value->total = $value->total * 0.25;
        }
        if ($value->id_mat == 1) {
          // LENGUAJE
          $value->total = $value->total * 0.5;
        }
        if ($value->id_mat == 27) {
          // GRAMATICA
          $value->total = $value->total * 0.5;
        }
      }
    }
  }
  /* Area Salud */
  if (is_numeric(array_search($curso, array("5B", "6B")))) {
    foreach ($notasPorcentajes as $key => $value) {
      if ($value->total != '-') {
        if ($value->id_mat == 12) {
          // MATEMATICA
          $value->total = $value->total * 0.6;
        }
        if ($value->id_mat == 26) {
          // ESTADISTICA
          $value->total = $value->total * 0.4;
        }
        if ($value->id_mat == 21) {
          // BIOLOGIA
          $value->total = $value->total * 0.7;
        }
        if ($value->id_mat == 33) {
          // LAB BIOLOGIA
          $value->total = $value->total * 0.3;
        }
        if ($value->id_mat == 23) {
          // QUIMICA
          $value->total = $value->total * 0.75;
        }
        if ($value->id_mat == 32) {
          // LAB QUIMICA
          $value->total = $value->total * 0.25;
        }
        if ($value->id_mat == 1) {
          // LENGUAJE
          $value->total = $value->total * 0.5;
        }
        if ($value->id_mat == 27) {
          // GRAMATICA
          $value->total = $value->total * 0.5;
        }
      }
    }
  }
  /* Area Socio */
  if (is_numeric(array_search($curso, array("5C", "6C")))) {
    foreach ($notasPorcentajes as $key => $value) {
      if ($value->total != '-') {
        if ($value->id_mat == 6) {
          // HISTORIA
          $value->total = $value->total * 0.4;
        }
        if ($value->id_mat == 7) {
          // CIVICA
          $value->total = $value->total * 0.3;
        }
        if ($value->id_mat == 8) {
          // GEOGRAFIA
          $value->total = $value->total * 0.3;
        }

        if ($value->id_mat == 1) {
          // LENGUAJE
          $value->total = $value->total * 0.6;
        }
        if ($value->id_mat == 27) {
          // GRAMATICA
          $value->total = $value->total * 0.4;
        }

        /*if ($value->id_mat == 10) {
            // EDUCACION MUSICAL
            $value->total = $value->total * 0.5;
          }
          if ($value->id_mat == 43) {
            // TALLER DE BANDA
            $value->total = $value->total * 0.5;
          }*/

        if ($value->id_mat == 24) {
          // COSMOVISIONES, FILOSOFIA Y PSICOLOGIA
          $value->total = $value->total * 0.75;
        }
        if ($value->id_mat == 44) {
          // METODOLOGÍA DE INVESTIGACIÓN
          $value->total = $value->total * 0.25;
        }
      }
    }
  }
  return $notasPorcentajes;
}

/* Obtener Porcetaje de una Materia Secundaria Mañana CENTRALIZADOR */
function getMateriaPorcentaje($curso, $id_mat)
{
  /* Inicial A */
  if (is_numeric(array_search($curso, array("1A", "1B", "1C", "2A", "2B", "2C")))) {
    if ($id_mat == 12) {
      // MATEMATICA
      return 70;
    }
    if ($id_mat == 29) {
      // GEOMETRIA
      return 30;
    }

    /*if ($id_mat == 10) {
        // EDUCACION MUSICAL
        return 50;
      }
      if ($id_mat == 43) {
        // TALLER DE BANDA
        return 50;
      }*/

    if ($id_mat == 38) {
      // FISICA
      return 30;
    }
    if ($id_mat == 39) {
      // QUIMICA
      return 30;
    }
    if ($id_mat == 14) {
      // INFORMATICA
      return 40;
    }

    if ($id_mat == 1) {
      // LENGUAJE
      return 50;
    }
    if ($id_mat == 27) {
      // GRAMATICA
      return 50;
    } else {
      return 100;
    }
  }
  /* Inicial B */
  if (is_numeric(array_search($curso, array("3A", "3B", "4A", "4B")))) {
    if ($id_mat == 12) {
      // MATEMATICA
      return 70;
    }
    if ($id_mat == 29) {
      // GEOMETRIA
      return 30;
    }
    if ($id_mat == 1) {
      // LENGUAJE
      return 50;
    }
    if ($id_mat == 27) {
      // GRAMATICA
      return 50;
    } else {
      return 100;
    }
  }
  /* Area Exactas */
  if (is_numeric(array_search($curso, array("5A", "6A")))) {
    if ($id_mat == 12) {
      // MATEMATICA
      return 80;
    }
    if ($id_mat == 29) {
      // GEOMETRIA
      return 20;
    }
    if ($id_mat == 22) {
      // FISICA
      return 75;
    }
    if ($id_mat == 31) {
      // LAB FISICA
      return 25;
    }
    if ($id_mat == 23) {
      // QUIMICA
      return 75;
    }
    if ($id_mat == 32) {
      // LAB QUIMICA
      return 25;
    }
    if ($id_mat == 1) {
      // LENGUAJE
      return 50;
    }
    if ($id_mat == 27) {
      // GRAMATICA
      return 50;
    } else {
      return 100;
    }
  }
  /* Area Salud */
  if (is_numeric(array_search($curso, array("5B", "6B")))) {
    if ($id_mat == 12) {
      // MATEMATICA
      return 60;
    }
    if ($id_mat == 26) {
      // ESTADISTICA
      return 40;
    }
    if ($id_mat == 21) {
      // BIOLOGIA
      return 70;
    }
    if ($id_mat == 33) {
      // LAB BIOLOGIA
      return 30;
    }
    if ($id_mat == 23) {
      // QUIMICA
      return 75;
    }
    if ($id_mat == 32) {
      // LAB QUIMICA
      return 25;
    }
    if ($id_mat == 1) {
      // LENGUAJE
      return 50;
    }
    if ($id_mat == 27) {
      // GRAMATICA
      return 50;
    }
  }
  /* Area Socio */
  if (is_numeric(array_search($curso, array("5C", "6C")))) {
    if ($id_mat == 6) {
      // HISTORIA
      return 40;
    }
    if ($id_mat == 7) {
      // CIVICA
      return 30;
    }
    if ($id_mat == 8) {
      // GEOGRAFIA
      return 30;
    }
    if ($id_mat == 1) {
      // LENGUAJE
      return 60;
    }
    if ($id_mat == 27) {
      // GRAMATICA
      return 40;
    }
    if ($id_mat == 24) {
      // FILOSOFIA
      return 75;
    }
    if ($id_mat == 44) {
      // INVESTIGACIÓN
      return 25;
    }
  }
  return 100;
}

function getCountMaterias($curso, $turno)
{
  /* [19,34] 19: Electronica, 34: Diseño Grafico*/
  $especialidadA_SM = ["3ASM", "3BSM", "4ASM", "4BSM"];
  /* [18,19] 18: Sitemas informaticos, 19: Electronica */
  $especialidadB_SM = ["5ASM", "5BSM", "5CSM", "6ASM", "6BSM", "6CSM"];
  $especialidadB_ST = ["3AST", "3BST", "4AST", "4BST", "5AST", "5BST", "5CST", "6AST", "6BST", "6CST"];

  $cursoTurno = $curso . $turno;

  if ($turno == "SM") {
    if (is_numeric(array_search($cursoTurno, $especialidadA_SM))) {
      return count(getSM($curso)) - 1;
    }
    if (is_numeric(array_search($cursoTurno, $especialidadB_SM))) {
      return count(getSM($curso)) - 1;
    }
    return count(getSM($curso));
  }
  if ($turno == "ST") {
    if (is_numeric(array_search($cursoTurno, $especialidadB_ST))) {
      return count(getST($curso)) - 1;
    }
    return count(getST($curso));
  }
  if ($turno == "PM") {
    return count(getPM($curso));
  }
  if ($turno == "PT") {
    return count(getPT($curso));
  }
}

function checkCurso($curso)
{
  $items = array(
    "1A" => "PRIMERO A",
    "1B" => "PRIMERO B",
    "1C" => "PRIMERO C",
    "2A" => "SEGUNDO A",
    "2B" => "SEGUNDO B",
    "2C" => "SEGUNDO C",
    "3A" => "TERCERO A",
    "3B" => "TERCERO B",
    "3C" => "TERCERO C",
    "4A" => "CUARTO A",
    "4B" => "CUARTO B",
    "4C" => "CUARTO C",
    "5A" => "QUINTO A",
    "5B" => "QUINTO B",
    "5C" => "QUINTO C",
    "6A" => "SEXTO A",
    "6B" => "SEXTO B",
    "6C" => "SEXTO C",
  );
  if (isset($items[$curso])) {
    return $items[$curso];
  }
  return "CURSO";
}

function checkGrado($turno)
{
  $items = array(
    "PM" => "PRIMARIA",
    "SM" => "SECUNDARIA",
    "PT" => "PRIMARIA",
    "ST" => "SECUNDARIA",
  );
  if (isset($items[$turno])) {
    return $items[$turno];
  }
  return "GRADO";
}

function checkTurno($turno)
{
  $items = array(
    "PM" => "MAÑANA",
    "SM" => "MAÑANA",
    "PT" => "TARDE",
    "ST" => "TARDE",
  );
  if (isset($items[$turno])) {
    return $items[$turno];
  }
  return "TURNO";
}

function checkColegio($turno)
{
  $items = array(
    "PM" => "TECNICO HUMANISTICO DON BOSCO",
    "SM" => "TECNICO HUMANISTICO DON BOSCO",
    "PT" => "DON BOSCO A",
    "ST" => "DON BOSCO B",
  );
  if (isset($items[$turno])) {
    return $items[$turno];
  }
  return "COLEGIO";
}

function checkTrimestre($trimestre)
{
  $items = array(
    "primer" => "1ER TRIMESTRE",
    "segundo" => "2DO TRIMESTRE",
    "tercer" => "3ER TRIMESTRE",
  );
  if (isset($items[$trimestre])) {
    return $items[$trimestre];
  }
  return "TRIMESTRE";
}
