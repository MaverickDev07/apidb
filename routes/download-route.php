<?php

/* PUBLIC */
$app->get('/download/notas/:gestion', function($gestion) use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new DownloadModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->checkNotas($gestion)));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});

/* PUBLIC */
$app->get('/download/asignacion/:gestion', function($gestion) use($app){
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new DownloadModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->checkAsignacion($gestion)));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});
?>
