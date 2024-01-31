<?php

/* PUBLIC */
$app->get('/boletines/enabled/:id_not_pro', function ($id_not_pro) use ($app) {
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new BoletinesModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->enabledBoletin($id_not_pro)));
  } catch (PDOException $e) {
    $app->response->status(500);
    $app->response->body(
      json_encode([
        'result' => [],
        'status' => false,
        'message' => $e->getMessage(),
        'error' => '500',
      ])
    );
  }
});

/* PUBLIC */
$app->get('/boletines/disabled/:id_not_pro', function ($id_not_pro) use ($app) {
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new BoletinesModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->disabledBoletin($id_not_pro)));
  } catch (PDOException $e) {
    $app->response->status(500);
    $app->response->body(
      json_encode([
        'result' => [],
        'status' => false,
        'message' => $e->getMessage(),
        'error' => '500',
      ])
    );
  }
});

/* PUBLIC */
$app->get('/boletines/:trimestre/:estudiante', function($trimestre, $estudiante) use($app){
  $app->response->headers->set('Content-type','application/pdf');
  $app->response->headers->set('Content-Disposition','attachment;filename="boletin.pdf"');
  $app->response->headers->set('Access-Control-Allow-Origin','*');
  try {
    $obj = new BoletinesModel();
    $app->response->status(200);
    $app->response->body($obj->generarBoletin($trimestre, $estudiante));
  }catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});

/* PUBLIC */
$app->get('/bole/tines/curso/:trimestre/:curso/:turno/:gestion', function($trimestre, $curso, $turno, $gestion) use($app) {
  $app->response->headers->set('Content-type','application/pdf');
  $app->response->headers->set('Content-Disposition','attachment;filename="boletin.pdf"');
  // $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');

  try {
    $obj = new BoletinesModel();
    $app->response->status(200);
    $app->response->body( json_encode($obj->generarBoletinTrimestreCurso($curso, $turno, $trimestre, $gestion)) );
  } catch(PDOException $e) {
    $app->response->status(500);
    $app->response->body('No se encontro nada');
  }
});

?>
