create table dancers(
id int AUTO_INCREMENT primary key,
dancer_first text not null,
dancer_last text not null,
dancer_email text not null
);

insert into dancers(dancer_first, dancer_last, dancer_email)
values('Fabrice', 'Calmels', 'fcalmels@joffrey.org');

