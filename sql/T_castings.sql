create table castings(
casting_id int AUTO_INCREMENT primary key,
re_id int,
role_id int,
dancer_id int,
update_dt datetime
);


insert into castings(re_id, role_id, dancer_id, update_dt)
values(1,1,1, NOW());

insert into castings(re_id, role_id, dancer_id, update_dt)
values(1,2,2, NOW());


