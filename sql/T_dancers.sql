create table dancers(
dancer_id int AUTO_INCREMENT primary key,
dancer_fullname text not null,
dancer_email text not null,
dancer_phone text not null
);

insert into dancers(dancer_fullname, dancer_phone, dancer_email)
values('Fabrice Calmels', '(123) 123-4567', 'fcalmels@joffrey.org');

