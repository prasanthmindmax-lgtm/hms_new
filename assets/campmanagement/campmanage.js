$(document).ready(function () {
  $("#reportrangecamp").on('click', function() {
    $(".ranges").removeClass('doctor_range activit_range');
    $(".ranges").addClass('camp_range');
    $('.drp-selected').removeClass('doctor_slct actvit_slct');
    $('.drp-selected').addClass('camp_slct');
    $('.applyBtn').removeClass('doctor_btn activit_btn');
    $('.applyBtn').addClass('camp_btn ');
});
$(".search_camp_view").hide();
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
  $('#submit-campdatas').click(function (event) {
      event.preventDefault();
      // Validation checks
      let isValid = true;
      // Validate doctor name
      if ($('#Branch').val() === "") {
          $('.error_doctor').text('Enter the Doctor Name');
          isValid = false;
      }
      // Validate employee name
      if ($('#camp_date').val() === "") {
          $('.error_employee').text('Please select the Employee Name');
          isValid = false;
      }
      // Validate specialty
      if ($('#camp_type').val() === "") {
          $('.error_special').text('Please select the Specialization');
          isValid = false;
      }
      // Validate hospital name
      if ($('#camp_location').val() === "") {
          $('.error_hplname').text('Enter the hospital name');
          isValid = false;
      }
      // Validate address
      if ($('#g_map').val() === "") {
          $('.error_adress').text('Enter the Address');
          isValid = false;
      }
      if ($('#doctor_name_views').val() === "") {
        $('.error_adress').text('Enter the Address');
        isValid = false;
    }
    if ($('#organized_by').val() === "") {
      $('.error_adress').text('Enter the Address');
      isValid = false;
  }
  if ($('#camp_incharge').val() === "") {
    $('.error_adress').text('Enter the Address');
    isValid = false;
}
      // Validate image uploads
      if (!isValid) {
          return; // Stop the form submission if validation fails
      }
      // Create FormData object
      let formData = new FormData();
      formData.append('Branch', $('#Branch').val());
      formData.append('camp_date', $('#camp_date').val());
      formData.append('camp_type', $('#camp_type').val());
      formData.append('camp_location', $('#camp_location').val());
      formData.append('g_map', $('#g_map').val());
      formData.append('doctor_name', $('#doctor_name_views').val());
      formData.append('organized_by', $('#organized_by').val());
      formData.append('camp_incharge', $('#camp_incharge').val());
      // Include CSRF Token
      formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
      // AJAX Request
      $.ajax({
          url: campdetailsadded,
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
   $('#itemsPerPagecamp').change(function() {
    var pageSizecamp = parseInt($(this).val());
    paginatecamps(dataSourceuserscamp, pageSizecamp);
    camptbl(dataSourceuserscamp, pageSizecamp, 1);  // Initially show the first page
});
var fitterremovedata = []; // Keep this variable persistent
$(document).on('click', '.camp_range, .camp_btn', function() {
    // Check if the click happened on a specific class
    if ($(this).hasClass('camp_range')) {
        var datefilltervalue = $('#datecampfitters').text(); // Get the current text value when '.ranges' is clicked
        var morefitterempty=$(".camp_views").text();
        if(morefitterempty=='')
        {
         campdatefitter(datefilltervalue);
        }
        else
        {
           alert("1");
            campandoveralldata(fitterremovedata,datefilltervalue);
        }
    } else if ($(this).hasClass('camp_btn')) {
        var datefilltervaluenew = $('.camp_slct').text(); // Get the current text value when '.applyBtn' is clicked
        var dateRange = datefilltervaluenew.split(' - ');
        function convertDateFormat(dateStr) {
            let parts = dateStr.split('/');
            return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
        }
        var startDate = convertDateFormat(dateRange[0]);
        var endDate = convertDateFormat(dateRange[1]);
        var datefilltervalue = `${startDate} - ${endDate}`;
        var morefitterempty=$(".camp_views").text();
        if(morefitterempty=='')
        {
         campdatefitter(datefilltervalue);
        }
        else
        {
           alert("2");
         campandoveralldata(fitterremovedata,datefilltervalue);
        }
    }
});
  overallcampmanagement();
     // More fitter search.....
     var moreFilterValues = [];
     $("#morefitter_camp_search").on("click", function () {
         moredatefittervale=$("#datecampfitters").text();
        // alert(moredatefittervale);
         $('.clear_camp_views').show();
         $(".search_camp_view").show();
         var resultsArray = [];
         $(".campfitters").each(function () {
             var value = $(this).val();
     // Check if the value is not empty before processing
     if (value === "") {
         return; // Skip this iteration if the value is empty
     }
     var results = $(this).attr('name') + "='" + value + "'";
     resultsArray.push(results);
        fitterremovedata=resultsArray;
         });
         morefiltercamps(fitterremovedata,moredatefittervale);
        fitterremovedata;
         var moreFilterValues = [
             $("#Branch_more").val(),
             $("#camp_type_more").val(),
             $("#camp_incharge_more").val(),
             $("#organized_by_more").val(),
             $("#doctor_name_more").val()
         ];
         $(".camp_views").each(function (index) {
             var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; // Use "N/A" if value is empty
             $(this).text(morefillterdata);
         });
     });
     $(document).on("click", ".camp_views", function () {
         var morefillterremvedata = $(this).text();
         var datefilltervalue = $('#datecampfitters').text();
         $(this).text("");
             $('input[type="checkbox"]').each(function() {
                 if (morefillterremvedata.includes($(this).val())) {
                     $(this).prop('checked', false); // Uncheck the checkbox
                 }
             });
         $('.campfitters').filter(function () {
             return $(this).val().startsWith(morefillterremvedata);
         }).val("");
        // Update the uniqueResults array to remove the corresponding filter
        fitterremovedata = fitterremovedata.filter(function (item) {
         return !item.endsWith(morefillterremvedata + "'");
     });
     campandoveralldataremove(fitterremovedata,datefilltervalue);
     });
     $(".mainclearallcamp").on("click",function(){
      $('.campfitters').val("")
      $('.clear_camp_views').hide();
      $(".search_camp_view").hide();
      $(".camp_views").text("");
  });
  $(document).on("click", ".clear_camp_views", function () {
    $('.campfitters').val("")
    $('.clear_camp_views').hide();
    $(".search_camp_view").hide();
    $(".camp_views").text("");
    var datefilltervalue = $('#datecampfitters').text();
    campdatefitter(datefilltervalue);
});
});
var dataSourceuserscamp = [];
function overallcampmanagement() {
  $.ajax({
      url: campalldetails,
      type: "GET",
      success: handleSuccess,
      error: handleError
  });
}
function morefiltercamps(fitterremovedata,moredatefittervale)
{
    //alert(moredatefittervale);
  $.ajax({
    url: campfetchurlfitters,
    type: "GET",
    data: {
        fitterremovedata:fitterremovedata,
        moredatefittervale:moredatefittervale,
    },
    success: handleSuccess,
      error: handleError
  });
}
function campdatefitter(datefilltervalue)
{
  $.ajax({
    url: campdatefitters,
    type: "GET",
    data: {
        datefilltervalue:datefilltervalue,
    },
    success: handleSuccess,
      error: handleError
  });
}
function campandoveralldata(fitterremovedata,datefilltervalue)
{
  $.ajax({
    url: campdateandsearchfitters,
    type: "GET",
    data: {
      fitterremovedata:fitterremovedata,
      datefilltervalue:datefilltervalue,
    },
    success: handleSuccess,
      error: handleError
  });
}
function campandoveralldataremove(fitterremovedata,datefilltervalue)
{
    alert(datefilltervalue);
    $.ajax({
        url: campdateandsearchfitters,
        type: "GET",
        data: {
          fitterremovedata:fitterremovedata,
          datefilltervalue:datefilltervalue,
        },
        success: handleSuccess,
          error: handleError
      });
}
function handleSuccess(responseData) {
  // console.log(responseData);
  dataSourceuserscamp = responseData;
  totalItemscamp = responseData.length; // Get total items count
  var pageSizecamp = parseInt($('#itemsPerPagecamp').val()); // Get selected items per page
  paginatecamps(dataSourceuserscamp, pageSizecamp);
  camptbl(dataSourceuserscamp, pageSizecamp, 1); // Show first page initially
}
function handleError(xhr, status, error) {
  console.error("AJAX Error:", status, error);
}
// Render table rows based on the page and page size
function camptbl(data, pageSizecamp, pageNum) {
  var startIdx = (pageNum - 1) * pageSizecamp;
  var endIdx = pageNum * pageSizecamp;
  var pageData = data.slice(startIdx, endIdx);
  //console.log(pageData);
  var body = "";
  let camp_count=0;
  $.each(pageData, function(index, user) {
    camp_count++;
      let dateStr = user.camp_date;
      let dateStrviews = user.created_at;
      let dateStrupdate = user.updated_at;
      let formattedDate = moment(dateStr).format("| DD MMM YYYY |");
      let formattedDates = moment(dateStrviews).format("| DD MMM YYYY | HH:MM");
      let ipdateformattedDate = moment(dateStrupdate).format("| DD MMM YYYY | HH:MM");
      body += '<tr onclick="rowClick(event)">' +
              '<td class="tdview" > <strong>#' + user.id + '</strong><br>' + formattedDate + '<br></td>' +
              '<td class="tdview" > Name : ' + user.Branch + '<br> Camp Type : '+ user.camp_type +'<br></td>' +
              '<td class="tdview" > Location : ' + user.camp_location + '<br> G Map : '+ user.g_map +'<br></td>' +
              '<td class="tdview" >Organizer : ' + user.organized_by + '<br> Incharge : '+ user.camp_incharge +'<br></td>' +
              '<td class="tdview" > Craeted At : ' + formattedDates+ '<br> Updated At : '+ ipdateformattedDate +'<br></td>' +
              '<td class="tdview" > Name : ' + user.doctor_name + ' <br></td>' +
             '</tr>';  });
  $("#camp_details").html(body);
  $("#total_camps").text(camp_count);
}
function paginatecamps(data, pageSizecamp) {
  var totalPages = Math.ceil(data.length / pageSizecamp);
  var paginationHtml = '';
  for (var i = 1; i <= totalPages; i++) {
      paginationHtml += '<button class="page-btnviewss " style="background-color:#b163a6;"  data-page="' + i + '">' + i + '</button>';
  }
  $('#paginationcamp').html(paginationHtml);
  // Bind click event to each pagination button
  $('.page-btnviewss').click(function() {
      var pageNum = $(this).data('page');
      $('.page-btnviewss').removeClass('active');
      $(this).addClass('active');
      camptbl(data, pageSizecamp, pageNum);
  });
}
