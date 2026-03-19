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
activity_fetch();
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
   $('.dropdown-item-loc-act').on('click', function () {
        var selectedBranchName = $(this).text();
        var selectedBranchId = $(this).data('value');
        $('#act_zone_id').val(selectedBranchName);
        $('#act_zone_id').attr('data-value', selectedBranchId);
    });
   function formatTimeToHHMMSS(timeStr) {
    if (!timeStr) return '';
    return timeStr;
}
    function convertTo12Hour(timeStr) {
    const [hour, minute] = timeStr.split(':');
    const h = parseInt(hour);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hr12 = h % 12 || 12;
    return `${hr12}:${minute} ${ampm}`;
}

$('#submit-new-activites').click(function (event) {
      event.preventDefault();
         let isValid = true;
      if ($('#act_zone_id').val() === "") {
          $('.error_location').text('Please select the Location');
          isValid = false;
      }
      if ($('#act_days').val() === "") {
          $('.error_days').text('Please select the Days');
          isValid = false;
      }
      if ($('#camp_name').val() === "") {
          $('.error_campname').text('Enter the Camp Name');
          isValid = false;
      }
      if ($('#budget').val() === "") {
          $('.error_budget').text('Enter the Budget');
          isValid = false;
      }
       if ($('#login_time').val() === "") {
          $('.error_login').text('Enter the Login');
          isValid = false;
      }
      if ($('#logout').val() === "") {
          $('.error_logout').text('Enter the Logout');
          isValid = false;
      }
      if ($('#activity_des').val() === "") {
          $('.error_feedback').text('Enter the Acivity Description');
          isValid = false;
      }
      if (!isValid) {
          return;
      }
      let formData = new FormData();
      const loginTime = formatTimeToHHMMSS($('#login_time').val());
      const logoutTime = formatTimeToHHMMSS($('#logout').val());
    formData.append('campa_login_time', loginTime);
    formData.append('campa_logout_time', logoutTime);
      formData.append('campa_days', $('#act_days').val());
      formData.append('campa_name', $('#camp_name').val());
      formData.append('camp_id', $('#camp_id').val());
    //   formData.append('campa_zone_id', $('#act_zone_id').val());
      formData.append('campa_zone_id', $('#act_zone_idssss').val());
      formData.append('campa_budget', $('#budget').val());
      formData.append('campa_loc_track', $('#location_track').val());
      formData.append('campa_description', $('#activity_des').val());
        // let zoneId = $('#act_zone_id').attr('data-value');
        //   formData.append('campa_zone_id', zoneId);
          // Add images to FormData
          const notes = $('#image_uploads_notes')[0].files;
          for (let i = 0; i < notes.length; i++) {
              formData.append('imagesnotes[]', notes[i]);
          }
      const files = $('#image_uploads_banner')[0].files;
      for (let i = 0; i < files.length; i++) {
          formData.append('imagesbanner[]', files[i]);
      }
      formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
       console.log(formData);
      $.ajax({
          url: newactivitedatasave,
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
           success: function (response) {
               if (response.success) {
                window.location.href = '/superadmin/campactivities';
                //  window.dispatchEvent(new CustomEvent('swal:toast', {
                //      detail: {
                //        title:'Info!',
                //        text: response.message,
                //        icon: 'success',
                //        background: 'success',
                //      }
                //  }));
                //    document.getElementById('analytics-tab-2').click();
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

                   $("#exampleModal2").modal('hide');
                    activity_fetch();
          },
          error: function (error) {
              console.error(error.responseJSON);
          },
      });
  });




  $('#submit-activites').click(function (event) {
      event.preventDefault();
         let isValid = true;
      if ($('#act_zone_id').val() === "") {
          $('.error_location').text('Please select the Location');
          isValid = false;
      }
      if ($('#act_days').val() === "") {
          $('.error_days').text('Please select the Days');
          isValid = false;
      }
      if ($('#camp_name').val() === "") {
          $('.error_campname').text('Enter the Camp Name');
          isValid = false;
      }
      if ($('#budget').val() === "") {
          $('.error_budget').text('Enter the Budget');
          isValid = false;
      }
       if ($('#login_time').val() === "") {
          $('.error_login').text('Enter the Login');
          isValid = false;
      }
      if ($('#logout').val() === "") {
          $('.error_logout').text('Enter the Logout');
          isValid = false;
      }
      if ($('#activity_des').val() === "") {
          $('.error_feedback').text('Enter the Acivity Description');
          isValid = false;
      }
      if (!isValid) {
          return;
      }
      let formData = new FormData();
      const loginTime = formatTimeToHHMMSS($('#login_time').val());
      const logoutTime = formatTimeToHHMMSS($('#logout').val());
    formData.append('campa_login_time', loginTime);
    formData.append('campa_logout_time', logoutTime);
      formData.append('campa_days', $('#act_days').val());
      formData.append('campa_name', $('#camp_name').val());
      formData.append('camp_id', $('#camp_id').val());
    //   formData.append('campa_zone_id', $('#act_zone_id').val());
      formData.append('campa_zone_id', $('#act_zone_idssss').val());
      formData.append('campa_budget', $('#budget').val());
      formData.append('campa_loc_track', $('#location_track').val());
      formData.append('campa_description', $('#activity_des').val());
        // let zoneId = $('#act_zone_id').attr('data-value');
        //   formData.append('campa_zone_id', zoneId);
          // Add images to FormData
          const notes = $('#image_uploads_notes')[0].files;
          for (let i = 0; i < notes.length; i++) {
              formData.append('imagesnotes[]', notes[i]);
          }
      const files = $('#image_uploads_banner')[0].files;
      for (let i = 0; i < files.length; i++) {
          formData.append('imagesbanner[]', files[i]);
      }
      formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
       console.log(formData);
      $.ajax({
          url: activitedatasave,
          type: "POST",
          data: formData,
          processData: false,
          contentType: false,
           success: function (response) {
               if (response.success) {
                window.location.href = '/superadmin/campactivities';
                //  window.dispatchEvent(new CustomEvent('swal:toast', {
                //      detail: {
                //        title:'Info!',
                //        text: response.message,
                //        icon: 'success',
                //        background: 'success',
                //      }
                //  }));
                //    document.getElementById('analytics-tab-2').click();
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

                   $("#exampleModal2").modal('hide');
                    activity_fetch();
          },
          error: function (error) {
              console.error(error.responseJSON);
          },
      });
  });



    $('#itemsPerPageactivites').change(function() {
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
    '<td class="tdview" id="idfetch" data-id="' + user.campa_id + '"> <strong>#' + user.campa_id + '</strong><br>' + formattedDate + '<br></td>' +
    '<td class="tdview">' + user.camp_loc_name +
    // '<td class="tdview">' + user.camp_loc_name + '<br>' + user.zone_name + '<br></td>' +
    '<td class="tdview">' + user.campa_days + '</td>' +
    '<td class="tdview">' + user.campa_name + '</td>' +
    '<td class="tdview">' + user.campa_budget + '</td>';

if (user.campa_notes_img && user.campa_notes_img !='[]') {
    body += '<td class="tdview"><a href="#" class="imagenote">Notes Images</a></td>' +
            '<td class="tdview imgnote" style="display:none">' + user.campa_notes_img + '</td>';
} else {
    body += '<td class="tdview" style="text-align:center;"><a href="#">-</a></td>' +
            '<td class="tdview imgnote" style="display:none">-</td>';
}

if (user.campa_banner_img && user.campa_banner_img !='[]') {
    body += '<td class="tdview"><a href="#" class="imagebanner">Banner Images</a></td>' +
            '<td class="tdview imgbanner" style="display:none">' + user.campa_banner_img + '</td>';
} else {
    body += '<td class="tdview" style="text-align:center;"><a href="#">-</a></td>' +
            '<td class="tdview imgbanner" style="display:none">-</td>';
}
body += '<td class="tdview">' + user.campa_login_time + '</td>' +
        '<td class="tdview">' + user.campa_logout_time + '</td>' +
        '<td class="tdview">' + user.campa_loc_track + '</td>' +
        '<td class="tdview">' + user.campa_description + '</td>' +
        '<td class="tdview">' + user.created_by + '</td>' +
    '</tr>';
  });
                if (pageData.length === 0) {
			body += '<tr><td colspan="13" class="tdview" style="text-align: center;">No data available</td></tr>';
		}
    $("#activites_details").html(body);
    $("#actcount").text(totalItems);
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

    $('#paginateactivites').html(paginationHtml);

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
        var moredatefittervalact = $('#mydateviewsallsave').text();
        ticketdatefillterrange(moredatefittervalact,fitterremovedata);

    } else if ($(this).hasClass('applyBtn')) {

        var datefilltervaluenew = $('.drp-selected').text(); // Get the current text value when '.applyBtn' is clicked
        var dateRange = datefilltervaluenew.split(' - ');
        function convertDateFormat(dateStr) {
            let parts = dateStr.split('/');
            return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY
        }
        var startDate = convertDateFormat(dateRange[0]);
        var endDate = convertDateFormat(dateRange[1]);
        var moredatefittervalact = `${startDate} - ${endDate}`;
        ticketdatefillterrange(moredatefittervalact,fitterremovedata);

    }

});


