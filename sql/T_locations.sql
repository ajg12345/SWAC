create table locations(
room text not null,
building text not null,
location_id int AUTO_INCREMENT primary key
);


insert into locations(room, building)
values('Main Stage', 'Auditorium Theatre');

insert into locations(room, building)
values('Studio A', 'Joffrey Tower');
