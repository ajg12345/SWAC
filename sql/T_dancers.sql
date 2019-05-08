create table dancers(
dancer_id int AUTO_INCREMENT primary key,
dancer_fullname text not null,
dancer_email text not null,
dancer_phone text not null,
dancer_email_or_phone text not null,
is_active int not null default 1
);

insert into dancers(dancer_fullname, dancer_phone, dancer_email, dancer_email_or_phone)
values('Fabrice Calmels', '(123) 123-4567', 'fcalmels@joffrey.org', 'email');

insert into dancers(dancer_fullname, dancer_phone, dancer_email, dancer_email_or_phone)
values('Timor Suluvashili', '(123) 123-4567', 'tsuluvashili@joffrey.org', 'phone');

insert into dancers(dancer_fullname, dancer_phone, dancer_email, dancer_email_or_phone)
values('April Daly', '(123) 123-4567', 'adaly@joffrey.org', 'email');

