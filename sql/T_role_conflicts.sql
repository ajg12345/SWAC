create table role_conflicts(
conflict_id int AUTO_INCREMENT primary key,
prod_id int not null,
conflict_pair_id int not null,
role_id1 int not null,
role_id2 int not null
);


insert into role_conflicts(prod_id, role_id1, role_id2, conflict_pair_id)
values(1, 1, 2, 1);

insert into role_conflicts(prod_id, role_id1, role_id2, conflict_pair_id)
values(1, 2, 1, 1);
