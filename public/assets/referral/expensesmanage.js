$(document).ready(function () {
$(".my_search_view").hide();
$(".search_expensenssss").hide();
leads_fetch();
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

   $('.dropdown-item-loc').on('click', function () {
        var selectedBranchName = $(this).text();
        var selectedBranchId = $(this).data('value');
        $('#zone_id').val(selectedBranchName);
        $('#zone_id').attr('data-value', selectedBranchId);
    });
  $('#submit-expenses-lead-data').click(function (event) {
    event.preventDefault();
      // Validation checks
      let isValid = true;
      // Validate doctor name
    //   if ($('#zone_id').val() === "") {
    //       $('.error_location').text('Please select the Location');
    //       isValid = false;
    //   }
      // Validate employee name
      if ($('#wifename').val() === "") {
          $('.error_wife').text('Enter the Wife Name');
          isValid = false;
      }
      // Validate specialty
      if ($('#wifeno').val() === "") {
          $('.error_wifeno').text('Please select the Specialization');
          isValid = false;
      }
      // Validate hospital name
      if ($('#address').val() === "") {
          $('.error_address').text('Enter the Address');
          isValid = false;
      }
// if ($('#image_uploads_expenses')[0].files.length === 0) {
//   $('.error_images').text('Please select the Images');
//   isValid = false;
// }

// if ($('#image_creatives_expenses')[0].files.length === 0) {
//   $('.error_images').text('Please select the Images');
//   isValid = false;
// }
      if (!isValid) {
          return; // Stop the form submission if validation fails
      }
      // Create FormData object
      let formData = new FormData();
           let zoneId = $('#zone_id').attr('data-value');
      formData.append('camp_zone_id', zoneId);
        formData.append('camp_id', $('#camp_id').val());
        formData.append('camp_name', $('#campa_name').val());
      formData.append('camp_wife_name', $('#wifename').val());
      formData.append('camp_wife_mobile', $('#wifeno').val());
      formData.append('camp_address', $('#address').val());
      formData.append('camp_husband_name', $('#husname').val());
      formData.append('camp_wife_age', $('#wifeage').val());
      formData.append('camp_marriage_at', $('#marriageat').val());
      formData.append('camp_married_years', $('#marriedyear').val());
      formData.append('camp_husband_mobile', $('#husno').val());
      formData.append('camp_city', $('#city').val());
      formData.append('camp_husband_age', $('#husage').val());
      formData.append('camp_state', $('#state').val());
      formData.append('camp_country', $('#country').val());
      formData.append('camp_email', $('#emailadd').val());
      formData.append('camp_wife_mrdno', $('#wifemrd').val());
      formData.append('camp_hus_mrdno', $('#husmrd').val());
      formData.append('camp_zipcode', $('#zipcode').val());
      formData.append('camp_profile_group', $('#profile_grp').val());
      formData.append('camp_for_fertility', $('#for_fertility').val());
      formData.append('camp_prefered_call', $('#prefered_call').val());
      formData.append('camp_prefered_language', $('#prefered_lan').val());
      formData.append('capm_walkindate', $('#walkin_date').val());
      formData.append('camp_description', $('#camp_desc').val());

      // Include CSRF Token
      formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
      $.ajax({
          url: campleadsnewadddata,
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {

               if (response.success) {
                 window.dispatchEvent(new CustomEvent('swal:toast', {
                     detail: {
                       title:'Info!',
                       text: response.message,
                       icon: 'success',
                       background: 'success',
                     }
                 }));
                //    document.getElementById('analytics-tab-3').click();
                      window.location.href = "/superadmin/campleads";
                }else{
                 window.dispatchEvent(new CustomEvent('swal:toast', {
                     detail: {
                       title:'Error!',
                       text: response.message,
                       icon: 'error',
                       background: '#f8d7da',
                     }
                 }));
                }
                leads_fetch();
                   $("#exampleModal3").modal('hide');
          },
          error: function (error) {
              console.error(error.responseJSON);
          },
      });
  });


  $('#submit-expenses-data').click(function (event) {
    event.preventDefault();
      // Validation checks
      let isValid = true;
      // Validate doctor name
    //   if ($('#zone_id').val() === "") {
    //       $('.error_location').text('Please select the Location');
    //       isValid = false;
    //   }
      // Validate employee name
      if ($('#wifename').val() === "") {
          $('.error_wife').text('Enter the Wife Name');
          isValid = false;
      }
      // Validate specialty
      if ($('#wifeno').val() === "") {
          $('.error_wifeno').text('Please select the Specialization');
          isValid = false;
      }
      // Validate hospital name
      if ($('#address').val() === "") {
          $('.error_address').text('Enter the Address');
          isValid = false;
      }
// if ($('#image_uploads_expenses')[0].files.length === 0) {
//   $('.error_images').text('Please select the Images');
//   isValid = false;
// }

// if ($('#image_creatives_expenses')[0].files.length === 0) {
//   $('.error_images').text('Please select the Images');
//   isValid = false;
// }
      if (!isValid) {
          return; // Stop the form submission if validation fails
      }
      // Create FormData object
      let formData = new FormData();
           let zoneId = $('#zone_id').attr('data-value');
      formData.append('camp_zone_id', zoneId);
        formData.append('camp_id', $('#camp_id').val());
        formData.append('camp_name', $('#campa_name').val());
      formData.append('camp_wife_name', $('#wifename').val());
      formData.append('camp_wife_mobile', $('#wifeno').val());
      formData.append('camp_address', $('#address').val());
      formData.append('camp_husband_name', $('#husname').val());
      formData.append('camp_wife_age', $('#wifeage').val());
      formData.append('camp_marriage_at', $('#marriageat').val());
      formData.append('camp_married_years', $('#marriedyear').val());
      formData.append('camp_husband_mobile', $('#husno').val());
      formData.append('camp_city', $('#city').val());
      formData.append('camp_husband_age', $('#husage').val());
      formData.append('camp_state', $('#state').val());
      formData.append('camp_country', $('#country').val());
      formData.append('camp_email', $('#emailadd').val());
      formData.append('camp_wife_mrdno', $('#wifemrd').val());
      formData.append('camp_hus_mrdno', $('#husmrd').val());
      formData.append('camp_zipcode', $('#zipcode').val());
      formData.append('camp_profile_group', $('#profile_grp').val());
      formData.append('camp_for_fertility', $('#for_fertility').val());
      formData.append('camp_prefered_call', $('#prefered_call').val());
      formData.append('camp_prefered_language', $('#prefered_lan').val());
      formData.append('capm_walkindate', $('#walkin_date').val());
      formData.append('camp_description', $('#camp_desc').val());

      // Include CSRF Token
      formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
      $.ajax({
          url: leadsadddata,
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function (response) {

               if (response.success) {
                 window.dispatchEvent(new CustomEvent('swal:toast', {
                     detail: {
                       title:'Info!',
                       text: response.message,
                       icon: 'success',
                       background: 'success',
                     }
                 }));
                //    document.getElementById('analytics-tab-3').click();
                      window.location.href = "/superadmin/campleads";
                }else{
                 window.dispatchEvent(new CustomEvent('swal:toast', {
                     detail: {
                       title:'Error!',
                       text: response.message,
                       icon: 'error',
                       background: '#f8d7da',
                     }
                 }));
                }
                leads_fetch();
                   $("#exampleModal3").modal('hide');
          },
          error: function (error) {
              console.error(error.responseJSON);
          },
      });
  });


    $('#itemsPerPageSelectdocument').change(function() {
        var pageSizedocuments = parseInt($(this).val());
        renderPaginationdocument(dataSourcedocument, pageSizedocuments);
        renderTabledocument(dataSourcedocument, pageSizedocuments, 1);  // Initially show the first page
    });
var dataSourcedocument = [];
function renderTabledocument(data, pageSizedocuments, pageNum) {
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    var pageData = data.slice(startIdx, endIdx);
    //console.log(pageData);
    var body = "";
    var uniqueZones = [];
    $.each(pageData, function(index, user) {
        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD/MM/YYYY");
        body += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="idfetch" data-id="'+ user.camp_id +'"> <strong>#' + user.camp_id + '</strong><br>' + formattedDate + '<br></td>' +
                '<td class="tdview"  data-filesview="' + user.camp_wife_name + '">' + user.location_name + '<br>' + user.zone_name+ '<br></td>' +
                 '<td class="tdview" >'+ user.camp_name +'</td>' +
                '<td class="tdview" >'+ user.camp_wife_name +'</td>' +
               '<td class="tdview" >'+ user.camp_husband_name +'</td>' +
                '<td class="tdview" >'+ user.camp_wife_mobile +'</td>' +
               '<td class="tdview" >'+ user.camp_husband_mobile +'</td>' +
               '<td class="tdview" >'+ user.camp_address +'</td>' +
               '<td class="tdview" >'+ user.camp_marriage_at +'</td>' +
               '<td class="tdview" >'+ user.camp_married_years +'</td>' +
                '<td class="tdview" >'+ user.camp_wife_age +'</td>' +
               '<td class="tdview" >'+ user.camp_husband_age +'</td>' +
               '<td class="tdview" >'+ user.camp_city +'</td>' +
                '<td class="tdview" >'+ user.camp_state +'</td>' +
               '<td class="tdview" >'+ user.camp_email +'</td>' +
               '<td class="tdview" >'+ user.camp_country +'</td>' +
                '<td class="tdview" >'+ user.camp_zipcode +'</td>' +
               '<td class="tdview" >'+ user.camp_wife_mrdno +'</td>' +
                '<td class="tdview" >'+ user.camp_hus_mrdno +'</td>' +
               '<td class="tdview" >'+ user.camp_profile_group +'</td>' +
               '<td class="tdview" >'+ user.camp_for_fertility +'</td>' +
               '<td class="tdview" >'+ user.camp_prefered_call +'</td>' +
               '<td class="tdview" >'+ user.camp_prefered_language +'</td>' +
                '<td class="tdview" >'+ user.capm_walkindate +'</td>' +
               '<td class="tdview" >'+ user.camp_description +'</td>' +
               '<td class="tdview">' + user.created_by + '</td>' +
               '</tr>';  });
                if (pageData.length === 0) {
			body += '<tr><td colspan="13" class="tdview" style="text-align: center;">No data available</td></tr>';
		}
    $("#leads_details").html(body);
    $("#mycounts").text(totalItems);
}

function renderPaginationdocument(data, pageSizedocuments, currentPage = 1) {
    const totalPages = Math.ceil(data.length / pageSizedocuments);
    let paginationHtml = '';

    if (currentPage > 1) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage - 1}">Prev</button>`;
    }

    const maxVisible = 3;
    const pageRange = [];

    pageRange.push(1);

    if (currentPage > maxVisible) {
        pageRange.push('...');
    }

    const start = Math.max(2, currentPage - 1);
    const end = Math.min(totalPages - 1, currentPage + 1);

    for (let i = start; i <= end; i++) {
        pageRange.push(i);
    }

    if (currentPage < totalPages - maxVisible + 1) {
        pageRange.push('...');
    }

    if (totalPages > 1) {
        pageRange.push(totalPages);
    }

    for (let i = 0; i < pageRange.length; i++) {
        if (pageRange[i] === '...') {
            paginationHtml += `<span class="dots">...</span>`;
        } else {
            const page = pageRange[i];
            const activeClass = page === currentPage ? 'active' : '';
            const bgColor = page === currentPage ? 'style="background-color: #080fd399;"' : '';
            paginationHtml += `<button class="page-btnviews ${activeClass}" data-page="${page}" ${bgColor}>${page}</button>`;
        }
    }

    if (currentPage < totalPages) {
        paginationHtml += `<button class="page-btnviews" data-page="${currentPage + 1}">Next</button>`;
    }

    $('#paginationdocument').html(paginationHtml);

    $('.page-btnviews').click(function () {
        const pageNum = $(this).data('page');
        renderPaginationdocument(data, pageSizedocuments, pageNum);
        renderTabledocument(data, pageSizedocuments, pageNum);
    });

    renderTabledocument(data, pageSizedocuments, currentPage);
}

