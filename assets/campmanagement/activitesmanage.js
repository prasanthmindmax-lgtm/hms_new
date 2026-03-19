$(document).ready(function () {
  $("#reportrangeactivites").on('click', function() {
    $(".ranges").removeClass('camp_range doctor_range meeting_range');
    $(".ranges").addClass('activit_range');
    $('.drp-selected').removeClass('doctor_slct meeting_slct camp_slct');
    $('.drp-selected').addClass('actvit_slct');
    $('.applyBtn').removeClass('doctor_btn camp_btn meeting_btn');
    $('.applyBtn').addClass('activit_btn');
});
$(".search_activites").hide();
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
  });
  // Clear error span text on input
  $('input, select, textarea').on('input change', function () {
      $(this).siblings('span').text('');
  });
  $('#close-button').click(function () {
      // Clear all input fields
      $('input, select, textarea').val('');
      // Clear error messages
      $('span').text('');
  });
  $('#submit-activites').click(function (event) {
      event.preventDefault();
      // Validation checks
      let isValid = true;
      // Validate doctor name
      if ($('#camp_id_views').val() === "") {
          $('.error_doctor').text('Enter the Doctor Name');
          isValid = false;
      }
      // Validate employee name
      if ($('#date_activites').val() === "") {
          $('.error_employee').text('Please select the Employee Name');
          isValid = false;
      }
      // Validate specialty
      if ($('#activites').val() === "") {
          $('.error_special').text('Please select the Specialization');
          isValid = false;
      }
      // Validate hospital name
      if ($('#area_covered').val() === "") {
          $('.error_hplname').text('Enter the hospital name');
          isValid = false;
      }
      // Validate address
      if ($('#description').val() === "") {
          $('.error_adress').text('Enter the Address');
          isValid = false;
      }
if ($('#image_uploads_camp')[0].files.length === 0) {
  $('.error_images').text('Please select the Images');
  isValid = false;
}
      // Validate image uploads
      if (!isValid) {
          return; // Stop the form submission if validation fails
      }
      // Create FormData object
      let formData = new FormData();
      formData.append('camp_id', $('#camp_id_views').val());
      formData.append('date_activites', $('#date_activites').val());
      formData.append('activites', $('#activites').val());
      formData.append('area_covered', $('#area_covered').val());
      formData.append('description', $('#description').val());
      const files = $('#image_uploads_camp')[0].files;
      for (let i = 0; i < files.length; i++) {
          formData.append('images[]', files[i]);
      }
      // Include CSRF Token
      formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
      // AJAX Request
      $.ajax({
          url: activitesadddata,
          type: "POST",
          data: formData,
          processData: false, // Prevent processing of the data
          contentType: false, // Prevent setting content-type header
          success: function (response) {
              if (response.success) {
                  alert(response.message);
                  location.reload(); // Optional: Refresh the page
              }
          },
          error: function (error) {
              console.error(error.responseJSON);
          },
      });
  });
   // Handle items per page change
   $('#itemsPerPageactivites').change(function() {
    var pageSizeact = parseInt($(this).val());
    paginatacties(dataSourceusersactivites, pageSizeact);
    activitesfetchdatas(dataSourceusersactivites, pageSizeact, 1);  // Initially show the first page
});
  overallactivitymanagement();
     // More fitter search.....
     var moreFilterValuesactivites = [];
     $("#activity_fitters_search").on("click", function () {
         moredatefittervale=$("#campactivitesdate").text();
         $('.clear_activites_views').show();
         $(".search_activites").show();
         var resultsArray = [];
         $(".activityfitters").each(function () {
             var value = $(this).val();
     // Check if the value is not empty before processing
     if (value === "") {
         return; // Skip this iteration if the value is empty
     }
     var results = $(this).attr('name') + "='" + value + "'";
     resultsArray.push(results);
        fitterremovedata=resultsArray;
         });
         morefilteractivites(fitterremovedata,moredatefittervale);
        fitterremovedata;
         var moreFilterValuesactivites = [
             $("#camp_id_more").val(),
             $("#date_activites_more").val(),
             $("#area_covered_more").val(),
             $("#camp_type_mores").val()
         ];
         $(".activites_views").each(function (index) {
             var morefillterdata = moreFilterValuesactivites[index] ? moreFilterValuesactivites[index] : ""; // Use "N/A" if value is empty
             $(this).text(morefillterdata);
         });
     });
     $(document).on("click", ".activites_views", function () {
         var morefillterremvedata = $(this).text();
         var datefilltervalue = $('#campactivitesdate').text();
         $(this).text("");
             $('input[type="checkbox"]').each(function() {
                 if (morefillterremvedata.includes($(this).val())) {
                     $(this).prop('checked', false); // Uncheck the checkbox
                 }
             });
         $('.activityfitters').filter(function () {
             return $(this).val().startsWith(morefillterremvedata);
         }).val("");
        // Update the uniqueResults array to remove the corresponding filter
        fitterremovedata = fitterremovedata.filter(function (item) {
         return !item.endsWith(morefillterremvedata + "'");
     });
     activitesandoveralldata(fitterremovedata,datefilltervalue);
     });
     var fitterremovedata = []; // Keep this variable persistent
     $(document).on('click', '.activit_range, .activit_btn', function() {
         // Check if the click happened on a specific class
         if ($(this).hasClass('activit_range')) {
             var datefilltervalue = $('#campactivitesdate').text(); // Get the current text value when '.ranges' is clicked
             var morefitterempty=$(".activites_views").text();
             if(morefitterempty=='')
             {
              activitesdatefitter(datefilltervalue);
             }
             else
             {
                 activitesandoveralldata(fitterremovedata,datefilltervalue);
             }
         } else if ($(this).hasClass('activit_btn')) {
             var datefilltervaluenew = $('.actvit_slct').text(); // Get the current text value when '.applyBtn' is clicked
             var dateRange = datefilltervaluenew.split(' - ');
             function convertDateFormat(dateStr) {
                 let parts = dateStr.split('/');
                 return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
             }
             var startDate = convertDateFormat(dateRange[0]);
             var endDate = convertDateFormat(dateRange[1]);
             var datefilltervalue = `${startDate} - ${endDate}`;
             var morefitterempty=$(".activites_views").text();
             if(morefitterempty=='')
             {
                activitesdatefitter(datefilltervalue);
             }
             else
             {
              activitesandoveralldata(fitterremovedata,datefilltervalue);
             }
         }
     });
     $(".mainclearallactivity").on("click",function(){
      $('.activityfitters').val("")
      $('.clear_activites_views').hide();
      $(".search_activites").hide();
      $(".activites_views").text("");
  });
  $(document).on("click", ".clear_activites_views", function () {
    $('.activityfitters').val("")
    $('.clear_activites_views').hide();
    $(".search_activites").hide();
    $(".activites_views").text("");
    var datefilltervalue = $('#campactivitesdate').text();
    activitesdatefitter(datefilltervalue);
});
});
var dataSourceusersactivites = [];
function overallactivitymanagement() {
  $.ajax({
      url: activitesalldetails,
      type: "GET",
      success: handleSuccessactivites,
      error: handleErroractivites
  });
}
function activitesdatefitter(datefilltervalue)
{
    $.ajax({
        url: activitesdatefitters,
        type: "GET",
        data: {
            datefilltervalue:datefilltervalue,
        },
        success: handleSuccessactivites,
          error: handleErroractivites
      });
}
function  morefilteractivites(fitterremovedata,moredatefittervale)
{
    $.ajax({
        url: activitesdatanaddatefitters,
        type: "GET",
        data: {
            fitterremovedata:fitterremovedata,
            moredatefittervale:moredatefittervale,
        },
        success: handleSuccessactivites,
          error: handleErroractivites
      });
}
function activitesandoveralldata(fitterremovedata,datefilltervalue)
{
    // alert(fitterremovedata);
    $.ajax({
        url: activitesdateandfittertexts,
        type: "GET",
        data: {
            fitterremovedata:fitterremovedata,
            datefilltervalue:datefilltervalue,
        },
        success: handleSuccessactivites,
          error: handleErroractivites
      });
}
function handleSuccessactivites(responseData) {
  // console.log(responseData);
  dataSourceusersactivites = responseData;
  totalItemscamp = responseData.length; // Get total items count
  var pageSizeact = parseInt($('#itemsPerPageactivites').val()); // Get selected items per page
  paginatacties(dataSourceusersactivites, pageSizeact);
  activitesfetchdatas(dataSourceusersactivites, pageSizeact, 1); // Show first page initially
}
function handleErroractivites(xhr, status, error) {
  console.error("AJAX Error:", status, error);
}
// Render table rows based on the page and page size
function activitesfetchdatas(data, pageSizeact, pageNum) {
  var startIdx = (pageNum - 1) * pageSizeact;
  var endIdx = pageNum * pageSizeact;
  var pageData = data.slice(startIdx, endIdx);
  //console.log(pageData);
  var body = "";
  $.each(pageData, function(index, user) {
      let dateStr = user.date_activites;
      body += '<tr onclick="rowClick(event)">' +
              '<td class="tdview" > <strong>#' + user.id + '</strong><br>' + dateStr + '<br></td>' +
              '<td class="tdview" > Branch Name :' + user.Branch + '<br> camp Type :' + user.camp_type + '<br></td>' +
              '<td class="tdview" > Activites :' + user.activites + '<br> Area Coverd : ' + user.area_covered + '<br></td>' +
              '<td class="tdview" > Created :' + user.created_at + '<br> Updated :' + user.updated_at + '<br></td>' +
              '<td class="tdview" ><img src="../assets/images/view.png" style="width: 38px;"  alt="Icon" class="icon"></td>' +
              '<td class="tdview" ><img src="../assets/images/job-description.png" style="width: 38px;"  alt="Icon" class="icon"></td>' +
             '</tr>';  });
  $("#activites_details").html(body);
}
function paginatacties(data, pageSizeact) {
  var totalPages = Math.ceil(data.length / pageSizeact);
  var paginationHtml = '';
  for (var i = 1; i <= totalPages; i++) {
      paginationHtml += '<button class="page-btnviewss " style="background-color:#b163a6;"  data-page="' + i + '">' + i + '</button>';
  }
  $('#paginateactivites').html(paginationHtml);
  // Bind click event to each pagination button
  $('.page-btnviewss').click(function() {
      var pageNum = $(this).data('page');
      $('.page-btnviewss').removeClass('active');
      $(this).addClass('active');
      activitesfetchdatas(data, pageSizeact, pageNum);
  });
}
