-- Create table
create table OMGCHECKINC
(
  idcheckin        NUMBER(10) not null,
  numped           NUMBER(10) not null,
  numnota          NUMBER(10) not null,
  numtransent      NUMBER(10),
  dtmov            DATE not null,
  dtinicio         DATE default SYSDATE not null,
  dtfim            DATE,
  codfornec        NUMBER(10) not null,
  fornecedor       VARCHAR2(100),
  idusurconferente NUMBER(10) not null,
  divergente       VARCHAR2(1) default 'S',
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
