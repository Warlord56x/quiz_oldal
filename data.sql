-- Drop szekvencia re-create -hez.
/*
DROP TABLE Rosszvalasz;
DROP TABLE Kerdes;
DROP TABLE Keszit;
DROP TABLE Kitolt;
DROP TABLE Kategoria;
DROP TABLE Quiz;
DROP TABLE Felhasznalo;

DROP SEQUENCE seq_felhasznalo;
DROP SEQUENCE seq_kerdes;
DROP SEQUENCE seq_quiz;
*/

create table Felhasznalo(
                            felhasznalo_id NUMBER NOT NULL,
                            email VARCHAR2(255) NOT NULL,
                            jelszo VARCHAR2(255) NOT NULL,
                            vezeteknev VARCHAR2(255) NOT NULL,
                            keresztnev VARCHAR2(255) NOT NULL,
                            eletkor NUMBER NOT NULL,
                            osszpontszam NUMBER,
                            PRIMARY KEY(felhasznalo_id)
);

create table Quiz(
                     quiz_id NUMBER NOT NULL,
                     quiz_nev VARCHAR2(255),
                     rangsor NUMBER,
                     kitoltes_szam NUMBER,
                     PRIMARY KEY(quiz_id)
);

create table Kerdes(
                       kerdes_id NUMBER NOT NULL,
                       quiz_id NUMBER NOT NULL,
                       kerdes VARCHAR2(255) NOT NULL,
                       jo_valasz VARCHAR2(255) NOT NULL,
                       PRIMARY KEY(kerdes_id),
                       FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id)
);

create table Rosszvalasz(
                            kerdes_id NUMBER NOT NULL,
                            rossz_valasz VARCHAR2(255) NOT NULL,
                            FOREIGN KEY(kerdes_id) REFERENCES Kerdes(kerdes_id)
);

create table Kategoria(
                          quiz_id NUMBER NOT NULL,
                          kategoria VARCHAR2(255) NOT NULL,
                          FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id)
);

create table Kitolt(
                       felhasznalo_id NUMBER NOT NULL,
                       quiz_id NUMBER NOT NULL,
                       kitoltes_idopont DATE,
                       kitoltes_pontszam NUMBER,
                       FOREIGN KEY(felhasznalo_id) REFERENCES Felhasznalo(felhasznalo_id),
                       FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id)
);

create table Keszit(
                       felhasznalo_id NUMBER NOT NULL,
                       quiz_id NUMBER NOT NULL,
                       letrehozas_idopont DATE,
                       FOREIGN KEY(felhasznalo_id) REFERENCES Felhasznalo(felhasznalo_id),
                       FOREIGN KEY(quiz_id) REFERENCES Quiz(quiz_id)
);

CREATE SEQUENCE seq_felhasznalo
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    CACHE 100;

CREATE SEQUENCE seq_quiz
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    CACHE 100;

CREATE SEQUENCE seq_kerdes
    MINVALUE 1
    START WITH 1
    INCREMENT BY 1
    CACHE 100;

INSERT INTO Felhasznalo VALUES(seq_felhasznalo.nextval, 'dummyuser@gmail.com', 'dummypassword', 'Vezetek', 'Kereszt', 1, 18);
INSERT INTO Felhasznalo VALUES(seq_felhasznalo.nextval, 'notsodummyuser@gmail.com', 'notsodummypassword', 'notVezetek', 'notKereszt', 1, 15);
INSERT INTO Felhasznalo VALUES(seq_felhasznalo.nextval, 'user@user.com', 'user', 'User1', 'User2', 1, 17);
INSERT INTO Felhasznalo VALUES(seq_felhasznalo.nextval, 'myuser@myuser.com', 'userpassword', 'MynameisAfganisztán', 'ButWithOutThis', 1, 20);

INSERT INTO Quiz VALUES(seq_quiz.nextval, 'Matekos kérdések', 0, 2); -- Max pont 8
INSERT INTO Quiz VALUES(seq_quiz.nextval, 'Földrajz kérdések', 0, 0); -- Max pont 8
INSERT INTO Quiz VALUES(seq_quiz.nextval, 'Általános tudnivalok',0, 1); -- Max pont 8

