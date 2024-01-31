use transcolar_pro;

-- ----------------remove booking
DROP PROCEDURE IF EXISTS removeBooking;
DELIMITER $$
CREATE PROCEDURE removeBooking(
	IN _idBooking INT(11)
)
BEGIN

    DECLARE error INT DEFAULT 0;
    DECLARE msg TEXT DEFAULT '';
    DECLARE failed BOOLEAN DEFAULT false;
    -- AUXILIAR VAR
    DECLARE idStudent INT;
    DECLARE idTutorFather INT;
    DECLARE idTutorMother INT;
    DECLARE idGalery INT;
    DECLARE nameGalery VARCHAR(250);
    DECLARE idBuslineBooking INT;
    DECLARE count INT;
    
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
	BEGIN
		SET error=1;
		SELECT "HANDLER FOR SQLEXCEPTION" error,"Transacción no completada: removeBooking." msg,'true' failed;
	END;

    START TRANSACTION;

    -- GET ID_STUDENT
    SET idStudent = (SELECT studentID FROM booking WHERE id_booking = _idBooking LIMIT 1);
    
    if !isnull(idStudent) THEN
		-- Obtenemos id_tutor
        SET idTutorFather = (
			SELECT t.id_tutor FROM tutor_student as ts
            INNER JOIN tutor as t
            ON ts.tutorID = t.id_tutor
            WHERE t.type = 'father' AND ts.studentID = idStudent
            LIMIT 1
		);
        SET idTutorMother = (
			SELECT t.id_tutor FROM tutor_student as ts
            INNER JOIN tutor as t
            ON ts.tutorID = t.id_tutor
            WHERE t.type = 'mother' AND ts.studentID = idStudent
            LIMIT 1
		);
        -- Verificamos si no son nulos
        if !isnull(idTutorFather) THEN
			-- verificamos si existe repetido en tutor_student
            SET count = (SELECT COUNT(*) FROM tutor_student WHERE tutorID = idTutorFather);
            -- ELIMINAMOS tutor_student
            DELETE FROM tutor_student WHERE studentID = idStudent AND tutorID = idTutorFather;
            -- si solo es 1 eliminar tutor
            if count=1 THEN
				-- ELIMINAMOS tutor
                DELETE FROM tutor WHERE id_tutor = idTutorFather;
            END IF;
            
		END IF;
        if !isnull(idTutorMother) THEN
			-- verificamos si existe repetido en tutor_student
            SET count = (SELECT COUNT(*) FROM tutor_student WHERE tutorID = idTutorMother);
            -- ELIMINAMOS tutor_student
            DELETE FROM tutor_student WHERE studentID = idStudent AND tutorID = idTutorMother;
            -- si solo es 1 eliminar tutor
            if count=1 THEN
				-- ELIMINAMOS tutor
                DELETE FROM tutor WHERE id_tutor = idTutorMother;
            END IF;
		END IF;
        
        -- Obtenemos id_busline_booking
        
        SET idBuslineBooking = (
			SELECT bb.id_busline_booking FROM busline_stop AS bs
            INNER JOIN busline_booking AS bb
            ON bb.buslineID = bs.buslineID
            INNER JOIN booking as bk
            ON bk.buslineStopID = bs.id_busline_stop
            WHERE bk.studentID = idStudent
            LIMIT 1
        );
        
         if !isnull(idBuslineBooking) THEN
            SET count = (SELECT (bussy_now-1) FROM busline_booking WHERE id_busline_booking = idBuslineBooking);
            -- si solo es 1 eliminar tutor
            if count>=0 THEN
				UPDATE busline_booking SET bussy_now=count WHERE id_busline_booking = idBuslineBooking;
            END IF;
		END IF;
        
        DELETE FROM booking WHERE id_booking = _idBooking;
        
        -- Obtenemos id_galery
        SET idGalery = (
			SELECT g.id_galery FROM student AS s
            INNER JOIN galery AS g
            ON s.galeryID = g.id_galery
            WHERE s.id_student = idStudent
            LIMIT 1
		);        
        -- Obtenemos el nombre de imagen
        SET nameGalery = (SELECT url_image FROM galery WHERE id_galery = idGalery);
        
        -- Eliminamos Student
        DELETE FROM student WHERE id_student = idStudent;
        
        if idGalery<>1 THEN
			DELETE FROM galery WHERE id_galery = idGalery;
		END IF;
        
        SET msg = "Eliminado con exito";
    ELSE
		SET msg = "No es posible eliminar";
        SET failed = true;
    END IF;
    
    IF (error = 1) THEN
		ROLLBACK;
	ELSE
		SELECT idGalery, nameGalery, msg, failed;
		COMMIT;
	END IF;
