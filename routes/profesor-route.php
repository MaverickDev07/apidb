<?php

/* PRIVATE */
$app->get('/profesor/materias/:usuario/:gestion', function ($usuario, $gestion) use ($app) {
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new ProfesorModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->getMateriasNotas($usuario, $gestion)));
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

/* PRIVATE */
$app->get('/profesor/notas/:id_trimestre/:id_asg_prof/:gestion', function ($id_trimestre, $id_asg_prof, $gestion) use ($app) {
  $app->response->headers->set('Content-type', 'application/json');
  $app->response->headers->set('Access-Control-Allow-Origin', '*');
  try {
    $obj = new ProfesorModel();
    $app->response->status(200);
    $app->response->body(json_encode($obj->getNotasTrimestre($id_trimestre, $id_asg_prof, $gestion)));
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
