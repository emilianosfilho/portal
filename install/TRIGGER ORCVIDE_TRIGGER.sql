CREATE OR REPLACE TRIGGER ORCVIDE_TRIGGER
AFTER INSERT or UPDATE or DELETE 
   ON ORCVIDE
   FOR EACH ROW

DECLARE
   v_IDLOGVIDE NUMBER(11);
   v_IDUSUARIO NUMBER(11);

BEGIN
  
   SELECT NVL(MAX(IDLOGVIDE),0)+1 AS IDLOGVIDE INTO v_IDLOGVIDE FROM ORCLOGVIDE;
   
   SELECT MIN(IDUSUARIO) AS IDUSUARIO INTO v_IDUSUARIO FROM ORCUSUARIO WHERE NOME = :new.USUARIOULTALTERACAO;
   
   IF DELETING THEN
       -- Insert record into audit table
       INSERT INTO ORCLOGVIDE
       ( TIPO,
         IDLOGVIDE,
         IDUSUARIO,
         USUARIO,
         CODPECA_OLD,
         CODPECA_NEW,
         APLICMARCA_OLD,
         APLICMARCA_NEW,
         VIDE_OLD,
         VIDE_NEW,
         DESCRICAO_OLD,
         DESCRICAO_NEW )
       VALUES
       ( 'DELETE',
         v_IDLOGVIDE,
         v_IDUSUARIO,
         NULL,
         :old.CODPECA,
         NULL,
         :old.APLICMARCA,
         NULL,
         :old.VIDE,
         NULL,
         :old.DESCRICAO,
         NULL ); 

   ELSIF INSERTING THEN
       -- Insert record into audit table
       INSERT INTO ORCLOGVIDE
       ( TIPO,
         IDLOGVIDE,
         IDUSUARIO,
         USUARIO,
         CODPECA_OLD,
         CODPECA_NEW,
         APLICMARCA_OLD,
         APLICMARCA_NEW,
         VIDE_OLD,
         VIDE_NEW,
         DESCRICAO_OLD,
         DESCRICAO_NEW )
       VALUES
       ( 'INSERT',
         v_IDLOGVIDE,
         v_IDUSUARIO,
         :new.USUARIOULTALTERACAO,
         NULL,
         :new.CODPECA,
         NULL,
         :new.APLICMARCA,
         NULL,
         :new.VIDE,
         NULL,
         :new.DESCRICAO );
         
   ELSIF UPDATING THEN
       
       -- Insert record into audit table
       INSERT INTO ORCLOGVIDE
       ( TIPO,
         IDLOGVIDE,
         IDUSUARIO,
         USUARIO,
         CODPECA_OLD,
         CODPECA_NEW,
         APLICMARCA_OLD,
         APLICMARCA_NEW,
         VIDE_OLD,
         VIDE_NEW,
         DESCRICAO_OLD,
         DESCRICAO_NEW )
       VALUES
       ( 'UPDATE',
         v_IDLOGVIDE,
         v_IDUSUARIO,
         :new.USUARIOULTALTERACAO,
         :old.CODPECA,
         :new.CODPECA,
         :old.APLICMARCA,
         :new.APLICMARCA,
         :old.VIDE,
         :new.VIDE,
         :old.DESCRICAO,
         :new.DESCRICAO ); 
       
       UPDATE ORCVIDE SET DTULTALTERACAO = SYSDATE WHERE IDLOGVIDE = v_IDLOGVIDE;
   
   END IF;
   
END;

/