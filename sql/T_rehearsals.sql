create table rehearsals(
re_id int AUTO_INCREMENT primary key,
prod_id int,
perf_dt date,
is_performance int,
location_id int,
start_time time,
end_time time
);


insert into rehearsals(prod_id, perf_dt, is_performance, location_id, start_time, end_time)
values(1, '2019-12-25', 1, 1, '19:30:00', '22:00:00');
