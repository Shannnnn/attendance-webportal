<?php
$sql = "CREATE OR REPLACE PROCEDURE @pr_log_reports(@current_date date)
BEGIN 
  DECLARE @counter INT;
  DECLARE @maxlogs INT;
  DECLARE @srid int; 
  DECLARE @srid_emp int;
  DECLARE @entry_type varchar(3);
  DECLARE @empid varchar(4);
  DECLARE @prev_date date;
  DECLARE @next_date date;
  DECLARE @prev_datetime varchar(16);
  DECLARE @next_datetime varchar(16);

  SET @empid = 0;
  SET @counter = 1;
  SET @prev_date = DATEADD(day, -1, @current_date);
  SET @next_date = DATEADD(day, 1, @current_date);
  SET @prev_datetime = cast(@prev_date as varchar(10)) + ' 23:59';
  SET @next_datetime = cast(@next_date as varchar(10)) + ' 23:59';
   
  Select  b.last_name, b.first_name, b.empid, a.timestamp, Date(a.timestamp) as date_entry,
          (Case a.type when 1 then 'IN' when 2 then 'Out' End) as entry_type, 
          a.guard_remarks, convert(numeric(8,2), 0) as time_elapsed, 
          left(convert(varchar, timestamp, 101), 5) as date_stamp,
          right(convert(varchar, timestamp, 100), 7) as time_stamp,
          convert(numeric(15,2), 0) as milliseconds,
          convert(varchar(11), 0) as shift_dur,
          convert(integer, 0) as first_in, //for first_in 
          convert(integer, 0) as final_out, //for final_out 
          number() as srid, 
          convert(integer, 1) as srid_emp  
  into    #t_logs 
  from    dba.logs a,
          dba.employee b
  Where   a.empid = b.empid and
          timestamp >= @prev_date and
          timestamp <= @next_datetime and 
          a.active = 1
  order by last_name, first_name, a.timestamp; 

  Update #t_logs a, #t_logs b 
  set a.srid_emp = a.srid_emp + b.srid_emp        
  Where   a.empid = b.empid and
          a.srid = (b.srid + 1);

  Update #t_logs a, #t_logs b 
  set b.time_elapsed = IsNull(DateDiff(ss, a.timestamp, b.timestamp)/60,0), b.milliseconds = IsNull(DateDiff(ms, a.timestamp, b.timestamp)/1000,0)       
  Where   a.empid = b.empid and
          a.srid = (b.srid - 1);

  Select empid, srid, srid_emp, entry_type
  into #t_prev_eighthours 
  from #t_logs 
  where time_elapsed >= (60 * 7) and
          timestamp >= @prev_date and
          timestamp <= @prev_datetime;

  Delete  #t_logs
  From     #t_logs a,
           #t_prev_eighthours b
  Where   a.empid = b.empid and
         a.srid <= b.srid; 

  Select empid, srid, srid_emp, entry_type
  into    #t_eighthours 
  from #t_logs 
  where time_elapsed >= (60 * 7);

  Select a.empid, min(a.srid) as srid, min(a.srid_emp) as srid_emp, b.timestamp
  into    #t_min
  From #t_eighthours a,
       #t_logs b 
  where a.empid = b.empid
  Group by a.empid, b.timestamp
  having b.timestamp >= @prev_date and
        b.timestamp <= @prev_datetime;

  Select a.empid, a.srid, a.srid_emp
  into    #t_all
  From #t_eighthours a,
       #t_logs b 
  Where a.empid = b.empid and
        a.entry_type = 'IN'
  Group by a.empid, a.srid, a.srid_emp, b.timestamp
  having b.timestamp >= @prev_date and
        b.timestamp <= @prev_datetime;

  Delete  #t_all
  From     #t_all a,
           #t_min b
  Where   a.empid = b.empid and
         a.srid = b.srid and
         a.srid_emp = b.srid_emp; 

  Select empid, min(srid) as srid, min(srid_emp) as srid_emp 
  into    #t_min_next
  From #t_all
  Group by empid;

  Delete  #t_logs
  from    #t_logs a, 
          #t_min  b
  Where   a.empid = b.empid and
         a.srid < b.srid and
         a.srid_emp < b.srid_emp;

  Delete  #t_logs
  from    #t_logs a, 
          #t_min_next  b
  Where   a.empid = b.empid and
         a.srid >= b.srid and
         a.srid_emp >= b.srid_emp;

  Update #t_logs set srid_emp = 1;

  Update #t_logs a, #t_logs b 
  set a.srid_emp = a.srid_emp + b.srid_emp        
  Where   a.empid = b.empid and
          a.srid = (b.srid + 1);

  Delete  #t_logs
  Where   srid_emp = 1 and
          date_entry = @prev_date;

  Select * 
  into #t_delete
  from  #t_logs
  Group by last_name, first_name, empid, timestamp, date_entry, entry_type, guard_remarks, time_elapsed, srid, srid_emp, milliseconds, final_out, first_in, shift_dur, date_stamp, time_stamp
  Having  date_entry <> @current_date;

  Delete  #t_logs
  from    #t_logs a, 
          #t_delete  b
  Where   a.empid = b.empid and
         a.srid = b.srid and
         a.srid_emp = b.srid_emp;

  Update #t_logs set srid_emp = 1;

  Update #t_logs a, #t_logs b 
  set a.srid_emp = a.srid_emp + b.srid_emp        
  Where   a.empid = b.empid and
          a.srid = (b.srid + 1);

  Create table #t_emp_log (
    empid varchar(10), 
    last_name varchar(150),
    first_name varchar(150),
    in_1 varchar(13) Default Null,
    out_1 varchar(13) Default Null,
    in_2 varchar(13) Default Null,
    out_2 varchar(13) Default Null,
    in_3 varchar(13) Default Null,
    out_3 varchar(13) Default Null,
    in_4 varchar(13) Default Null,
    out_4 varchar(13) Default Null,
    in_5 varchar(13) Default Null,
    out_5 varchar(13) Default Null,
    in_6 varchar(13) Default Null,
    out_6 varchar(13) Default Null,
    in_7 varchar(13) Default Null,
    out_7 varchar(13) Default Null,
    in_8 varchar(13) Default Null,
    out_8 varchar(13) Default Null,
    shift_dur varchar(11) Default Null,
    break_dur varchar(12) Default Null,
    ob_mins varchar(12) Default Null,
    late_mins varchar(12) Default Null,
    undertime varchar(12) Default Null,
    status varchar(12) Default Null
  );

  Insert into  #t_emp_log (empid, last_name, first_name)
  Select empid, last_name, first_name
  From employee
  Order by last_name, first_name; 

  SET @maxlogs = (select max(srid) from #t_logs);

  WHILE @counter <= @maxlogs LOOP
     SELECT empid, entry_type, srid, srid_emp 
     into @empid, @entry_type, @srid, @srid_emp
     FROM #t_logs WHERE srid = @counter;
   
     IF (@entry_type = 'IN' and Mod(@srid_emp, 2) = 0) or (@entry_type = 'Out' and Mod(@srid_emp, 2) > 0) THEN
        Update #t_logs set srid_emp = @srid_emp + 1 
        Where  empid = @empid and
               srid_emp = @srid_emp and
               entry_type = @entry_type;
     END IF;
     
     SET @counter  = @counter  + 1;
  END LOOP;

  Update #t_logs a, #t_logs b 
  set b.time_elapsed = 0, b.milliseconds = 0       
  Where   a.empid = b.empid and
          a.srid <> (b.srid - 1);

  Update #t_logs a, #t_logs b 
  set b.time_elapsed = IsNull(DateDiff(second, a.timestamp, b.timestamp)/60,0),
  b.milliseconds = IsNull(DateDiff(ms, a.timestamp, b.timestamp)/1000,0)       
  Where   a.empid = b.empid and
          a.srid = (b.srid - 1);

  Update #t_logs set first_in = case when entry_type = 'IN' and time_elapsed = 0 then 1 else 0 end;
  Update #t_logs a, #t_logs b set a.first_in = 2 where b.first_in = 1 and b.srid_emp = a.srid_emp + 1;
  Update #t_logs set final_out = case when entry_type = 'Out' and time_elapsed >= (60 * 7) then 2 else 0 end;

  Update #t_emp_log set in_1 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 1 and
          b.entry_type = 'IN';

  Update #t_emp_log set out_1 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 2 and
          b.entry_type = 'Out';

  Update #t_emp_log set in_2 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 3 and
          b.entry_type = 'IN';

  Update #t_emp_log set out_2 = b.date_stamp +' '+ b.time_stamp  
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 4 and
          b.entry_type = 'Out';

  Update #t_emp_log set in_3 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 5 and
          b.entry_type = 'IN';

  Update #t_emp_log set out_3 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 6 and
          b.entry_type = 'Out';

  Update #t_emp_log set in_4 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 7 and
          b.entry_type = 'IN';

  Update #t_emp_log set out_4 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 8 and
          b.entry_type = 'Out';

  Update #t_emp_log set in_5 = b.date_stamp +' '+ b.time_stamp  
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 9 and
          b.entry_type = 'IN';

  Update #t_emp_log set out_5 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 10 and
          b.entry_type = 'Out';

  Update #t_emp_log set in_6 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 11 and
          b.entry_type = 'IN';

  Update #t_emp_log set out_6 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 12 and
          b.entry_type = 'Out';

  Update #t_emp_log set in_7 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 13 and
          b.entry_type = 'IN';

  Update #t_emp_log set out_7 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 14 and
          b.entry_type = 'Out';

  Update #t_emp_log set in_8 = b.date_stamp +' '+ b.time_stamp 
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 15 and
          b.entry_type = 'IN';

  Update #t_emp_log set out_8 = b.date_stamp +' '+ b.time_stamp  
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid and
          b.srid_emp = 16 and
          b.entry_type = 'Out';

  select empid, timestamp, first_in
  into #t_first_in 
  from #t_logs
  where first_in = 1;

  select empid, timestamp, final_out 
  into #t_final_out 
  from #t_logs
  where final_out = 2;

  Update #t_logs a, #t_first_in c
  set a.shift_dur = case when a.time_elapsed < (60 * 7) then convert(varchar(5), DateDiff(ss, c.timestamp, dateadd(hour, 15, now()))/3600)+':'+convert(varchar(2), DateDiff(ss, c.timestamp, dateadd(hour, 15, now()))%3600/60)+':'+convert(varchar(5),(DateDiff(ss, c.timestamp, dateadd(hour, 15, now()))%60))  
  else 'Missing IN!' end
  where a.empid = c.empid AND 
        a.first_in = 1;

  Update #t_logs a, #t_final_out b, #t_first_in c
  set a.shift_dur = case when a.time_elapsed < (60 * 7) then convert(varchar(5), DateDiff(ss, a.timestamp, dateadd(hour, 15, now()))/3600)+':'+convert(varchar(2), DateDiff(ss, a.timestamp, dateadd(hour, 15, now()))%3600/60)+':'+convert(varchar(5),(DateDiff(ss, a.timestamp, dateadd(hour, 15, now()))%60))  
  when a.time_elapsed >= (60 * 7) then convert(varchar(5), DateDiff(ss, c.timestamp, b.timestamp)/3600)+':'+convert(varchar(2), DateDiff(ss, c.timestamp, b.timestamp)%3600/60)+':'+convert(varchar(5),(DateDiff(ss, c.timestamp, b.timestamp)%60))
  else 'Missing IN!' end
  where a.empid = c.empid AND 
        a.empid = b.empid;

  Update #t_emp_log set a.shift_dur = case when b.first_in = 1 then b.shift_dur else 'Missing IN!' end
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid;

  Update #t_emp_log set a.shift_dur = b.shift_dur
  from    #t_emp_log a,
          #t_logs b 
  Where   a.empid = b.empid AND 
          b.final_out = 2;

  select empid, last_name, first_name, sum(milliseconds)as sum
  into #t_breaks
  from #t_logs
  group by empid, last_name, first_name, final_out, first_in
  having first_in = 0 and final_out = 0;

  Update #t_emp_log set a.break_dur = convert(varchar, CONVERT(timestamp, DATEADD(ss, b.sum, '00:00:00')), 108)
  from    #t_emp_log a,
          #t_breaks b 
  Where   a.empid = b.empid;

  Update #t_emp_log set ob_mins = convert(varchar, CONVERT(timestamp, DATEADD(hour, -1, break_dur)), 108)
  from  #t_emp_log
  Where break_dur > '01:00:00';


  Select empid AS [Employee ID], last_name +', '+ first_name AS [Employee Name], isnull(in_1, '') AS [Time In], 
       isnull(out_1, '') AS [Time Out], isnull(out_2, '') AS [Out 1], isnull(in_2, '') AS [In 1], isnull(out_3, '') AS [Out 2], isnull(in_3, '') AS [In 2],  
       isnull(out_4, '') AS [Out 3], isnull(in_4, '') AS [In 3], isnull(out_5, '') AS [Out 4], isnull(in_5, '') AS [In 4], isnull(out_6, '') AS [Out 5], 
       isnull(in_6, '') AS [In 5], isnull(out_7, '') AS [Out 6], isnull(in_7, '') AS [In 6], isnull(out_8, '') AS [Out 7], isnull(in_8, '') AS [In 7],  
       isnull(shift_dur, '') AS [Shift Duration], isnull(break_dur, '') AS [Break Duration], isnull(ob_mins, '') AS [OB Mins], 
       isnull(late_mins, '') AS [Late Mins], isnull(undertime, '') AS [Undertime], isnull(status, '') AS [Status] from #t_emp_log

END;
"
?>