$('.ranges, .applyBtn').on('click', function() {
    // Check if the click happened on a specific class

    if ($(this).hasClass('ranges')) {
        var moredatefittervale = $('#mydateviewsall').text();
        ticketdatefillterrange(moredatefittervale,fitterremovedata);

    } else if ($(this).hasClass('applyBtn')) {

        var datefilltervaluenew = $('.drp-selected').text(); // Get the current text value when '.applyBtn' is clicked
        var dateRange = datefilltervaluenew.split(' - ');
        function convertDateFormat(dateStr) {
            let parts = dateStr.split('/');
            return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
        }
        var startDate = convertDateFormat(dateRange[0]);
        var endDate = convertDateFormat(dateRange[1]);
        var moredatefittervale = `${startDate} - ${endDate}`;
        ticketdatefillterrange(moredatefittervale,fitterremovedata);

    }

});


function leads_fetch() {
		var moredatefittervale = $('#mydateallviews').text();
		$(".value_views_mysearch").text("");
		$("#my_ticket_details1").show();
		$.ajax({
			url: leadsadataUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
			},
			success: function (responseData) {
				handleSuccess(responseData);
			},
			error: function (xhr, status, error) {
				console.error("AJAX Error:", status, error);
			}
		});
	}
    function secmorefilterview(uniqueResults,url,moredatefittervale) {
        if(uniqueResults!="")
           {
           var morefilltersall=uniqueResults.join(" AND ");
           $.ajax({
               url: url,
               type: "GET",
               data: {
                   morefilltersall: morefilltersall,
                   moredatefittervale:moredatefittervale,
               },
               success: function (responseData) {
                   handleSuccess(responseData);
               },
               error: function (xhr, status, error) {
                   $("#my_ticket_details1").hide();
                   console.error("AJAX Error:", status, error);
               }
           });
           }else
          {
            $('.clear_my_views').hide();
            $(".my_search_view").hide();
            leads_fetch(2);

          }
    }
    function handleSuccess(responseData) {
                $("#my_ticket_details1").hide();
                $("#document_tbl").show();
                dataSourcedocument = responseData;
                totalItems = responseData.length;
                var pageSizedocuments = parseInt($('#itemsPerPageSelectdocument').val()); // Get selected items per page
                renderPaginationdocument(dataSourcedocument, pageSizedocuments);
                renderTabledocument(dataSourcedocument, pageSizedocuments, 1); // Show first page initially

    }

    var fitterremovedata = [];
    function ticketdatefillterrange(moredatefittervale,fitterremovedata) {
    currentFilter = moredatefittervale;
	var morefilltersall=fitterremovedata.join(" AND ");

    $("#document_tbl").show();
    $.ajax({
        url: leadsadataUrl,
        type: "GET",
        data: {
            moredatefittervale: currentFilter,
			morefilltersall: morefilltersall,

        },
        success: function (responseData) {
            handleSuccess(responseData);
        },
        error: function (xhr, status, error) {
            $("#document_tbl").hide();
            console.error("AJAX Error:", status, error);
        }
    });
}

   $(document).on('click', '.sec_options_marketers div', function () {
    $(".my_value_views").text("");
      $('.clear_my_views').show();
    $(".my_search_view").show();
    var moredatefittervale = $('#mydateallviews').text();

    // Initialize an array to hold the filtered results
    let resultsArray_marketer = [];
    // Collect the values from the search inputs
    $(".securitydatasearch").each(function () {
        const value = $(this).val();
        if (value !== "") {
            const result =  $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(result);
        }
    });

    const moreFilterValues_market = [
        $("#lead_name_views").val(),
        $("#sec_zone_views").val(),
        $("#sec_loc_views").val(),
    ];
// alert(moreFilterValues_market);
    // Update the UI with the selected filter values
    $(".value_views_mysearch").each(function (index) {
        const filterValue = moreFilterValues_market[index] || ""; // Default to empty string if no value
        $(this).text(filterValue);
    });

     fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));

    secmorefilterview(fitterremovedata,leadsadataUrl,moredatefittervale);

});

