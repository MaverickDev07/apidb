/* Consultas */
SELECT
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
LEFT OUTER JOIN materias as MAT
ON NT.id_mat = MAT.id_mat
LEFT OUTER JOIN areas as AR
ON MAT.id_area = AR.id_area
LEFT OUTER JOIN campo as CAM
ON AR.id_campo = CAM.id_campo
WHERE NT.gestion = 2021 AND NT.id_est = 2293 AND NT.id_bi = 5 AND
(MAT.id_mat = 1 OR MAT.id_mat = 3 OR MAT.id_mat = 5 OR MAT.id_mat = 9 OR MAT.id_mat = 10 OR MAT.id_mat = 11 OR MAT.id_mat = 12 OR MAT.id_mat = 13 OR MAT.id_mat = 20 OR MAT.id_mat = 25 OR MAT.id_mat = 27 OR MAT.id_mat = 29 OR MAT.id_mat = 37)
ORDER BY CAM.id_campo, AR.id_area



SELECT
  NT.id_mat,
  MAT.nombre,
  NT.total,
  NT.id_bi,
  NT.id_est
FROM nota_trimestre as NT
LEFT OUTER JOIN materias as MAT
WHERE NT.gestion = 2021 AND NT.id_est = 2293 AND NT.id_bi = 5 AND
(MAT.id_mat = 1 OR MAT.id_mat = 3 OR MAT.id_mat = 5 OR MAT.id_mat = 9 OR MAT.id_mat = 10 OR MAT.id_mat = 11 OR MAT.id_mat = 12 OR MAT.id_mat = 13 OR MAT.id_mat = 20 OR MAT.id_mat = 25 OR MAT.id_mat = 27 OR MAT.id_mat = 29 OR MAT.id_mat = 37)


SELECT
  NT.id_mat,
  MAT.nombre,
  NT.total,
  NT.id_bi,
  NT.id_est
FROM materias as MAT
LEFT OUTER JOIN nota_trimestre as NT
ON NT.id_mat = MAT.id_mat
WHERE NT.gestion = 2021 AND NT.id_est = 2293 AND NT.id_bi = 5 AND
(MAT.id_mat = 1 OR MAT.id_mat = 3 OR MAT.id_mat = 5 OR MAT.id_mat = 9 OR MAT.id_mat = 10 OR MAT.id_mat = 11 OR MAT.id_mat = 12 OR MAT.id_mat = 13 OR MAT.id_mat = 20 OR MAT.id_mat = 25 OR MAT.id_mat = 27 OR MAT.id_mat = 29 OR MAT.id_mat = 37)

/*
  Obtener id_asg_ma
  para la tabla de nota_trimestre
*/
SELECT
*
FROM asiginar_profesorm as AP
INNER JOIN asiginar_materiacu as AM
ON AM.id_asg_mate = AP.id_asg_mate
WHERE
AM.id_mat = 1 AND
AP.gestion = 2021 AND
AP.codigo LIKE '%2A-PM%'

/* Buscar estudiante */
SELECT
* FROM (
SELECT * , CONCAT(nombre,' ' ,appaterno,' ', apmaterno) as info FROM estudiantes
) as busqueda
INNER JOIN nota_prom as NP
ON busqueda.id_est = NP.id_est
WHERE busqueda.info LIKE '%Jorge%' AND NP.gestion = 2021

/* Obtener materias de un profesor */
SELECT
ASP.id_prof,
ASP.gestion,
ASP.codigo,
MAT.id_mat,
MAT.nombre as materia_nombre,
MAT.sigla as materia_sigla
FROM asiginar_profesorm as ASP
INNER JOIN asiginar_materiacu as AMC
ON AMC.id_asg_mate = ASP.id_asg_mate
INNER JOIN materias as MAT
ON MAT.id_mat = AMC.id_mat
WHERE ASP.id_prof = 100 AND ASP.gestion = 2021

-- Obtener las notas subidas de todas las materias asignadas a un profesor
SELECT
DISTINCT APM.id_asg_prof,
MAT.id_mat, MAT.nombre,
NT.id_bi, NT.gestion
FROM asiginar_profesorm as APM
LEFT OUTER JOIN nota_trimestre as NT
ON APM.id_asg_prof = NT.id_asg_prof
INNER JOIN asiginar_materiacu as AMC
ON APM.id_asg_mate = AMC.id_asg_mate
INNER JOIN materias as MAT ON
AMC.id_mat = MAT.id_mat
WHERE APM.id_prof = 110 AND APM.gestion = 2021

/* Obtener al profesor  por usuario */
SELECT
*
FROM usuario as US
INNER JOIN profesor as PROF
ON US.nombre = PROF.nombres AND
US.appat = PROF.appaterno AND
US.apmat = PROF.apmaterno

/* Obtener notas de los estudiantes por profesor */
SELECT
ES.id_est,
ES.nombre,
ES.appaterno,
ES.apmaterno,
ES.ci,
ES.genero,
NT.*
FROM nota_trimestre as NT
INNER JOIN estudiantes as ES
ON ES.id_est = NT.id_est
WHERE
NT.id_asg_prof = 2484 AND
NT.id_bi = 5 AND
NT.gestion = 2021
ORDER BY ES.appaterno, ES.apmaterno, ES.nombre ASC

/* Obtener profesores de un curso */
SELECT
ASP.id_asg_prof,
AM.id_mat,
PROF.id_prof,
PROF.nombre,
PROF.appaterno,
PROF.apmaterno,
ASP.codigo,
ASP.gestion
FROM asiginar_profesorm as ASP
INNER JOIN asiginar_materiacu as AM
ON ASP.id_asg_mate = AM.id_asg_mate
INNER JOIN profesores as PROF
ON ASP.id_prof = PROF.id_prof
WHERE
AM.codigo LIKE CONCAT('%','1A-SM','%') AND
ASP.gestion = 2021
ORDER BY AM.id_mat ASC