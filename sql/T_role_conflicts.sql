create table role_conflicts(
prod_id int not null,
role_id1 int not null,
role_id2 int not null
);


insert into role_conflicts(prod_id, role_id1, role_id2)
values(1, 1, 2);