$(document).on("click", ".value_views_mysearch", function () {
    var moredatefittervale = $('#mydateallviews').text();
    var morefillterremvedata = $(this).text().replace(/, /g, ",");
    var clear_filtr = $(this).attr('id');
    $(this).text("");
   if(clear_filtr == 'myzone_search'){
        $('#sec_loc_views').val('');
    }
    if(clear_filtr == 'mybranch_search'){
        $('#sec_zone_views').val('');
    }
    if(clear_filtr == 'leadmy_search'){
        $('#sec_loc_views').val('');
    }
   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
      secmorefilterview(fitterremovedata,leadsadataUrl,moredatefittervale);
});

$(document).on("click", ".my_value_views", function () {
	var moredatefittervale = $('#mydateallviews').text();
    var morefillterremvedata = $(this).text().replace(/\s*-\s*/, '-').trim();

    $(this).text("");
    let indexToRemove = fitterremovedata.findIndex(item => item.includes(morefillterremvedata));

    if (indexToRemove !== -1) {
      // Remove the element at the found index
      var removedElement = fitterremovedata.splice(indexToRemove, 1)[0]; // splice returns an array, so we access the first element
    }

    // Split the string at the first '=' and get the part before it
        let key = removedElement.split('=')[0];

        if(key == 'security_details.sec_shift'){
            $('#shift_type').prop('selectedIndex', 0);
        }

   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
secmorefilterview(fitterremovedata,leadsadataUrl,moredatefittervale);

});

$(document).on("click", ".clear_my_views", function () {

    fitterremovedata.length = 0;
    $('.securitydatasearch').val("")
    $(".my_value_views").text("");
    $('.morefittersclr').val("");
    $('.clear_my_views').hide();
    $(".my_search_view").hide();
    $(".value_views_mysearch").text("");
    leads_fetch(2);
});


});
