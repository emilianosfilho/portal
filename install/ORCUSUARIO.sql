-- Create table
create table ORCUSUARIO
(
  idusuario  NUMBER(10) not null,
  nome       VARCHAR2(100) not null,
  email      VARCHAR2(50),
  senha      VARCHAR2(32),
  perfil     VARCHAR2(15),
  avatar     VARCHAR2(15),
  dtcadastro DATE default SYSDATE,
  dtexclusao DATE,
  status     VARCHAR2(1),
  codcli     NUMBER(10),
  codusur    NUMBER(10),
  matricula  NUMBER(10),
  dtultlogin DATE
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
-- Create/Recreate primary, unique and foreign key constraints 
alter table ORCUSUARIO
  add constraint ORCUSUARIO_PK primary key (IDUSUARIO)
  using index 
  tablespace TS_DADOS
  pctfree 10
  initrans 2
  maxtrans 255
  storage
  (
    initial 64K
    next 1M
    minextents 1
    maxextents unlimited
  );
-- Create/Recreate check constraints 
alter table ORCUSUARIO
  add check (STATUS IN ('A', 'I'));
alter table ORCUSUARIO
  add check (PERFIL IN ('ADMINISTRADOR', 'COLABORADOR', 'VENDEDOR', 'LOGISTICA', 'CLIENTE'));
