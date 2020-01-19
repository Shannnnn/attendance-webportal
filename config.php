<?php
  $connect = sasql_connect( "UID=dba;PWD=1m2p3k4n;Server=ips_dems;DBN=ips;" );
  //$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ims_ics;DBN=IMS;CommLinks=all");
  //$connect = sasql_connect("UID=dba;PWD=sql;Server=attendance_testing;DBN=testing;CommLinks=all");
  //$connect = sasql_connect("UID=dba;PWD=sql;Server=testing;DBN=attendance_tracker;CommLinks=all");
  //$connect = sasql_connect("UID=dba;PWD=1m2p3k4n;Server=ics_cebu;DBN=IMS;CommLinks=all");
  //sasql_query( $connect, "SET TEMPORARY OPTION connection_authentication = NULL;" );
  //sasql_query( $connect, "SET OPTION PUBLIC.database_authentication = NULL;" );
  //sasql_query( $connect, "SET TEMPORARY OPTION CONNECTION_AUTHENTICATION='Company=Meditab Software India Pvt Ltd;Application=IMS;Signature=000fa55157edb8e14d818eb4fe3db41447146f1571g6d24262b3c52e40043334f88f15f65d5ab2178e9'" );
  sasql_query( $connect, "set option string_rtruncation = off;");
  //sasql_query($connect, $sql)
  //sasql_result_all( $result );
  //sasql_free_result( $result );
  //sasql_disconnect( $conn );
?>.