END
$$

CALL removeBooking(25);



-- ----------------remove booking_pay
DROP PROCEDURE IF EXISTS removeBookingPay;
DELIMITER $$
CREATE PROCEDURE removeBookingPay(
	IN _idBookingPay INT(11)
)
BEGIN

    DECLARE error INT DEFAULT 0;
    DECLARE msg TEXT DEFAULT '';
    DECLARE failed BOOLEAN DEFAULT false;
    -- AUXILIAR VAR
    DECLARE idGalery INT;
    DECLARE nameGalery VARCHAR(250);
    DECLARE isPosible INT;
    
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
	BEGIN
		SET error=1;
		SELECT "HANDLER FOR SQLEXCEPTION" error,"Transacción no completada: removeBookingPay." msg,'true' failed;
	END;

    START TRANSACTION;

    -- GET ID_STUDENT
    SET isPosible = (SELECT id_booking_pay FROM booking_pay WHERE id_booking_pay = _idBookingPay ORDER BY id_booking_pay DESC LIMIT 1);
    
    if !isnull(isPosible) THEN
		-- obtener id_galery
        SET idGalery = (
			SELECT g.id_galery FROM booking_pay AS bp
            INNER JOIN galery AS g
            ON bp.galeryID = g.id_galery
            WHERE bp.id_booking_pay = isPosible
            ORDER BY bp.id_booking_pay LIMIT 1
		);
        -- ELIMINAMOS PRIMERO booking_pay
        DELETE FROM booking_pay WHERE id_booking_pay = isPosible;
        -- ELIMINAMOS GALERY excepto default(1)
        
        -- Obtenemos el nombre de imagen
        SET nameGalery = (SELECT url_image FROM galery WHERE id_galery = idGalery);
        
        if idGalery<>1 THEN
			DELETE FROM galery WHERE id_galery = idGalery;
		END IF;
        
        SET msg = "Eliminado con exito";
    ELSE
		SET msg = "No es posible eliminar";
        SET failed = true;
    END IF;
    
    IF (error = 1) THEN
		ROLLBACK;
	ELSE
		SELECT idGalery, nameGalery, msg, failed;
		COMMIT;
	END IF;
END
$$

CALL removeBookingPay(6);


