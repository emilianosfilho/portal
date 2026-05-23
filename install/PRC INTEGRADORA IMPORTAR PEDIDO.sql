 PROCEDURE importarpedido
  (
    p_tipoleitura IN NUMBER DEFAULT 1,
    p_datainicial IN DATE DEFAULT TRUNC(SYSDATE),
    p_datafinal   IN DATE DEFAULT TRUNC(SYSDATE),
    p_codfilial   IN VARCHAR2 DEFAULT '99',
    p_tiporeg     IN NUMBER DEFAULT NULL,
    p_codusur     IN NUMBER DEFAULT NULL,
    p_numpedrca   IN NUMBER DEFAULT NULL
  )