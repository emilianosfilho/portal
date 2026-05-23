PROCEDURE PRC_CANCELA (P_NUMPED   IN PCPEDC.NUMPED%TYPE,
                       P_CODFUNC  IN PCEMPR.MATRICULA%TYPE,
                       P_MOTIVO   IN VARCHAR2,
                       P_CODMOTIVO IN NUMBER,
                       P_MENSAGEM OUT VARCHAR2, 
                       P_CANCELADO OUT VARCHAR2) 