-- ----------------remove booking saving
DROP PROCEDURE IF EXISTS removeBookingSaving;
DELIMITER $$
CREATE PROCEDURE removeBookingSaving(
	IN _idBooking INT(11)
)
BEGIN

    DECLARE error INT DEFAULT 0;
    DECLARE msg TEXT DEFAULT '';
    DECLARE failed BOOLEAN DEFAULT false;
    -- AUXILIAR VAR
    DECLARE idStudent INT;
    DECLARE idTutorFather INT;
    DECLARE idTutorMother INT;
    DECLARE idGalery INT;
    DECLARE nameGalery VARCHAR(250);
    DECLARE idBusline INT;
    DECLARE idReservation INT;
    DECLARE idBookingSave INT;
    DECLARE idBookingNew INT;
    DECLARE count INT;
    
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
	BEGIN
		SET error=1;
		SELECT "HANDLER FOR SQLEXCEPTION" error,"Transacción no completada: removeBookingSaving." msg,'true' failed;
	END;

    START TRANSACTION;

    -- GET ID_STUDENT
    SET idStudent = (SELECT studentID FROM booking WHERE id_booking = _idBooking LIMIT 1);
    
    if !isnull(idStudent) THEN
		-- Obtenemos id_tutor
        SET idTutorFather = (
			SELECT t.id_tutor FROM tutor_student as ts
            INNER JOIN tutor as t
            ON ts.tutorID = t.id_tutor
            WHERE t.type = 'father' AND ts.studentID = idStudent
            ORDER BY t.id_tutor LIMIT 1
		);
        SET idTutorMother = (
			SELECT t.id_tutor FROM tutor_student as ts
            INNER JOIN tutor as t
            ON ts.tutorID = t.id_tutor
            WHERE t.type = 'mother' AND ts.studentID = idStudent
            ORDER BY t.id_tutor LIMIT 1
		);
        -- Verificamos si no son nulos
        if !isnull(idTutorFather) THEN
			-- verificamos si existe repetido en tutor_student
            SET count = (SELECT COUNT(*) FROM tutor_student WHERE tutorID = idTutorFather);
            -- ELIMINAMOS tutor_student
            DELETE FROM tutor_student WHERE studentID = idStudent AND tutorID = idTutorFather;
            -- si solo es 1 eliminar tutor
            if count=1 THEN
				-- ELIMINAMOS tutor
                DELETE FROM tutor WHERE id_tutor = idTutorFather;
            END IF;
            
		END IF;
        if !isnull(idTutorMother) THEN
			-- verificamos si existe repetido en tutor_student
            SET count = (SELECT COUNT(*) FROM tutor_student WHERE tutorID = idTutorMother);
            -- ELIMINAMOS tutor_student
            DELETE FROM tutor_student WHERE studentID = idStudent AND tutorID = idTutorMother;
            -- si solo es 1 eliminar tutor
            if count=1 THEN
				-- ELIMINAMOS tutor
                DELETE FROM tutor WHERE id_tutor = idTutorMother;
            END IF;
		END IF;
        
        -- Obtenemos id_busline        
        SET idBusline = (
            SELECT bs.buslineID FROM busline_stop AS bs
            INNER JOIN booking AS bk
            ON bk.buslineStopID = bs.id_busline_stop
            WHERE bk.id_booking = _idBooking
            LIMIT 1
        );
        -- Obtenemos id_reservation
        SET idReservation = (SELECT reservationID FROM booking WHERE id_booking = _idBooking LIMIT 1);
        
        DELETE FROM booking WHERE id_booking = _idBooking;
        
        -- Obtenemos id_galery
        SET idGalery = (
			SELECT g.id_galery FROM student AS s
            INNER JOIN galery AS g
            ON s.galeryID = g.id_galery
            WHERE s.id_student = idStudent
            LIMIT 1
		);
        -- Obtenemos el nombre de imagen
        SET nameGalery = (SELECT url_image FROM galery WHERE id_galery = idGalery);
        
        -- Eliminamos Estudiante
        DELETE FROM student WHERE id_student = idStudent;
        
        if idGalery<>1 THEN
			DELETE FROM galery WHERE id_galery = idGalery;
		END IF;
        
        -- Creamos una nueva reserva en blanco
        
        INSERT INTO booking VALUES (null, null, null, idReservation, '', CURRENT_TIMESTAMP);
        SET idBookingNew = @@identity;
        
        -- Creamos una referencia de la reserva
        INSERT INTO booking_save VALUES (null, idBusline, idBookingNew, 1 , CURRENT_TIMESTAMP);        
        SET idBookingSave = @@identity;
        
        SET msg = "Eliminado con exito";
    ELSE
		SET msg = "No es posible eliminar";
        SET failed = true;
    END IF;
    
    IF (error = 1) THEN
		ROLLBACK;
	ELSE
		SELECT idGalery, nameGalery, idBookingSave, msg, failed;
		COMMIT;
	END IF;
END
$$

CALL removeBookingSaving(28);