<?php
	session_start();

	require 'vendor/autoload.php';

	\Slim\Slim::registerAutoloader();
	\Moment\Moment::setLocale('es_ES');

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	$app = new \Slim\Slim();

	$corsOptions = array(
    	"origin" => "*",
    	"exposeHeaders" => array(
			"X-API-KEY", "Origin", "X-Requested-With" , "Authorization" ,"Content-Type", "Accept", "Access-Control-Request-Method", "x-xsrf-token"
		),
		"maxAge" => 1728000,
    	"allowCredentials" => True,
		"allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS')
	);
	$app->add(new \CorsSlim\CorsSlim($corsOptions));

	/* Autoload */
	$folders = [
		'common',
		'models',
		'routes',
		'helpers'
	];
	foreach ($folders as $f) {
		foreach (glob("$f/*.php") as $k => $filename) {
			require $filename;
		}
	} 

	
	/* Hello World */
	$app->get('/', function(){
		echo 'API REST, Don Bosco Sucre © 2021';
	});

	$app->get('/tester', function() use($app){
		$app->response->headers->set('Access-Control-Allow-Origin','*');
		$app->response->headers->set('Content-type','application/vnd.ms-excel; charset=utf-8');
		$app->response->headers->set('Content-Disposition','attachment;filename=informe.xlsx');
		$app->response->headers->set('Expires','0');
		$app->response->headers->set('Cache-Control','must-revalidate, post-check=0, pre-check=0');
		header("Cache-Control: private",false);
		try {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', 'Hello World !');

			$writer = new Xlsx($spreadsheet);
			$writer->save('php://output');
		}catch(PDOException $e) {
				$app->response->status(500);
				$app->response->body('No se encontro nada');
		}
	});

	$app->run();