-- Kérdések 1. quizhez
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 1, 'Mennyi 2+2?', '4');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 1, 'Mennyi 2-2?', '0');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 1, 'Mennyi 2*2?', '4');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 1, 'Mennyi 2/2?', '1');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 1, 'Mennyi 3+2?', '5');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 1, 'Mennyi 3-2?', '1');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 1, 'Mennyi 3*2?', '6');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 1, 'Mennyi 3/2?', '1.5');


-- Kérdések 2. quizhez
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 2, 'Hol van Magyarország?', 'Európa');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 2, 'Hol van Lengyelország?', 'Európa');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 2, 'Hol van Németország?', 'Európa');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 2, 'Hol van Olaszország?', 'Európa');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 2, 'Hol van Spanyolország?', 'Európa');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 2, 'Hol van Szerbia?', 'Európa');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 2, 'Hol van az Amerika Egyesült Államok?', 'Észak-Amerika');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 2, 'Hol van Kína?', 'Ázsia');

-- Kérdések 3. quizhez
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 3, 'Pozsony melyik ország fővárosa?', 'Szlovákia');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 3, 'Mivel egészítenéd ki? Ne igyál előre a ... bőrére.
?', 'Medve');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 3, 'Ki volt Poszeidon a görög mitológiában?', 'A tenger istene');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 3, 'Melyik bolygó van a legközelebb a Naphoz, a Naprendszerünkben?', 'Merkúr');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 3, 'Milyen tudomány az archeológia?', 'Régészet');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 3, 'Embereknél hol található az egyensúlyozás szerve?', 'Belsőfülben');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 3, 'Hány megabájt egy gigabájt?', '1024');
INSERT INTO Kerdes VALUES(seq_kerdes.nextval, 3, 'Mit rövidít az IT?', 'Information Technologies');



-- Quiz 1. rossz válaszok
INSERT INTO Rosszvalasz VALUES(1, '3');
INSERT INTO Rosszvalasz VALUES(1, '0');
INSERT INTO Rosszvalasz VALUES(1, '7');

INSERT INTO Rosszvalasz VALUES(2, '-2');
INSERT INTO Rosszvalasz VALUES(2, '4');
INSERT INTO Rosszvalasz VALUES(2, '62.38');

INSERT INTO Rosszvalasz VALUES(3, '8');
INSERT INTO Rosszvalasz VALUES(3, '2');
INSERT INTO Rosszvalasz VALUES(3, '6');

INSERT INTO Rosszvalasz VALUES(4, '2');
INSERT INTO Rosszvalasz VALUES(4, '4');
INSERT INTO Rosszvalasz VALUES(4, '0');

INSERT INTO Rosszvalasz VALUES(5, '6');
INSERT INTO Rosszvalasz VALUES(5, '4');
INSERT INTO Rosszvalasz VALUES(5, '-3');

INSERT INTO Rosszvalasz VALUES(6, '-3');
INSERT INTO Rosszvalasz VALUES(6, '3');
INSERT INTO Rosszvalasz VALUES(6, '-1');

INSERT INTO Rosszvalasz VALUES(7, '9');
INSERT INTO Rosszvalasz VALUES(7, '16');
INSERT INTO Rosszvalasz VALUES(7, '7.5');

INSERT INTO Rosszvalasz VALUES(8, '3');
INSERT INTO Rosszvalasz VALUES(8, '3');
INSERT INTO Rosszvalasz VALUES(8, '3');


-- Quiz 2 rossz válaszok
INSERT INTO Rosszvalasz VALUES(9, 'Ázsia');
INSERT INTO Rosszvalasz VALUES(9, 'Dél-Amerika');
INSERT INTO Rosszvalasz VALUES(9, 'Antarktisz');

INSERT INTO Rosszvalasz VALUES(10, 'Ázsia');
INSERT INTO Rosszvalasz VALUES(10, 'Óceánia');
INSERT INTO Rosszvalasz VALUES(10, 'Kecskemét');

