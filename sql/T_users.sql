create table users(
user_uid int AUTO_INCREMENT primary key,
user_fullname text not null,
user_email text not null,
user_pwd text not null,
can_create int DEFAULT 0
);

insert into users(user_first, user_last, user_email, user_pwd, can_create)
values('Aaron', 'Glynn', 'aglynn@joffrey.org', '123', 1);

insert into users(user_first, user_last, user_email, user_pwd, can_create)
values('Paul', 'Key', 'pkey@joffrey.org', '123', 0);