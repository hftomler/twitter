-- BASE DE DATOS TWITTER

-- USUARIOS:
drop table if exists usuarios cascade;
create table usuarios (
      id       bigserial   constraint pk_usuarios primary key,
      nick     varchar(15) not null constraint uq_usuarios_nick unique,
      password char(32)    not null constraint ck_password_valida
                           check (length(password) = 32));

-- FOLLOWERS

drop table if exists followers cascade;

create table followers (
      id_fr    bigint   not null    constraint fk_followers_usuarios_fr
                                    references usuarios(id) on delete cascade
                                    on update cascade,
      id_fd    bigint   not null    constraint fk_followers_usuarios_fd
                                    references usuarios(id) on delete cascade
                                    on update cascade,
      CONSTRAINT ck_fr_distinto_fd  CHECK (id_fr != id_fd));


-- TUITS:
drop table if exists tuits cascade;
create table tuits (
      id          bigserial    constraint pk_tuits primary key,
      usuario_id  bigint       not null constraint fk_tuits_usuarios
                               references usuarios (id) on delete cascade
                               on update cascade,
      from_id     bigint       constraint fk_retuit_usuarios
                               references usuarios (id) on delete set null
                               on update cascade,
      mensaje     varchar(140) not null constraint ck_tuits_mensaje
                               check (length(mensaje) <= 140),
      fecha       timestamp    not null default current_timestamp);