INSERT INTO Rosszvalasz VALUES(11, 'Kanada');
INSERT INTO Rosszvalasz VALUES(11, 'Ázsia');
INSERT INTO Rosszvalasz VALUES(11, 'Dél-Amerika');

INSERT INTO Rosszvalasz VALUES(12, 'Dél-Amerika');
INSERT INTO Rosszvalasz VALUES(12, 'Észak-Amerika');
INSERT INTO Rosszvalasz VALUES(12, 'Szerda');

INSERT INTO Rosszvalasz VALUES(13, 'Ázsia');
INSERT INTO Rosszvalasz VALUES(13, 'Antarktisz');
INSERT INTO Rosszvalasz VALUES(13, 'Dél-Amerika');

INSERT INTO Rosszvalasz VALUES(14, 'Óceánia');
INSERT INTO Rosszvalasz VALUES(14, 'Pécs');
INSERT INTO Rosszvalasz VALUES(14, 'Románia');

INSERT INTO Rosszvalasz VALUES(15, 'Anglia');
INSERT INTO Rosszvalasz VALUES(15, 'Kanada');
INSERT INTO Rosszvalasz VALUES(15, 'Afrika');

INSERT INTO Rosszvalasz VALUES(16, 'Afrika');
INSERT INTO Rosszvalasz VALUES(16, 'Óceánia');
INSERT INTO Rosszvalasz VALUES(16, 'Európa');


-- Quiz 3 rossz válaszok
INSERT INTO Rosszvalasz VALUES(17, 'Csehország');
INSERT INTO Rosszvalasz VALUES(17, 'Szlovénia');
INSERT INTO Rosszvalasz VALUES(17, 'Magyarország');

INSERT INTO Rosszvalasz VALUES(18, 'Kecske');
INSERT INTO Rosszvalasz VALUES(18, 'Kutya');
INSERT INTO Rosszvalasz VALUES(18, 'Macska');

INSERT INTO Rosszvalasz VALUES(19, 'A tűz istene');
INSERT INTO Rosszvalasz VALUES(19, 'A villámok istene');
INSERT INTO Rosszvalasz VALUES(19, 'A hülyeség istene');

INSERT INTO Rosszvalasz VALUES(20, 'Vénusz');
INSERT INTO Rosszvalasz VALUES(20, 'Uránusz');
INSERT INTO Rosszvalasz VALUES(20, 'Mars');

INSERT INTO Rosszvalasz VALUES(21, 'Embertan');
INSERT INTO Rosszvalasz VALUES(21, 'Növénytan');
INSERT INTO Rosszvalasz VALUES(21, 'Földtan');

INSERT INTO Rosszvalasz VALUES(22, 'Hasban');
INSERT INTO Rosszvalasz VALUES(22, 'Fejben');
INSERT INTO Rosszvalasz VALUES(22, 'A lábban');

INSERT INTO Rosszvalasz VALUES(23, '1000');
INSERT INTO Rosszvalasz VALUES(23, 'Csak 1 lehet mert ugyanaz.');
INSERT INTO Rosszvalasz VALUES(23, '666');

INSERT INTO Rosszvalasz VALUES(24, 'Infantilis Törvények');
INSERT INTO Rosszvalasz VALUES(24, 'Information Teslanologies');
INSERT INTO Rosszvalasz VALUES(24, 'Inf.u-szeged.hu Többit_nem_tudom');

-- Quiz kategóriák
INSERT INTO Kategoria VALUES(1, 'Matematika');
INSERT INTO Kategoria VALUES(1, 'Kezdő');
INSERT INTO Kategoria VALUES(1, 'Első quizem');
INSERT INTO Kategoria VALUES(2, 'Földrajz');
INSERT INTO Kategoria VALUES(2, 'Kezdő');
INSERT INTO Kategoria VALUES(3, 'Vegyes');
INSERT INTO Kategoria VALUES(3, 'Általános ismeretek');
INSERT INTO Kategoria VALUES(3, 'Saját kategória');

