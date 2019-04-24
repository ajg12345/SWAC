create table users(
user_uid int AUTO_INCREMENT primary key,
user_first text not null,
user_last text not null,
user_email text not null,
user_pwd text not null,
can_create int DEFAULT 0
);
