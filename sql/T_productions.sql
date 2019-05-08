create table productions(
prod_id int AUTO_INCREMENT primary key,
description text not null,
create_dt datetime
);

insert into productions(description, create_dt)
values('The Nutcracker 2019',  NOW());

insert into productions(description, create_dt)
values('Across The Pond',  '5/24/2019');