create table roles(
role_id int AUTO_INCREMENT primary key,
description text not null,
prod_id int not null,
role_count int DEFAULT 0
);


insert into roles(description, prod_id, role_count)
values('Impresario', 1, 0);

insert into roles(description, prod_id, role_count)
values('Snowflakes', 1, 8);

insert into roles(description, prod_id, role_count)
values('Clara', 1, 1);

insert into roles(description, prod_id, role_count)
values('Dance Lead #1', 2, 1);