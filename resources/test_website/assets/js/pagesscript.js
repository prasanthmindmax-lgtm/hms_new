$(document).ready(function(){
    
// newly added
$(document).on('click','.add-spec-row',function(){
  console.log('clicked');
    // rand = Math.floor(Math.random() * 5826 + 10);
    // $('#specialities_row').append('<div class="form-group specialities_row_single"><input type="text"  class="form-control" name="faq_title[]" placeholder="Title"><br><textarea cols="5" rows="5" class="form-control" id="faqdesc'+rand+'"  name="faq_description[]"></textarea><button class="btn btn-danger remove-faq-row" type="button">-</button></div>');
    $('.specialities_row').append('<div class="specialities_row_single"><hr>'+
    '<div class="fileUpload btn"><span><i class="fa fa-camera"></i></span>'+
    '<div class="title"> Select image </div>'+
    '<input type="file" name="spec_inner_img[]" class="upload" accept="image/x-png,image/gif,image/jpeg"/>'+
    '</div>'+
    '<input  type="text" name="spec_inner_num[]"  class="form-control form-control-sm" placeholder="Count"/>'+ 
    '<input  type="text" name="spec_inner_title[]"  class="form-control form-control-sm" placeholder="Title"/>'+
    '<button class="btn btn-danger remove_spec_row" type="button">-</button></div>');
    $('.editor').summernote({ height: 150});
  
  });
  $(document).on('click','.remove_spec_row',function(){
       $(this).parent('.specialities_row_single').remove();
  });

  $(document).on('click','.add_avail_row',function(){
      $('.availabilities_row').append('<div class="availabilities_row_single"><hr>'+
      '<div class="fileUpload btn"><span><i class="fa fa-camera"></i></span>'+
      '<div class="title"> Select image </div>'+
      '<input type="file" name="avail_sub_img[]" class="upload" accept="image/x-png,image/gif,image/jpeg"/>'+
      '</div>'+
      '<input  type="text" name="avail_sub_title1[]"  class="form-control form-control-sm" placeholder="Title 1"/>'+ 
      '<input  type="text" name="avail_sub_title2[]"  class="form-control form-control-sm" placeholder="Title 2"/>'+ 
      '<textarea name="aval_sub_description[]" rows="5" class="form-control form-control-sm"></textarea>'+ 
      '<button class="btn btn-danger remove_avail_row" type="button">-</button></div>');
      $('.editor').summernote({ height: 150});
    
    });
    $(document).on('click','.remove_avail_row',function(){
         $(this).parent('.availabilities_row_single').remove();
    });

    $(document).on('click','.add_awards_row',function(){
      $('.awards_row').append('<div class="awards_row_single"><hr>'+
      '<div class="fileUpload btn"><span><i class="fa fa-camera"></i></span>'+
      '<div class="title"> Select image </div>'+
      '<input type="file" name="awards_img[]" class="upload" accept="image/x-png,image/gif,image/jpeg"/>'+
    '</div>'+
      '<button class="btn btn-danger remove_awards_row" type="button">-</button></div>');
    });
    $(document).on('click','.remove_awards_row',function(){
      $(this).parent('.awards_row_single').remove();
 });

 $('.remove_prev_awrd_row').on('click',function(){
  $(this).parent('.image_prev_mdiv').remove();
  var rval=$(this).attr('data-awdimg');
 var rem_txtbox_val =$('.removed_awd_imgs').val();
 var removed_val = '';
 if(rval != '')
 {
    if(rem_txtbox_val ==''){
      removed_val =  $('.removed_awd_imgs').val()+rval;
    }
    else{
      removed_val =  $('.removed_awd_imgs').val() +','+rval;
    }
 }
 
 $('.removed_awd_imgs').val(removed_val);
 console.log($('.removed_awd_imgs').val());

 });

$('.add_treat_ourservice_row').on('click',function(){
   $('.treatment_ourservice_mdiv').append('<div class="treatment_ourservice_subdiv">'+
                      '<textarea name="treat_ourservice[]" rows="3"  class="form-control form-control-sm"></textarea>'+
                      '<button class="btn btn-danger rem_treat_ourservice_row"  type="button">-</button>'+
                      '</div>');
                      console.log('clicked');

 });

 $(document).on('click','.rem_treat_ourservice_row',function(){
  $(this).parent('.treatment_ourservice_subdiv').remove();
});

 $('.add_faq_row').on('click',function(){
  $('.faq_main_div').append('<div class="faq_main_subdiv">'+
  '<input type="text" name="faq_title[]" class="form-control input-sm" placeholder="Title">'+           
  '<textarea name="faq_description[]" rows="5"  class="form-control editor"></textarea>'+
  '<button class="btn btn-danger delete_faq_row"  type="button">-</button>'+
  '</div>');
  $('.editor').summernote({ height: 100});

});
$(document).on('click','.delete_faq_row',function(){
  $(this).parent('.faq_main_subdiv').remove();
});
 

$('.add_convenient_sec_row').on('click',function(){
  $('.convenient_mdiv').append('<div class="convenient_subdiv">'+
  '<textarea name="convenient_section[]" rows="3" class="form-control form-control-sm"></textarea>'+
  '<button class="btn btn-danger rem_convenient_row"  type="button">-</button>'+
'</div>');
});

$(document).on('click','.rem_convenient_row',function(){
  $(this).parent('.convenient_subdiv').remove();
});

$(document).on('click','.add_awards_section',function(){
  $('.awardssec_row').append('<div class="awards_sec_single"><hr>'+
  '<input  type="text" name="awardimg_title[]" class="form-control form-control-sm" placeholder="Image title"/>'+
    '<div class="fileUpload btn"><span><i class="fa fa-camera"></i></span>'+
      '<div class="title"> Select image </div>'+
      '<input type="file" name="awards_img[]" class="upload" accept="image/x-png,image/gif,image/jpeg"/>'+
    '</div>'+
  '<button class="btn btn-danger remove_awards_sec_single" type="button">-</button></div>');
});

$(document).on('click','.remove_awards_sec_single',function(){
  $(this).parent('.awards_sec_single').remove();
});

  // newly added
});