// var save_url = 'https://iswaryaivf.com/campaign/book-an-appointment/save_appointment.php';
// console.log('book script called');
var save_url = $("#base_url").val();
var base_url = $("#base_url").val();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// console.log(save_url);

function checkisNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        // alert("Please enter only Numbers.");
        return false;
    }



    return true;
}

function validateEmail(email) {
  return String(email)
    .toLowerCase()
    .match(
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    );
};

// $(document).on('click','.formsubmit',function(){
// // console.log('btn clicked');
// })

function checkMandatory_appointmentform() {

    console.log('calle');
    var mndFileds = new Array('Last Name', 'Phone', 'LEADCF1');
    var fldLangVal = new Array('Name', 'Phone', 'Preferred\x20Location');
    for (i = 0; i < mndFileds.length; i++) {
        var fieldObj = document.forms['WebToLeads4177388000000565029'][mndFileds[i]];
        if (fieldObj) {
            if (((fieldObj.value).replace(/^\s+|\s+$/g, '')).length == 0) {
                if (fieldObj.type == 'file') {
                    alert('Please select a file to upload.');
                    fieldObj.focus();
                    return false;
                }

                alert(fldLangVal[i] + ' cannot be empty.');
                fieldObj.focus();
                return false;
            } else if (fieldObj.nodeName == 'SELECT') {
                if (fieldObj.options[fieldObj.selectedIndex].value == '-None-') {
                    alert(fldLangVal[i] + ' cannot be none.');
                    fieldObj.focus();
                    return false;
                }
            } else if (fieldObj.type == 'checkbox') {
                if (fieldObj.checked == false) {
                    alert('Please accept  ' + fldLangVal[i]);
                    fieldObj.focus();
                    return false;
                }
            }
             else if(fieldObj.name == 'Phone'){
                    if( ((fieldObj.value).length != 10) ){
                        alert('Invalid Mobile Number');
                        fieldObj.focus();
                        return false;
                    }
                }
            try {
                if (fieldObj.name == 'Last Name') {
                    name = fieldObj.value;
                }
            } catch (e) {}
        }
    }

    localStorage.setItem('name',$('#Last_Name').val());
    localStorage.setItem('phone',$('#Phone').val());
    localStorage.setItem('source',$('#LEADCF2').val());
    localStorage.setItem('location',$('#LEADCF1').val());

   

    var appt_form = document.getElementById('book-appointment-form');
    var formdata = new FormData(appt_form);
    // console.log(formdata);
    $.ajax({
        url: save_url+'/save-appointment',
        type: 'post',
        dataType: 'json',
        cache : false,
        processData: false,
        data: formdata,
        contentType: false,
         
        success:function(result){
            // if(result == 'success'){
            //     alert('Details saved successfully!');
            // }
            console.log(result);
        }
    });

    // return false;
    document.querySelector('.crmWebToEntityForm .formsubmit').setAttribute('disabled', true);
}


// 
$(document).on('click','.video-modal',function(){
            var id = $(this).attr('data-id');
        // console.log(base_url);
        // console.log(id);
        $.ajax({
            type : 'GET',
            url : base_url+'/get-video-modal',
            data : {
                id: id
            },
            success:function(html){
                $('#video-modal').modal('show');
                $('.video-modal-content').html(html);
               
            }
        })
    });

$(document).on('click','.video-modal-testimonial',function(){
            var id = $(this).attr('data-id');
        // console.log(base_url);
        // console.log(id);
        $.ajax({
            type : 'GET',
            url : base_url+'/get-video-modal-testimonial',
            data : {
                id: id
            },
            success:function(html){
                $('#video-modal').modal('show');
                $('.video-modal-content').html(html);
               
            }
        })
    });



$(document).on('click','.careerform-submit',function(){

    // console.log('career form clicked');

        var name = $('.career_name').val();
        var email = $('.career_email').val();
        var mobile = $('.career_mobile').val();
        var location = $('.career_location').val();
        var years_of_practice = $('.career_years').val();   
        var speciality = $('.career_speciality').val();
        var message = $('.career_message').val();

         if(name == ''){
            alert('Please enter name');
        }else if(email == ''){
            alert('Please enter email');
        }
        else if(!(validateEmail(email) )) {
            alert('Invalid Email id');
        }
        else if(mobile == ''){
            alert('Please enter mobile number');
        }
        else if(mobile.length != '10'){
            alert('Invalid mobile number');
        }
        else if(location == ''){
             alert('Please enter location');
        }
        else if(years_of_practice == ''){
            alert('Please enter Years of practice');
        }
        else if(speciality == ''){
            alert('Please enter speciality');
        }else{
             $.ajax({
            type : 'POST',
            url : base_url+'/careerform-submit',
            data : {
                name : name,
                email : email,
                mobile : mobile,
                location : location,
                years_of_practice : years_of_practice,
                speciality : speciality,
                message : message
            },
            success:function(html){
               alert('Details Saved Successfully!');
               window.location.reload();
               
            }
        })
        }


       
    });


$(document).on('click','.trainingform_submit',function(){

    // console.log('training form clicked');

        var name = $('.training_name').val();
        var email = $('.training_email').val();
        var phone = $('.training_phone').val();
        var message = $('.training_message').val();

        if(name == ''){
            alert('Please enter name');
        }else if(email == ''){
            alert('Please enter email');
        }
        else if(phone == ''){
            alert('Please enter mobile number');
        }
        else if(phone.length != '10'){
            alert('Invalid mobile number');
        }
        else if(!(validateEmail(email) )) {
            alert('Invalid Email id');
        }
        else{
            $.ajax({
            type : 'POST',
            url : base_url+'/trainingform-submit',
            data : {
                name : name,
                email : email,
                phone : phone,
                message : message
            },
            success:function(html){
               alert('Details Saved Successfully!');
               window.location.reload();
               
            }
        })
        }

        
    });

// 