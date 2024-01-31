## Cambiar boletines

### Editar archivos

```bash
nvim plaforma/application/views/Estudiantes_su_view.php # Linea 13, para el enlace y la 146 para el título
```

#### El generador está en apidb tspdf
Si queremos colocar el promedio, modificamos el tamaño de las columnas
```bash

```

### Tablas utilizadas:

* asiginar_profesorm
* galery
* preinscripcion
* nota_trimestre
* materias
* areas
* campo
* bimestre --> activo: para saber en que bimestre está
* acceso_token

### Tablas deprecadas:

* abandono
* 
* 
* 
* 

### Observaciones:

* Este endpoint no existe en dombosco-app: /download/notas --> lo cambié a /download/notas/:gestion
* Este endpoint no existe en dombosco-app: /download/asignacion --> lo cambié a /download/asignacion/:gestion

* Este endpoint no existe en dombosco-app: /galery/add y este otro tampoco /galery/delete


// Editar lineas

security-route.php: 11


security-model.php: 185

### Plataforma:

Subir Notas: application/controllers/Not_notas_subir_contr.php --> 918
Planilla Trimestre: application/controllers/Not_notas_contr.php --> 1250
Modulo de Notas (ROOT): application/views/principal_view.php --> [353, 481]
Notas Profesores (gestión quemado): application/controllers/Not_notas_contr.php --> [39, 40]

#### Asignar Cursos Niveles y Colegio
Guardar:
asiginar_curso
cursos
niveles
colegio

Listar:
nivel_curso
cursos

### Centralizador:

models/centralizador-model.php  —> 306, 233

## Centralizadores
### Profesores PM:
pwd general: d0n*sucr3
LENGUAJE (1): [m.munguia]
INGLES (3): [i.claure]
CIENCIAS SOCIALES (5): [m.munguia]
EDUCACION FISICA Y DEPORTE (9): [j.coronado, y.choque]
EDUCACION MUSICAL (10): [m.averanga]
ARTES PLASTICAS Y VISUALES (11): [c.guzman]
MATEMATICA (12): [m.munguia]
INFORMATICA PRIMARIA (13): [j.leyton]
CIENCIAS NATURALES (20): [m.munguia]
VALORES, ESPIRITUALIDAD Y RELIGIONES (25): [c.nava]

INGLES (4): i.claure
MATEMATICA (12): m.munguia

### Problemas con:
FISICA: [22, 38]
QUIMICA: [23, 39]

### Permisos PHP
sudo chown -R gary:www-data ~/coder/files/Centralizador.xlsx
sudo usermod -aG www-data gary
sudo chgrp www-data files
sudo usermod -a -G www-data gary
sudo chown -R www-data:www-data /var/lib/phpmyadmin



### Modificaciones por Trimestre:

__Archivo:__ `../plataforma/application/controllers/Not_notas_subir_contr.php`
__Líneas:__ `[875, 888]`

__Archivo:__ `/var/www/apidb/models/download-model.php`
__Líneas:__ `[51]`


### Modificar imprir kardex por estudiante

__Archivo:__ `plataforma/application/controllers/Karde_contr.php`
__Líneas:__ `[909]`
