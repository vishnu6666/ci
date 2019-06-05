<script>
  $('#submit').click(function(){
    var fd = new FormData();
    var formData = new FormData($('#createform')[0]);
    var files = $('#file')[0].files[0];
    fd.append('file',files);
    var ext = $('#file').val().split('.').pop().toLowerCase();
    if($('#file').val() == ''){  
      alert("Please Select the File.");  
    }else if($.inArray(ext, ['wav','mp3']) == -1) {
      alert('Invalid Extension!!! ,Only wav file alowed.');
    }else{ 
        $.ajax({
          url:'<?php echo base_url();?>freeswitch/callertune_save/', 
          type: 'post',
          data: formData,
          contentType: false,
          processData: false,
         success: function(){ 
           $(document).trigger('close.facebox');
            location.reload(true);
         },
       });
      }
    });
  </script>


<form action="" id="createform" method="POST" accept-charset="utf-8" enctype="multipart/form-data">

<input type="file" name="file" value="" class="col-md-5 form-control" id="file">
  
<button name="action" type="button" value="save" id="submit" class="btn btn-line-parrot">Save</button>
