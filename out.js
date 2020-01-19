<script type="text/javascript">
      var i = -1;
      var x = -1;
      var zero = 0;

      function change_rowspan(row){
        document.getElementsByTagName("th")[17 + (row - 1) + row].setAttribute("rowspan", 2); 
        document.getElementsByTagName("th")[18 + (row - 1) + row].setAttribute("rowspan", 2);
        //console.log(row); 
      }

      function out_time(data){
        //console.log(data);
        if (data == 1){
          if (i != -1 && zero != 0 || i != 0 && zero != 0 ){
            zero--;
            i = i + zero;
            zero = 0;
          }
          i++;
          x++;
          var hidden_elements = document.getElementsByClassName('hidden_out_time');
          var elements = document.getElementsByClassName('out_time');

          elements[i].innerHTML = hidden_elements[x].getAttribute('name');
          //elements[i].innerHTML = elements.length;
          elements[i].setAttribute('value', hidden_elements[x].getAttribute('value'));
          elements[i].setAttribute('id', hidden_elements[x].getAttribute('id'));
          elements[i].setAttribute('name', 2);
          elements[i].style.backgroundColor = "transparent";
          //console.log(elements.length);
          //console.log(i);
        } else if (data == 2){
          i = i + 1;
        } else if (data == 0){
          zero++;
        }
      }       
    </script>