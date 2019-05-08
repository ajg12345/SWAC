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
values(1, '2019-12-25', 1, 2, '19:30:00', '22:00:00');

insert into rehearsals(prod_id, perf_dt, is_performance, location_id, start_time, end_time)
values(1, '2019-12-2', 0, 1, '11:30:00', '13:00:00');

insert into rehearsals(prod_id, perf_dt, is_performance, location_id, start_time, end_time)
values(2, '2019-5-1', 1, 2, '19:30:00', '22:00:00');