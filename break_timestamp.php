<?php
      include("config.php");

      $string = '';
      $sql = "select distinct l.empid as id, first_name, last_name
              from logs l
              left join employee e
              on e.empid = l.empid
              where timestamp like '".date("Y-m-d")."%'
              order by last_name asc";

      $result = sasql_query($connect, $sql);
       
      if (sasql_num_rows($result) > 0){
        while($row = sasql_fetch_object($result)){
            $string .= "<tr>";
            $string .= "<td class='empid'>".$row->id."</td>";
            $string .= "<td class='name'>".$row->last_name.", ".$row->first_name."</td>";

          $check = "select top 1 empid as id, tran_id, convert(varchar(5),timestamp, 101) + right(convert(varchar(32),timestamp, 100), 8) as timevar, timestamp, type, guard_remarks
            from logs
            where timestamp like '".date("Y-m-d")."%' and empid = ".$row->id."
            order by timestamp asc";  

          $emp_log = "select * from
            (
            select empid as id, tran_id, convert(varchar(5),timestamp, 101) + right(convert(varchar(32),timestamp, 100), 8) as timevar, timestamp, type, row_number() over (order by timestamp asc) row_num, count(*) over () as rows, guard_remarks
                        from logs
                        where timestamp like '".date("Y-m-d")."%' and empid = ".$row->id."
            ) tt
            where row_num != 1";

          $result_check = sasql_query($connect, $check);
          $result_log = sasql_query($connect, $emp_log);
          $row_count = sasql_num_rows($result_check);

          while($check_row = sasql_fetch_object($result_check)){
            $first_type = $check_row->type;
            
            if ($first_type == 1){
              $string .= "<td class='status'>In</td>";
              $string .= "<td class='action'><input type='button' value='Out' id='type_button' class='btn btn-danger btn-sm' style='margin-left:18px;'></input></td>";
              $string .= "<td class='guard_remarks'><select id='guard_input'><option value=''></option><option value='Official Business'>Official Business</option><<option value='2Quad Office'>2Quad Office</option><option value='JY Office'>JY Office</option><option value='No ID'>No ID</option><option value='Error in Biometrics'>Error in Biometrics</option></select></td>";

              while($log_row = sasql_fetch_object($result_log)){
                
              } 
            }
          }
        }
      }else{
        $string = "No result!";
      } 
       
      echo $string;
      sasql_close($connect);
    ?>