-- Kitöltések
INSERT INTO Kitolt VALUES(1, 1, TO_DATE('2023 01 27', 'yyyy mm dd'), 8);
INSERT INTO Kitolt VALUES(1, 2, TO_DATE('2023 02 17', 'yyyy mm dd'), 4);
INSERT INTO Kitolt VALUES(1, 3, TO_DATE('2023 03 26', 'yyyy mm dd'), 6);

INSERT INTO Kitolt VALUES(2, 1, TO_DATE('2023 02 12', 'yyyy mm dd'), 8);
INSERT INTO Kitolt VALUES(2, 2, TO_DATE('2023 02 13', 'yyyy mm dd'), 2);
INSERT INTO Kitolt VALUES(2, 3, TO_DATE('2023 03 28', 'yyyy mm dd'), 5);

INSERT INTO Kitolt VALUES(3, 1, TO_DATE('2023 01 28', 'yyyy mm dd'), 3);
INSERT INTO Kitolt VALUES(3, 2, TO_DATE('2023 02 27', 'yyyy mm dd'), 6);
INSERT INTO Kitolt VALUES(3, 3, TO_DATE('2023 03 25', 'yyyy mm dd'), 8);

INSERT INTO Kitolt VALUES(4, 1, TO_DATE('2023 01 29', 'yyyy mm dd'), 4);
INSERT INTO Kitolt VALUES(4, 2, TO_DATE('2023 02 20', 'yyyy mm dd'), 8);
INSERT INTO Kitolt VALUES(4, 3, TO_DATE('2023 03 27', 'yyyy mm dd'), 8);

-- Készítések
INSERT INTO Keszit VALUES(1, 1, TO_DATE('2023 01 26', 'yyyy mm dd'));
INSERT INTO Keszit VALUES(2, 2, TO_DATE('2023 02 12', 'yyyy mm dd'));
INSERT INTO Keszit VALUES(3, 3, TO_DATE('2023 03 24', 'yyyy mm dd'));


create FUNCTION QuizKeres (
    p_kategoria IN VARCHAR2
)
    RETURN SYS_REFCURSOR
AS
    ret_cursor SYS_REFCURSOR;
BEGIN
    OPEN ret_cursor FOR SELECT QUIZ.QUIZ_NEV
                        FROM QUIZ
                                 INNER JOIN kategoria ON QUIZ.quiz_id = kategoria.quiz_id
                        WHERE kategoria.kategoria LIKE p_kategoria;
    RETURN ret_cursor;
END QuizKeres;
/

create FUNCTION REGISTER (
    p_email IN VARCHAR2,
    p_jelszo IN VARCHAR2,
    p_vezet IN VARCHAR2,
    p_kereszt IN VARCHAR2,
    p_eletkor IN NUMBER
)
    RETURN NUMBER
AS
    felh Felhasznalo%ROWTYPE;
    CURSOR c_felhasznalo IS
        SELECT *
        FROM Felhasznalo;
BEGIN
    OPEN c_felhasznalo;
    LOOP
        FETCH c_felhasznalo INTO felh;
        EXIT WHEN c_felhasznalo%NOTFOUND;
        IF felh.email = p_email THEN
            RETURN 0;
        end if;
    END LOOP;
    CLOSE c_felhasznalo;

    INSERT INTO Felhasznalo VALUES (seq_felhasznalo.nextval, p_email, p_jelszo, p_vezet, p_kereszt, p_eletkor, 0);

    RETURN 1;
END REGISTER;
/

create trigger QUIZ_DELETE
    before delete
    on QUIZ
    for each row
BEGIN
    DELETE FROM KESZIT WHERE KESZIT.QUIZ_ID = :OLD.QUIZ_ID;
    DELETE FROM KERDES WHERE KERDES.QUIZ_ID = :OLD.QUIZ_ID;
    DELETE FROM KITOLT WHERE KITOLT.QUIZ_ID = :OLD.QUIZ_ID;
END;
/

create trigger KERDES_DELETE
    before delete
    on KERDES
    for each row
BEGIN
    DELETE FROM ROSSZVALASZ WHERE ROSSZVALASZ.KERDES_ID = :OLD.KERDES_ID;
END;
/

