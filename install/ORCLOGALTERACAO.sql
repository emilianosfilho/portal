-- Create table
create table ORCLOGALTERACAO
(
  idlog    NUMBER(10),
  data     DATE default (sysdate),
  usuario  VARCHAR2(50),
  operacao VARCHAR2(50),
  tabela   VARCHAR2(50),
  registro VARCHAR2(400)
)
tablespace TS_DADOS
  pctfree 10
  initrans 1
  maxtrans 255
  storage
  (
    initial 64K
    next 1M
    minextents 1
    maxextents unlimited
  );
