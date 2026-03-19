$(document).ready(function () {

  $("#reportrangeexpenses").on('click', function() {
    $(".ranges").removeClass('camp_range doctor_range meeting_range activit_range patient_range');
    $(".ranges").addClass('expenses_range');
    $('.drp-selected').removeClass('doctor_slct meeting_slct camp_slct actvit_slct patient_slct');
    $('.drp-selected').addClass('expenses_slct');
    $('.applyBtn').removeClass('doctor_btn camp_btn meeting_btn activit_btn patient_btn');
    $('.applyBtn').addClass('expenses_btn');
});

$(".search_expensenssss").hide();

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

  $('#submit-expenses-data').click(function (event) {
      event.preventDefault();
      // Validation checks
      let isValid = true;
      // Validate doctor name
      if ($('#Branch_expenses').val() === "") {
          $('.error_doctor').text('Enter the Doctor Name');
          isValid = false;
      }
      // Validate employee name
      if ($('#activites_expenses').val() === "") {
          $('.error_employee').text('Please select the Employee Name');
          isValid = false;
      }
      // Validate specialty
      if ($('#cost_expenses').val() === "") {
          $('.error_special').text('Please select the Specialization');
          isValid = false;
      }
      // Validate hospital name
      if ($('#creatives_expenses').val() === "") {
          $('.error_hplname').text('Enter the hospital name');
          isValid = false;
      }


if ($('#image_uploads_expenses')[0].files.length === 0) {
  $('.error_images').text('Please select the Images');
  isValid = false;
}

if ($('#image_creatives_expenses')[0].files.length === 0) {
  $('.error_images').text('Please select the Images');
  isValid = false;
}

      // Validate image uploads

      if (!isValid) {
          return; // Stop the form submission if validation fails
      }
      // Create FormData object
      let formData = new FormData();
      formData.append('Branch', $('#Branch_expenses').val());
      formData.append('activites', $('#activites_expenses').val());
      formData.append('cost', $('#cost_expenses').val());

      const files = $('#image_uploads_expenses')[0].files;
      const creativesFiles = $('#image_creatives_expenses')[0].files;

      for (let i = 0; i < files.length; i++) {
        formData.append('document_purchase_order[]', files[i]);
    }

    // Append multiple files from 'image_creatives_expenses'
    for (let i = 0; i < creativesFiles.length; i++) {
        formData.append('creatives[]', creativesFiles[i]);
    }

      // Include CSRF Token
      formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
      // AJAX Request
      $.ajax({
          url: expensesadddata,
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
   $('#itemsPerPageexpenses').change(function() {
    var pageSizeexpenses = parseInt($(this).val());
    paginatexpenses(dataSourceusersexpenses, pageSizeexpenses);
    expensesfetchdatas(dataSourceusersexpenses, pageSizeexpenses, 1);  // Initially show the first page
});

      overallexpensesmanagement();

     // More fitter search.....
     var moreFilterValuesactivites = [];
     $("#expensens_submit_search").on("click", function () {
         moredatefittervale=$("#expensesdateviews").text();
         $('.clear_expenses_views').show();
         $(".search_expensenssss").show();
         var resultsArray = [];
         $(".expensens_cls").each(function () {
             var value = $(this).val();
     // Check if the value is not empty before processing
     if (value === "") {
         return; // Skip this iteration if the value is empty
     }
     var results = $(this).attr('name') + "='" + value + "'";
     resultsArray.push(results);
        fitterremovedata=resultsArray;
         });
         morefilterexpenses(fitterremovedata,moredatefittervale);
        fitterremovedata;
         var moreFilterValuesactivites = [
             $("#camp_id_more").val(),
             $("#date_activites_more").val(),
             $("#area_covered_more").val(),
             $("#camp_type_mores").val()
         ];
         $(".expenses_views").each(function (index) {
             var morefillterdata = moreFilterValuesactivites[index] ? moreFilterValuesactivites[index] : ""; // Use "N/A" if value is empty
             $(this).text(morefillterdata);
         });
     });
     $(document).on("click", ".expenses_views", function () {
         var morefillterremvedata = $(this).text();
         var datefilltervalue = $('#expensesdateviews').text();
         $(this).text("");
             $('input[type="checkbox"]').each(function() {
                 if (morefillterremvedata.includes($(this).val())) {
                     $(this).prop('checked', false); // Uncheck the checkbox
                 }
             });
         $('.expensens_cls').filter(function () {
             return $(this).val().startsWith(morefillterremvedata);
         }).val("");
        // Update the uniqueResults array to remove the corresponding filter
        fitterremovedata = fitterremovedata.filter(function (item) {
         return !item.endsWith(morefillterremvedata + "'");
     });
     expensesandoveralldata(fitterremovedata,datefilltervalue);
     });


     var fitterremovedata = []; // Keep this variable persistent
     $(document).on('click', '.expenses_range, .expenses_btn', function() {
         // Check if the click happened on a specific class
         if ($(this).hasClass('expenses_range')) {
             var datefilltervalue = $('#expensesdateviews').text(); // Get the current text value when '.ranges' is clicked
             var morefitterempty=$(".expenses_views").text();
             if(morefitterempty=='')
             {
              expensesdatefitter(datefilltervalue);
             }
             else
             {
               expensesandoveralldata(fitterremovedata,datefilltervalue);
             }
         } else if ($(this).hasClass('expenses_btn')) {
             var datefilltervaluenew = $('.expenses_btn').text(); // Get the current text value when '.applyBtn' is clicked
             var dateRange = datefilltervaluenew.split(' - ');
             function convertDateFormat(dateStr) {
                 let parts = dateStr.split('/');
                 return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
             }
             var startDate = convertDateFormat(dateRange[0]);
             var endDate = convertDateFormat(dateRange[1]);
             var datefilltervalue = `${startDate} - ${endDate}`;
             var morefitterempty=$(".expenses_views").text();
             if(morefitterempty=='')
             {
                expensesdatefitter(datefilltervalue);
             }
             else
             {
              expensesandoveralldata(fitterremovedata,datefilltervalue);
             }
         }
     });

     $(".mainclearallactivity").on("click",function(){
      $('.expensens_cls').val("")
      $('.clear_expenses_views').hide();
      $(".search_expensenssss").hide();
      $(".expenses_views").text("");
  });

  $(document).on("click", ".clear_expenses_views", function () {
    $('.expensens_cls').val("")
    $('.clear_expenses_views').hide();
    $(".search_expensenssss").hide();
    $(".expenses_views").text("");
    var datefilltervalue = $('#expensesdateviews').text();
    expensesdatefitter(datefilltervalue);
});

});

var dataSourceusersexpenses = [];

function overallexpensesmanagement() {
  $.ajax({
      url: expensesalldetails,
      type: "GET",
      success: handleSuccessexpence,
      error: handleErrorexpenses
  });
}


function handleSuccessexpence(responseData) {
  // console.log(responseData);
  dataSourceusersexpenses = responseData;
  totalItemscamp = responseData.length; // Get total items count
  var pageSizeexpenses = parseInt($('#itemsPerPageexpenses').val()); // Get selected items per page
  paginatexpenses(dataSourceusersexpenses, pageSizeexpenses);
  expensesfetchdatas(dataSourceusersexpenses, pageSizeexpenses, 1); // Show first page initially
}

function handleErrorexpenses(xhr, status, error) {
  console.error("AJAX Error:", status, error);
}

// Render table rows based on the page and page size
function expensesfetchdatas(data, pageSizeexpenses, pageNum) {
  var startIdx = (pageNum - 1) * pageSizeexpenses;
  var endIdx = pageNum * pageSizeexpenses;
  var pageData = data.slice(startIdx, endIdx);
  //console.log(pageData);
  var body = "";
  $.each(pageData, function(index, user) {
      let dateStr = user.created_at;

      body += '<tr onclick="rowClick(event)">' +
              '<td class="tdview" > <strong>#' + user.id + '</strong><br>' + dateStr + '<br></td>' +
              '<td class="tdview" > Branch Name :' + user.Branch + '<br> camp Type :' + user.camp_type + '<br></td>' +
              '<td class="tdview" > Activites :' + user.activites + '</td>' +
              '<td class="tdview" > cost :' + user.cost + '</td>' +
              '<td class="tdview" ><img src="../assets/images/pdf-file-format.png" style="width: 38px;"  alt="Icon" class="icon"></td>' +
              '<td class="tdview" ><img src="../assets/images/confidential-document.png" style="width: 38px;"  alt="Icon" class="icon"></td>' +
              '<td class="tdview" > Created :' + user.created_at + '<br> Updated :' + user.updated_at + '<br></td>' +
             '</tr>';  });
  $("#expenses_details").html(body);

}
function paginatexpenses(data, pageSizeexpenses) {
  var totalPages = Math.ceil(data.length / pageSizeexpenses);
  var paginationHtml = '';
  for (var i = 1; i <= totalPages; i++) {
      paginationHtml += '<button class="page-btnviewss " style="background-color:#b163a6;"  data-page="' + i + '">' + i + '</button>';
  }
  $('#paginatexpenses').html(paginationHtml);
  // Bind click event to each pagination button
  $('.page-btnviewss').click(function() {
      var pageNum = $(this).data('page');
      $('.page-btnviewss').removeClass('active');
      $(this).addClass('active');
      expensesfetchdatas(data, pageSizeexpenses, pageNum);
  });
}
