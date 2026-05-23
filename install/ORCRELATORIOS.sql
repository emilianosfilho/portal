-- Create table
create table ORCRELATORIOS
(
  idrelatorio         NUMBER(18),
  tipo                VARCHAR2(50),
  descricao           VARCHAR2(100),
  namefunction        VARCHAR2(100),
  dtultimaatualizacao DATE default sysdate,
  bloqueiadatas       VARCHAR2(1) default 'N'
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
