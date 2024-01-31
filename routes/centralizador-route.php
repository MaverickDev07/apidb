<?php

/* PUBLIC */
$app->get('/centralizador/acumulador/:curso/:turno/:gestion', function($curso, $turno, $gestion) use($app) {
  // $app->response->headers->set('Content-type', 'application/json');
  // $app->response->headers->set('Access-Control-Allow-Origin', '*');
  $app->response->headers->set('Access-Control-Allow-Origin','*');
  $app->response->headers->set('Content-type','application/vnd.ms-excel; charset=utf-8');
  $app->response->headers->set('Content-Disposition','attachment;filename='.$curso.'-'.$turno.' ACUMULADOR.xlsx');
  $app->response->headers->set('Expires','0');
  $app->response->headers->set('Cache-Control','must-revalidate, post-check=0, pre-check=0');
  header("Cache-Control: private",false);
  try {
    $obj = new CentralizadorModel();
    $app->response->status(200);
    $obj->generateAcumulador($curso, $turno, $gestion);
    //$app->response->body($obj->generateAcumulador($curso, $turno));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});

/* PUBLIC */
$app->get('/centralizador/acumulador/save/:curso/:turno/:gestion', function($curso, $turno, $gestion) use($app) {
  $app->response->headers->set('Content-type','application/json');
  $app->response->headers->set('Access-Control-Allow-Origin','*');

  try {
    $obj = new CentralizadorModel();
    $save = true;
    $response = $obj->generateAcumulador($curso, $turno, $gestion, $save);

    $app->response->status(200);
    $app->response->body(json_encode($response));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});

/* PUBLIC */
$app->get('/centralizador/:trimestre/:curso/:turno/:gestion', function($trimestre, $curso, $turno, $gestion) use($app){
  $app->response->headers->set('Access-Control-Allow-Origin','*');
  $app->response->headers->set('Content-type','application/vnd.ms-excel; charset=utf-8');
  $app->response->headers->set('Content-Disposition','attachment;filename='.$curso.'-'.$turno.' '.strtoupper($trimestre).' TRIMESTRE.xlsx');
  $app->response->headers->set('Expires','0');
  $app->response->headers->set('Cache-Control','must-revalidate, post-check=0, pre-check=0');
  header("Cache-Control: private",false);
  try {
    $obj = new CentralizadorModel();
    $app->response->status(200);
    $app->response->body($obj->generarCentralizador($curso, $turno, $trimestre, $gestion));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});

/* PUBLIC */
$app->get('/centralizador/save/:trimestre/:curso/:turno/:gestion', function($trimestre, $curso, $turno, $gestion) use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new CentralizadorModel();
    $response = $obj->generarCentralizadorSave($curso, $turno, $trimestre, $gestion);

    $app->response->status(200);
    $app->response->body(json_encode($response));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});

/* PUBLIC */
$app->get('/centralizador/application/:trimestre/:curso/:turno/:gestion', function($trimestre, $curso, $turno, $gestion) use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new CentralizadorModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->generarCentralizadorApplication($curso, $turno, $trimestre, $gestion)));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});

?>
