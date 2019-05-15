create table locations(
room text not null,
building text not null,
location_id int AUTO_INCREMENT primary key,
is_active int default 1
);

--These locations are very important and crucial to the layout of the printed schedule, but they can be edited and renamed
insert into locations(room, building)
values('Main Stage', 'Auditorium Theatre');

insert into locations(room, building)
values('Studio A', 'Joffrey Tower');

insert into locations(room, building)
values('Studio B', 'Joffrey Tower');

insert into locations(room, building)
values('Studio C', 'Joffrey Tower');
