/*execute javascript after the dom is loaded*/
$(document).ready(function(){

   fetch_data();
   function fetch_data()
   {
      var action = "fetch";
      $.ajax({
           url:"src/IMG_Upload.php",
           method:"POST",
           data:{action:action},
           success:function(res)
           {
            let data = $.parseJSON(res);
            $('#image_data').html(data.result);
           }
      });
   }

   /*Hide error when form modal close*/
   $('button#close').click(function(){
      $('#error').css('display', 'none');
   });

   /*Trigger the modal*/
   $('#add').click(function(){
         //open the modal(pop up) perhaps likewise .modal("show"). 
         $('#imageModal').modal();
         //reset the form in case data exist
         $('#image_form')[0].reset();
         //append 'Add Image' to the class="modal-title"
         $('.modal-title').text("Add Image");
         $('#image_id').val("");
         $('#action').val("insert");
         $('#insert').val("Insert");
   });

   /*Trigger update modal*/
   $(document).on('click', '.update', function(){
       $('#imageModal').modal("show");
       $('#image_form')[0].reset();
       $('.modal-title').text("Update Image");
       $('#image_id').val($(this).attr("id"));
       $('#action').val("update");
       $('#insert').val("Update");
   });

   //Handle submission of data
   $('#image_form').submit(function(event){
        var uImage = $('#image').val();
        var imageExtension = uImage.split('.').pop().toLowerCase();
        // console.log(uImage, imageExtension);
        event.preventDefault();
        if(uImage == false)
          {
            $('#error').css('display', 'block');
            $('#error').html("Please Select Image").css('color', 'red');
            return false;
          } 
        else
          {
              $('#error').css('display', 'none');
              //Check if extension equal to false(or -1) and display status.
              if(jQuery.inArray(imageExtension, ['gif', 'png', 'jpg', 'jpeg']) == -1)
              {
                 $('#error').css('display', 'block');
                 $('#error').html("Invalid Image File").css('color', 'red');
                 $('#image').val('');
                 return false;
              }
              else
              {
                 $.ajax({
                     url: "src/IMG_Upload.php",
                     method: "POST",
                     data: new FormData(this),
                     contentType: false,
                     processData: false,
                     success: function(data)
                     {
                      $('#image_form')[0].reset();
                      $('#imageModal').modal('hide');
                       data = $.parseJSON(data);
                       if(data)
                        {
                           swal({
                                title: "",
                                text: data.text,
                                type: data.type,
                                showCancelButton: false,
                                confirmButtonClass: data.confirmButtonClass,
                                confirmButtonText: data.confirmButtonText,
                                closeOnConfirm: true
                              });
                        }
                       fetch_data();
                     }
                 });
              }
          }
   });


  /*Handle deletion of data*/
  $(document).on('click', '.delete', function(){
       var image_id = $(this).attr("id");
       var action = "delete";
       swal({
              title: "Are you sure?",
              text: "You will not be able to recover this image file!",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-danger",
              confirmButtonText: "Yes, delete it!",
              cancelButtonText: "No, cancel please!",
              closeOnConfirm: false,
              closeOnCancel: false
            },
            function(isConfirm) {
              if (isConfirm) 
              {
                   $.ajax({
                   url: "src/IMG_Upload.php",
                   method: "POST",
                   data: {image_id:image_id, action:action},
                   success:function(data)
                   {
                    data = $.parseJSON(data);
                    if (data) 
                    {
                      swal("Deleted!", data.text, data.type);
                    }
                    fetch_data();
                   }
                });
              } 
              else 
              {
                swal("Cancelled", "Your image file is safe.", "error");
              }
          }); 
   });

  /*function that identify if the checkbox is checked(1) 
    otherwise (0) and transfer the values to the server side
    through ajax*/
  $(document).on('click', 'input[type=checkbox]', function(){
        var id = $(this).attr("value");
        let visible;
        if($(this).prop("checked") == true)
          {
            visible = 1;
          }
          else
          {
            visible = 0;
          }
          let data = {
            "action": "checkbox",
            "id": id,
            "visible": visible
          };
          $.ajax({
            type: "post",
            url: "src/IMG_Upload.php",
            data: data,
            success: function(data){
              data = $.parseJSON(data);
              console.log(data);
            } 
          });
     });

  //*Handle deletion of multiple data*/
  $(document).on('click', '#delete_multiple_data', function(){
       var action = "delete_multiple_data";
       let visible;
       if($("input[type=checkbox]").prop("checked"))
          {
            visible = 1;
          }
          else
          {
            visible = 0;
          }
          let data = {
            "action": action,
            "visible": visible
          };
       swal({
              title: "Are you sure?",
              text: "You will not be able to recover this image files!",
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: "btn-danger",
              confirmButtonText: "Yes, delete it!",
              cancelButtonText: "No, cancel please!",
              closeOnConfirm: false,
              closeOnCancel: false
            },
            function(isConfirm) {
              if (isConfirm) 
              { 
                $.ajax({
                   url: "src/IMG_Upload.php",
                   method: "POST",
                   data: data,
                   success:function(data)
                   {
                     data = $.parseJSON(data);
                     if (data) 
                     {
                      swal(data.title, data.text, data.type);
                     }
                     fetch_data();
                   }
                });
              } 
              else 
              {
                swal("Cancelled", "Your image files is safe.", "error");
              }
          }); 
   });
});