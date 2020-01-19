 '".date("Y-m-d")."%'

 table {
          position: relative;
          width: 150%;
        }
        .fix {
          position: absolute;
          *position: relative; /*ie7*/
          margin-left: -500px;
          width: 100px;
        }
        .outer {position:relative;}
        .inner {
          overflow-x:scroll;
          overflow-y:visible;
          width: 150%; 
          margin-left: 500px;
        }



        table thead tr{
        display:block;
      }.fix{
        width:150px;
      }table td{
        width:150px;
      }
      table tbody{
        display:block;
        height:700px;
        overflow-x:scroll;
        overflow-y:scroll;
      }



      thead {
          display: table;
          table-layout:fixed;
          width: 100%;
      }
      tbody {
          display: block;
          height: 100em;
          overflow-y: scroll;
      }
      tbody tr {
          display: table;
          table-layout:fixed;
          width: 100%;
      }
      th, td {
          width: auto;
      }


       //$('#report_table tbody').on('click', 'td', function () {     
        //  alert('ColumnIndex:'+ $(this).parent().find('td').index(this));
        //  alert('RowIndex:'+ $(this).parent().parent().find('tr').index($(this).parent()));
        //});
         
        //for(var i = 1; i < table.rows.length; i++){
        //  table.rows[i].onclick = function(){
        //      rIndex = this.rowIndex;
              //console.log(rIndex);
              //alert('Row ' + $(this).closest("tr").index());
              //alert('Column ' + $(this).closest("td").index());
              //console.log(this.cells[0].innerHTML);
              //console.log(this.cells[1].innerHTML);
              //console.log(this.cells[2].innerHTML);
        //    };
        //}

        //$(function () {
        //    $("#report_table td").dblclick(function () {
        //        newInput(this);

                //for(var i = 1; i < table.rows.length; i++){
                //  table.rows[i].onclick = function(){
                //    //rIndex = this.rowIndex;
                //    console.log(this.cells[0].innerHTML);
                //    console.log(this.cells[1].innerHTML);
                //    console.log(this.cells[2].innerHTML);
                //  };
                //}
        //    });
        //});

        function closeInput(elm) {
            var value = $(elm).find('input').val();
            $(elm).empty().text(value);

            $(elm).bind("dblclick", function () {
                newInput(elm);
            });
        }  
                
        function newInput(elm) {
            $(elm).unbind('dblclick');

            var value = $(elm).text();
            $(elm).empty();
            
            $("<input>")
                .attr('type', 'text')
                .val(value)
                .blur(function() {
                    closeInput(elm);
                })
                .appendTo($(elm))
                .focus();
        }


        #myInput {
        width: 100%; /* Full-width */
        font-size: 16px; /* Increase font-size */
        padding: 12px 20px 12px 40px; /* Add some padding */
        border: 1px solid #ddd; /* Add a grey border */
        margin-bottom: 12px; /* Add some space below the input */
      }#report_table{
        box-sizing: border-box
      }#report_table thead {
          display:table;
          table-layout:fixed;
          width: 100%;
      }
      #report_table tbody{
        display:block;
        height:710px;
        width: 100.6%;
        overflow-x:scroll;
        overflow-y:scroll;
      }
      #report_table tbody tr {
          display: table;
          table-layout:fixed;
          width: 100%;
      }



      #myInput {
        width: 100%; /* Full-width */
        font-size: 16px; /* Increase font-size */
        padding: 12px 20px 12px 40px; /* Add some padding */
        border: 1px solid #ddd; /* Add a grey border */
        margin-bottom: 12px; /* Add some space below the input */
      }.table-scroll {
        position:relative;
        max-width:1900px;
        margin:auto;
        overflow:hidden;
        border:1px solid #000;
      }
      .table-wrap {
        width:150%;
        overflow:auto;
      }
      .table-scroll table {
        width:150%;
        margin:auto;
        border-collapse:separate;
        border-spacing:0;
      }
      .table-scroll th, .table-scroll td {
        padding:12px 12px;
        border:1px solid #000;
        background:#fff;
        white-space:nowrap;
        vertical-align:top;
      }
      .table-scroll thead, .table-scroll tfoot {
        background:#f9f9f9;
      }
      .clone {
        position:absolute;
        top:0;
        left:0;
        pointer-events:none;
      }
      .clone th, .clone td {
        visibility:hidden
      }
      .clone td, .clone th {
        border-color:transparent
      }
      .clone tbody th {
        visibility:visible;
      }
      .clone .fix {
        border:1px solid #000;
        background:#eee;
        visibility:visible;
      }
      .clone thead, .clone tfoot{background:transparent;}

       jQuery(document).ready(function() {
       jQuery("#report_table").clone(true).appendTo('#table-scroll').addClass('clone');   
      });