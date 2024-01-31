use transcolar_pro;

select * from bus;

DROP VIEW IF EXISTS view_busline_detail;
CREATE VIEW view_busline_detail AS SELECT 
BL.id_busline, L.id_line, L.name,
BL.level, BL.line_order, BL.price, BL.schoolID,
L.color, B.capacity, B.id_bus, BS.id_busline_stop,
BS.stop_order, S.address, S.lat, S.lng
from busline as BL
INNER JOIN bus as B
ON B.id_bus = BL.busID
INNER JOIN line as L
ON L.id_line = BL.lineID
INNER JOIN busline_stop BS
ON BS.buslineID = BL.id_busline
INNER JOIN stop as S
ON S.id_stop = BS.stopID
ORDER BY BL.id_busline;


DROP VIEW IF EXISTS view_check_account;
CREATE VIEW view_check_account AS SELECT
BS.id_busline_stop, BL.price, BK.account, BK.bank, BK.owner
FROM busline as BL
INNER JOIN busline_stop BS
ON BS.buslineID = BL.id_busline
INNER JOIN school SC
ON SC.id_school = BL.schoolID
INNER JOIN bank_account BK
ON SC.bankAccountID = BK.id_bank_account
ORDER BY BS.id_busline_stop;

-- Nuevas Vistas  (31 Enero 2020)

DROP VIEW IF EXISTS view_tracking_student_list;
CREATE VIEW view_tracking_student_list AS SELECT
T.id_tutor, ST.id_student,
T.first_name as tutor_first_name, T.last_name as tutor_last_name, T.cellphone,
ST.first_name as student_first_name, ST.last_name as student_last_name, ST.age
FROM tutor_student as TS
INNER JOIN tutor as T
ON T.id_tutor = TS.tutorID
INNER JOIN student as ST
ON ST.id_student = TS.studentID;

DROP VIEW IF EXISTS view_tracking_starter;
CREATE VIEW view_tracking_starter AS
SELECT *
FROM booking as BK
INNER JOIN busline_stop as BS
ON BS.id_busline_stop = BK.buslineStopID
WHERE BK.studentID = 33
LIMIT 1;