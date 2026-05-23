-- Create table
create table OMGCHECKINI
(
  idcheckin        NUMBER(10) not null,
  idcheckini       NUMBER(10) not null,
  codprod          NUMBER(10) not null,
  dv               NUMBER(10) not null,
  numoriginal      VARCHAR2(20),
  descricao        VARCHAR2(40),
  marca            VARCHAR2(20),
  locacao          VARCHAR2(20),
  qtpedida         NUMBER(10) default 0,
  qtconferida      NUMBER(10) default 0,
  idusurconferente NUMBER(10),
  numnota          NUMBER(10),
  numtransent      NUMBER(10),
  observacao       VARCHAR2(100),
  dataconferencia  DATE default SYSDATE,
  numped           NUMBER(10),
  numseqped        NUMBER(11)
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
