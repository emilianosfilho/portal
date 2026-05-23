-- Create table
create table OMGCANCELADOS
(
  idcancelados     VARCHAR2(40) not null,
  numped           NUMBER(10) not null,
  codprod          NUMBER(10) not null,
  dv               NUMBER(10),
  descricao        VARCHAR2(40),
  marca            VARCHAR2(20),
  locacao          VARCHAR2(20),
  datacanc         DATE not null,
  codusur          NUMBER(10),
  rca              VARCHAR2(60),
  codcli           NUMBER(10),
  cliente          VARCHAR2(60),
  cancelado_por    VARCHAR2(40),
  motivo           VARCHAR2(60),
  qt               NUMBER(10) default 0,
  idusurconferente NUMBER(10),
  dataconferencia  DATE,
  qtconferida      NUMBER(10) default 0,
  observacao       VARCHAR2(100)
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
