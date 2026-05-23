-- Create table
create table ORCRELATORIOSFILTROS
(
  idrelatoriofiltro NUMBER(18),
  idrelatorio       NUMBER(18),
  campo             VARCHAR2(50),
  tipo              VARCHAR2(50),
  funcao            VARCHAR2(200)
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
