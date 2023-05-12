create table if not exists public."user"
(
    id        varchar(80)  not null
        primary key
        unique,
    firstname varchar(64)  not null,
    lastname  varchar(64)  not null,
    password  varchar(64)  not null,
    email     varchar(128) not null,
    phone     varchar(32)  not null,
    verified  integer default 0
);

alter table public."user"
    owner to living_test_user;

create table if not exists public.signup_mail
(
    url       varchar(512) not null
        constraint signup_mail_2_pkey
            primary key,
    usr_id    varchar(80)  not null
        constraint signup_mail_user_id_fk
            references public."user",
    timestamp timestamp default now()
);

alter table public.signup_mail
    owner to living_test_user;