function activity_fetch() {
		var moredatefittervalact = $('#campactivitesdate').text();
        $(".activites_views").text("");
		$("#my_act_details1").show();
		$.ajax({
			url: activitydataUrl,
			type: "GET",
			data: {
				moredatefittervalact: moredatefittervalact,
			},
			success: function (responseData) {
				handleSuccess(responseData);
			},
			error: function (xhr, status, error) {
				console.error("AJAX Error:", status, error);
			}
		});
	}
    function actmorefilterview(uniqueResults,url,moredatefittervalact) {
        if(uniqueResults!="")
           {
           var morefilltersallact=uniqueResults.join(" AND ");
           $.ajax({
               url: url,
               type: "GET",
               data: {
                   morefilltersallact: morefilltersallact,
                   moredatefittervalact:moredatefittervalact,
               },
               success: function (responseData) {
                   handleSuccess(responseData);
               },
               error: function (xhr, status, error) {
                   $("#my_act_details1").hide();
                   console.error("AJAX Error:", status, error);
               }
           });
           }else
          {
            $('.clear_activites_views').hide();
            $(".search_activites").hide();
            activity_fetch(2);

          }
    }
    function handleSuccess(responseData) {
                $("#my_act_details1").hide();
                $("#document_tbl").show();
                dataSourcedocument = responseData;
                totalItems = responseData.length;
                var pageSizedocuments = parseInt($('#itemsPerPageactivites').val()); // Get selected items per page
                renderPaginationdocument(dataSourcedocument, pageSizedocuments);
                renderTabledocument(dataSourcedocument, pageSizedocuments, 1); // Show first page initially

    }

    var fitterremovedata = [];
    function ticketdatefillterrange(moredatefittervalact,fitterremovedata) {
    currentFilter = moredatefittervalact;
	var morefilltersallact=fitterremovedata.join(" AND ");

    $("#document_tbl").show();
    $.ajax({
        url: activitydataUrl,
        type: "GET",
        data: {
            moredatefittervalact: currentFilter,
			morefilltersallact: morefilltersallact,
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

   $(document).on('click', '.act_options_marketers div', function () {
    $(".my_value_views").text("");
      $('.clear_activites_views').show();
    $(".search_activites").show();
    var moredatefittervalact = $('#campactivitesdate').text();
    let resultsArray_marketer = [];
    $(".actdatasearch").each(function () {

        const value = $(this).val();
        if (value !== "") {
            const result =  $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(result);
        }
    });
    const moreFilterValues_market = [
        $("#act_zone_views").val(),
        $("#act_loc_views").val(),
         $('#act_name_views').val(),
    ];
    $(".activites_views").each(function (index) {
        const filterValue = moreFilterValues_market[index] || ""; // Default to empty string if no value
        $(this).text(filterValue);
    });
     fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));
    actmorefilterview(fitterremovedata,activitydataUrl,moredatefittervalact);
});

$(document).on("click", ".activites_views", function () {
    var moredatefittervalact = $('#campactivitesdate').text();
    var morefillterremvedata = $(this).text().replace(/, /g, ",");

    var clear_filtr = $(this).attr('id');
    $(this).text("");
    if(clear_filtr == 'actbranch_search'){
        $('#act_loc_views').val('');
    }
    if(clear_filtr == 'actzone_search'){
        $('#act_zone_views').val('');
         $('#act_zone_id').val("");
    }
    if(clear_filtr == 'act_campname'){
        $('#act_name_views').val('');
    }
   // Update the uniqueResults array to remove the corresponding filter
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
      actmorefilterview(fitterremovedata,activitydataUrl,moredatefittervalact);
});

$(document).on("click", ".my_value_views", function () {
	var moredatefittervalact = $('#campactivitesdate').text();
    var morefillterremvedata = $(this).text().replace(/\s*-\s*/, '-').trim();

    $(this).text("");
    let indexToRemove = fitterremovedata.findIndex(item => item.includes(morefillterremvedata));

    if (indexToRemove !== -1) {
      var removedElement = fitterremovedata.splice(indexToRemove, 1)[0];
    }
        let key = removedElement.split('=')[0];

        if(key == 'security_details.sec_shift'){
            $('#shift_type').prop('selectedIndex', 0);
        }
   fitterremovedata = fitterremovedata.filter(function (item) {
    return !item.trim().includes(morefillterremvedata.trim() + "'");
});
actmorefilterview(fitterremovedata,activitydataUrl,moredatefittervalact);
});


$(document).on("click", ".clear_activites_views", function () {
    fitterremovedata.length = 0;
    $('.actdatasearch').val("")
    $(".my_value_views").text("");
    $('.morefittersclr').val("");
    $('.clear_activites_views').hide();
    $(".search_activites").hide();
    $(".activites_views").text("");
    $('#act_zone_id').val("");
    activity_fetch(2);
});

 // IMAGE NOTE HANDLER
$(document).on('click', '.imagenote', function (e) {
    $('#noteimagepopup').modal('show');
    var row = $(this).closest('tr');
    var imageviews = row.find('.imgnote').text().trim();

    imageviews = imageviews.replace(/[\[\]"]/g, ''); // Remove brackets and quotes
    var imageArray = imageviews.split(',').map(item => item.trim());

    if (imageArray.length > 0) {
        var firstImage = encodeURIComponent(imageArray[0].split('/').pop());
        $('#main_notes').attr('src', '../camp_activites/' + firstImage);
    }

    var views = '';
    imageArray.forEach(function (image) {
        var imageName = encodeURIComponent(image.split('/').pop());
        views += '<img src="../camp_activites/' + imageName + '" style="width:80px; margin:5px; cursor:pointer;">';
    });

    $('#thumbnails_notes').html(views);

    // Add click event for thumbnail images
    $('#thumbnails_notes img').on('click', function () {
        $('#main_notes').attr('src', $(this).attr('src'));
    });
});

// IMAGE BANNER HANDLER
$(document).on('click', '.imagebanner', function (e) {
    $('#bannerimagepopup').modal('show');
    var row = $(this).closest('tr');
    var imageviews = row.find('.imgbanner').text().trim();

    imageviews = imageviews.replace(/[\[\]"]/g, ''); // Remove brackets and quotes
    var imageArray = imageviews.split(',').map(item => item.trim());

    if (imageArray.length > 0) {
        var firstImage = encodeURIComponent(imageArray[0].split('/').pop());
        $('#main_banner').attr('src', '../camp_activites/' + firstImage);
    }

    var views = '';
    imageArray.forEach(function (image) {
        var imageName = encodeURIComponent(image.split('/').pop());
        views += '<img src="../camp_activites/' + imageName + '" style="width:80px; margin:5px; cursor:pointer;">';
    });

    $('#thumbnails_banner').html(views);

    // Add click event for thumbnail images
    $('#thumbnails_banner img').on('click', function () {
        $('#main_banner').attr('src', $(this).attr('src'));
    });
});


});
