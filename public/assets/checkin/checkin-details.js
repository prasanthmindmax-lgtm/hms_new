$(document).ready(function() {	
    overall_fetch(1);
    last_next_visit_fetch(1);

    $(".csearch_view").hide();
    $(".next_search_view").hide();
    $(".last_search_view").hide();
	var fitterremovedata = [];	
	var totalItems;	
	var activeTabId = "";
	// Listen for tab change event
	$('#myTab').on('shown.bs.tab', function (e) {
	  // Get the ID of the active tab
	   activeTabId = $(e.target).attr('id'); // This gets the ID of the newly activated tab	  
	});
	
function morefilterview(uniqueResults, moredatefittervale, urlstatus, url,apistatus) {
	if (apistatus == 'checkinlastappt') {
		$('#next_details11').show();
		$("#next_details").hide();
	}else{
		$('#daily_details11').show();
		$("#daily_details").hide();
	}
	 if(uniqueResults!="")
    {
    var morefilltersall=uniqueResults.join(" AND ");

    $.ajax({
        url: url,
        type: "GET",
        data: {
            morefilltersall: morefilltersall,
            moredatefittervale:moredatefittervale,
            apistatus:apistatus,   
        },
        success: function (responseData) {
			if (apistatus === 'checkinlastappt') {
				$('#next_details11').hide();
				handleSuccess(responseData, 6);
			} else {
				$('#daily_details11').hide();
			  var locations = responseData.dropdown || responseData; 
				var container =$(".options_branch.brachviewsall");
				container.empty(); 
				var doctor = responseData.doctor_name || responseData; 
				var doctor_container =$(".options_branch.doctorviewsall");
				doctor_container.empty(); 
				doctor.forEach(function(doc) {					
						var options = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', doc.id)
							.text(doc.doctor_name);
						doctor_container.append(options);					
				});
				locations.forEach(function(location) {
					if (location.status === 1) { 
						var option = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', location.id)
							.text(location.name);
						container.append(option);
					}
				});
				var hrm_user = responseData.hrm_users || responseData; 
				var hrm_container =$(".options_branch.cc_name_view");
				hrm_container.empty(); 
				hrm_user.forEach(function(hrm) {					
						var hptions = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', hrm.employment_id)
							.text(hrm.fullname);
						hrm_container.append(hptions);					
				});
				handleSuccess(responseData,urlstatus);
			}
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
	}else
   {
	if(urlstatus == 1){
      $(".csearch_view").hide();
	  $('.clear_views').hide();
      overall_fetch(2);
	}
   }
}	

	// date fitter function
function ticketdatefillterrange(datefiltervalue,url,urlstatus,fitterremovedata,apistatus) {
    currentFilter = datefiltervalue;
	var morefilltersall=fitterremovedata.join(" AND ");
	if(urlstatus == 1){
		$("#daily_details11").show();
		$("#daily_details").hide();
	}
	if(urlstatus == 4){
		$("#all_details11").show();
		$("#all_details").hide();
	}
	if(urlstatus == 6){
		$("#next_details11").show();
		$("#next_details").hide();
	}
    $.ajax({
        url: url,
        type: "GET",
        data: {
            datefiltervalue: currentFilter,
			morefilltersall: morefilltersall,
			apistatus:apistatus,
        },
        success: function (responseData) {
           handleSuccess(responseData,urlstatus);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
	
function  morefilltersremoveviews(fitterremovedata,url,urlstatus,datefilltervalue)
{

    if(fitterremovedata!="")
    {
        var fitterremovedataall=fitterremovedata.join(" AND ");
   $.ajax({
    url: url,
    type: "GET",
    data: {
        fitterremovedataall: fitterremovedataall,
		moredatefittervale:datefilltervalue,

    },
    success: function (responseData) {
       handleSuccess(responseData,urlstatus);
    },
    error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
    }
  });

   }else
   {
	   if(urlstatus == 1){
		  $(".csearch_view").hide();
		  $('.clear_views').hide();
		  overall_fetch(2);
		}
   }

}

	var ticketdataSource = [];  // Data will be fetched here
	var ticketdataSource1 = [];  // Data will be fetched here

	// Fetch the data and initialize pagination
	function overall_fetch(statusid) {
		
		var moredatefittervale = $('#dateallviews').text();
		$(".value_views_mainsearch").text("");
		$('#daily_details11').show();
		//return;
		$.ajax({
			//url: checkfetchUrl,
			url: checkinfetchUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid,
				apistatus:'checkinreport'
			},
			success: function (responseData) {
				$('#daily_details11').hide();
				var locations = responseData.dropdown || responseData; 
				var container =$(".options_branch.brachviewsall");
				container.empty(); 
				var hrm_user = responseData.hrm_users || responseData; 
				var hrm_container =$(".options_branch.cc_name_view");
				hrm_container.empty(); 
				hrm_user.forEach(function(hrm) {					
						var hptions = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', hrm.employment_id)
							.text(hrm.fullname);
						hrm_container.append(hptions);					
				});
				var doctor = responseData.doctor_name || responseData; 
				var doctor_container =$(".options_branch.doctorviewsall");
				doctor_container.empty(); 
				doctor.forEach(function(doc) {					
						var options = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', doc.id)
							.text(doc.doctor_name);
						doctor_container.append(options);					
				});
				locations.forEach(function(location) {
					if (location.status === 1) { 
						var option = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', location.id)
							.text(location.name);
						container.append(option);
					}
				}); 				
				handleSuccess(responseData,1);
			},
			error: function (xhr, status, error) {
				console.error("AJAX Error:", status, error);
			}
		});
	}// Fetch the data and initialize pagination
	
	function last_next_visit_fetch(statusid) {
		
		var moredatefittervale = $('#lastdateviewsall').text();
		$(".value_views_mainsearch").text("");
		$('#all_details11').show();
		//return;
		$.ajax({
			//url: checkfetchUrl,
			url: checkinLastFetch,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid,
				apistatus:'checkinreport'
			},
			success: function (responseData) {
				$('#all_details11').hide();				
				$('#next_details11').hide();				
				handleSuccess(responseData,4);
				handleSuccess(responseData,6);
			},
			error: function (xhr, status, error) {
				console.error("AJAX Error:", status, error);
			}
		});
	}
			
function handleSuccess(responseData,urlstatus) {
	if(urlstatus == 1){		
			$("#daily_details11").hide();
			$("#daily_details").show();
			ticketdataSource1 = responseData; 
			totalItems = responseData.length; 
			var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
			ticketrenderPagination(ticketdataSource1, ticketpageSize);
			ticketrenderTable(ticketdataSource1, ticketpageSize, 1); // Show first page initially
	}else if(urlstatus == 2){
			$("#nxt_appt_details").show();
			ticketdataSource = responseData; 
			totalItems = responseData.length; 
			var ticketpageSize = parseInt($('#reportPerPageSelect3').val()); // Get selected items per page
			prerenderPagination(ticketdataSource, ticketpageSize);
			prerenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
	}else if(urlstatus == 3){
			$("#upp_appt_details").show();
			ticketdataSource = responseData; 
			totalItems = responseData.length; 
			var ticketpageSize = parseInt($('#reportPerPageSelect4').val()); // Get selected items per page
			upprenderPagination(ticketdataSource, ticketpageSize);
			upprenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
	}else if(urlstatus == 4){			
			$("#all_details11").hide();
			$("#all_details").show();
			ticketdataSource = responseData; 
			totalItems = responseData.length; 
			var ticketpageSize = parseInt($('#lastitemsPerPageSelect').val()); // Get selected items per page
			lastrenderPagination(ticketdataSource, ticketpageSize);
			lastrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
	}else if(urlstatus == 6){
			$("#next_details11").hide();
			$("#next_details").show();
			ticketdataSource = responseData; 
			totalItems = responseData.length; 
			var ticketpageSize = parseInt($('#nextitemsPerPageSelect').val()); // Get selected items per page
			nextrenderPagination(ticketdataSource, ticketpageSize);
			nextrenderTable(ticketdataSource, ticketpageSize, 1);
	}else if(urlstatus == 8){
			$("#amt_details").show();
			ticketdataSource = responseData; 
			totalItems = responseData.length; 
			var ticketpageSize = parseInt($('#amtPerPageSelect').val()); // Get selected items per page
			amtrenderPagination(ticketdataSource, ticketpageSize);
			amtrenderTable(ticketdataSource, ticketpageSize, 1);
	}else if(urlstatus == 9){
			$("#pharmacy_details").show();
			ticketdataSource = responseData; 
			totalItems = responseData.length; 
			var ticketpageSize = parseInt($('#pharmacyPerPageSelect').val()); // Get selected items per page
			pharrenderPagination(ticketdataSource, ticketpageSize);
			pharrenderTable(ticketdataSource, ticketpageSize, 1);
	}
	else{
		ticketdataSource = responseData; 
		totalItems = responseData.length; 
		var ticketpageSize = parseInt($('#reportPerPageSelect').val()); 
		reportrenderPagination(ticketdataSource, ticketpageSize);
		reportrenderTable(ticketdataSource, ticketpageSize, 1)
	}
}
 let lastFocusedInput = null;

    $(document).on('focus', '.searchLocation', function () {
        lastFocusedInput = this.id;
    });
	
$(document).on('click', '.options_branch div', function (e) {
		 //return;
        // Get the selected value and text
        var selectedValue = $(this).data('value');
        var selectedText = $(this).text();
		$(".value_views").text("");
        var moredatefittervale = $('#dateallviews').text();
		if (lastFocusedInput === 'czone_views') {
            $('#cbranch_views').val(''); 
            $('#cc_doctor_name').val(''); 
            $('#name_cc_audit').val(''); 
        }
		
        $('.clear_views').show();
        $(".csearch_view").show();
        
		var resultsArray_marketer = [];
        $(".checkvalues_search").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing
            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(results);

            var moreFilterValues_market = [
                $("#czone_views").val(),
                $("#cbranch_views").val(),
                $("#ctrt_category").val(),
                $("#ctreatment_stage").val(),
                $("#pt_source_id").val(),
                $("#name_cc_audit").val(),
                $("#cc_audit_name").val(),
                $("#cc_doctor_name").val(),
                $("#cc_patient_age").val(),
            ];

            $(".value_views_mainsearch").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });
                marketersearchvalue=resultsArray_marketer;
                fitterremovedata=resultsArray_marketer;
				fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata,moredatefittervale,1,checkinBranchfitter,"checkinreport");
        });	
		var fitterremovedata1 = [];
	$(document).on('click', '.locations_branch div', function (e) {
		 //return;
        // Get the selected value and text
        var selectedValue = $(this).data('value');
        var selectedText = $(this).text();
		$(".value_views").text("");
        var moredatefittervale = $('#nextdateviewsall').text();
		$('.next_search_view').show();
        $(".clear_next_views").show();        
		var resultsArray_marketer = [];
        $(".locations_search").each(function () {
            var value = $(this).val();
            // Check if the value is not empty before processing
            if (value === "") {
                return; // Skip this iteration if the value is empty
            }
            var results = $(this).attr('name') + "='" + value + "'";
            resultsArray_marketer.push(results);
			
			var moreFilterValues_market = [
                $("#location_views").val(),
            ];

            $(".value_views_next").each(function (index) {
                var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; // Use "N/A" if value is empty
                $(this).text(morefillterdata_market);
            });

            });
                marketersearchvalue=resultsArray_marketer;
                fitterremovedata1=resultsArray_marketer;
				fitterremovedata1 = fitterremovedata1.map(filter => filter.replace(/, /g, ','));
                morefilterview(fitterremovedata1,moredatefittervale,1,checkinLastFetch,"checkinlastappt");
        });
	

// Handle items per page change
$('#itemsPerPageSelect').change(function() {
    var ticketpageSize = parseInt($(this).val());
    ticketrenderPagination(ticketdataSource1, ticketpageSize);
    ticketrenderTable(ticketdataSource1, ticketpageSize, 1);  // Initially show the first page
});

$('#reportPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        reportrenderPagination(ticketdataSource, ticketpageSize);
        reportrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
	
$('#reportPerPageSelect3').change(function() {
        var ticketpageSize = parseInt($(this).val());
        prerenderPagination(ticketdataSource, ticketpageSize);
        prerenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
	
$('#reportPerPageSelect4').change(function() {
        var ticketpageSize = parseInt($(this).val());
        upprenderPagination(ticketdataSource, ticketpageSize);
        upprenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
	
$('#lastitemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        lastrenderPagination(ticketdataSource, ticketpageSize);
        lastrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
$('#nextitemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        nextrenderPagination(ticketdataSource, ticketpageSize);
        nextrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
$('#amtPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        amtrenderPagination(ticketdataSource, ticketpageSize);
        amtrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
$('#pharmacyPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        pharrenderPagination(ticketdataSource, ticketpageSize);
        pharrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
/*
function ticketrenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.page-bttn.active').data('page')) || 1;

    var visiblePages = 10;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    // Previous button
    paginationHtml += '<button class="page-bttn prev" style="background-color:#6a6ee4;color: #fff;" ' + (currentPage === 1 ? 'disabled' : '') + ' data-page="' + (currentPage - 1) + '">Previous</button>';

    // Page buttons
    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="page-bttn" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    // Ellipsis and last page button
    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="page-bttn" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }

    if (endPage < totalPages) {
        paginationHtml += '<button class="page-bttn" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    // Next button
    paginationHtml += '<button class="page-bttn next" style="background-color:#6a6ee4;color: #fff;" ' + (currentPage === totalPages ? 'disabled' : '') + ' data-page="' + (currentPage + 1) + '">Next</button>';

    $('#ticketpagination').html(paginationHtml);

    $('.page-bttn').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.page-bttn').removeClass('active');
            $(this).addClass('active');
            ticketrenderTable(data, ticketpageSize, pageNum);
            ticketrenderPagination(data, ticketpageSize); 
        }
    });
}*/
function pharrenderPagination(response, ticketpageSize) {
	var data = response;

    if (!Array.isArray(data)) {
        console.error("No valid data array found for pagination.");
        return;
    }
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.pge-pnxt.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="pge-pnxt" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="pge-pnxt" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }
	
    if (endPage < totalPages) {
        paginationHtml += '<button class="pge-pnxt" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#pharmacypagination').html(paginationHtml);
    $('.pge-pnxt').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.pge-pnxt').removeClass('active');
            $(this).addClass('active');
            pharrenderTable(data, ticketpageSize, pageNum);
            pharrenderPagination(data, ticketpageSize); 
        }
    });
	$('.pge-pnxt[data-page="' + currentPage + '"]').addClass('active');
}
function amtrenderPagination(response, ticketpageSize) {
	var data = response;

    if (!Array.isArray(data)) {
        console.error("No valid data array found for pagination.");
        return;
    }
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.pge-nxt.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="pge-nxt" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="pge-nxt" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }
	
    if (endPage < totalPages) {
        paginationHtml += '<button class="pge-nxt" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#amtpagination').html(paginationHtml);
    $('.pge-nxt').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.pge-nxt').removeClass('active');
            $(this).addClass('active');
            amtrenderTable(data, ticketpageSize, pageNum);
            amtrenderPagination(data, ticketpageSize); 
        }
    });
	$('.pge-nxt[data-page="' + currentPage + '"]').addClass('active');
}
function nextrenderPagination(response, ticketpageSize) {
	var data = Array.isArray(response?.next_data) ? response.next_data : response;

    if (!Array.isArray(data)) {
        console.error("No valid data array found for pagination.");
        return;
    }
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.page-nxt.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="page-nxt" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="page-nxt" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }
	
    if (endPage < totalPages) {
        paginationHtml += '<button class="page-nxt" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#nxtpagination').html(paginationHtml);
    $('.page-nxt').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.page-nxt').removeClass('active');
            $(this).addClass('active');
            nextrenderTable(data, ticketpageSize, pageNum);
            nextrenderPagination(data, ticketpageSize); 
        }
    });
	$('.page-nxt[data-page="' + currentPage + '"]').addClass('active');
}
function lastrenderPagination(response, ticketpageSize) {
	var data = Array.isArray(response?.last_data) ? response.last_data : response;

    if (!Array.isArray(data)) {
        console.error("No valid data array found for pagination.");
        return;
    }
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.page-lst.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="page-lst" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="page-lst" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }
	
    if (endPage < totalPages) {
        paginationHtml += '<button class="page-lst" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#lastticketpagination').html(paginationHtml);
    $('.page-lst').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.page-lst').removeClass('active');
            $(this).addClass('active');
            lastrenderTable(data, ticketpageSize, pageNum);
            lastrenderPagination(data, ticketpageSize); 
        }
    });
	$('.page-lst[data-page="' + currentPage + '"]').addClass('active');
}
function upprenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.page-bup.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="page-bup" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="page-bup" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }
	
    if (endPage < totalPages) {
        paginationHtml += '<button class="page-bup" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#reportpagination4').html(paginationHtml);
    $('.page-bup').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.page-bup').removeClass('active');
            $(this).addClass('active');
            upprenderTable(data, ticketpageSize, pageNum);
            upprenderPagination(data, ticketpageSize); 
        }
    });
	$('.page-bup[data-page="' + currentPage + '"]').addClass('active');
}
function prerenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.page-btp.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="page-btp" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="page-btp" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }
	
    if (endPage < totalPages) {
        paginationHtml += '<button class="page-btp" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#reportpagination3').html(paginationHtml);
    $('.page-btp').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.page-btp').removeClass('active');
            $(this).addClass('active');
            prerenderTable(data, ticketpageSize, pageNum);
            prerenderPagination(data, ticketpageSize); 
        }
    });
	$('.page-btp[data-page="' + currentPage + '"]').addClass('active');
}

function reportrenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';
    
    var currentPage = parseInt($('.page-btn.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="page-btn" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="page-btn" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }
	
    if (endPage < totalPages) {
        paginationHtml += '<button class="page-btn" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#reportpagination').html(paginationHtml);
    $('.page-btn').click(function() {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.page-btn').removeClass('active');
            $(this).addClass('active');
            reportrenderTable(data, ticketpageSize, pageNum);
            reportrenderPagination(data, ticketpageSize); 
        }
    });
	$('.page-btn[data-page="' + currentPage + '"]').addClass('active');
}

function ticketrenderPagination(response, ticketpageSize) {
    var data = Array.isArray(response?.data) ? response.data : response;

    if (!Array.isArray(data)) {
        console.error("No valid data array found for pagination.");
        return;
    }

    var totalItems = data.length;
    var totalPages = Math.ceil(totalItems / ticketpageSize);
    var paginationHtml = '';

    var currentPage = parseInt($('.page-bttn.active').data('page')) || 1;

    var visiblePages = 5;  
    var startPage, endPage;

    if (totalPages <= visiblePages) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= Math.ceil(visiblePages / 2)) {
            startPage = 1;
            endPage = visiblePages;
        } else if (currentPage + Math.floor(visiblePages / 2) >= totalPages) {
            startPage = totalPages - visiblePages + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - Math.floor(visiblePages / 2);
            endPage = currentPage + Math.floor(visiblePages / 2);
        }
    }

    for (var i = startPage; i <= endPage; i++) {
        paginationHtml += '<button class="page-bttn" style="background-color:#6a6ee4;color: #fff;" data-page="' + i + '">' + i + '</button>';
    }

    if (endPage < totalPages - 1) {
        paginationHtml += '<button class="page-bttn" style="background-color:#6a6ee4;color: #fff;" disabled>...</button>';
    }

    if (endPage < totalPages) {
        paginationHtml += '<button class="page-bttn" style="background-color:#6a6ee4;color: #fff;" data-page="' + totalPages + '">' + totalPages + '</button>';
    }

    $('#ticketpagination').html(paginationHtml);
    $("#dcounts").text(totalItems);

    $('.page-bttn').click(function () {
        var pageNum = $(this).data('page');
        if (pageNum) {
            $('.page-bttn').removeClass('active');
            $(this).addClass('active');
            ticketrenderTable(data, ticketpageSize, pageNum);
            ticketrenderPagination(data, ticketpageSize); 
        }
    });

    $('.page-bttn[data-page="' + currentPage + '"]').addClass('active');
}

function reportrenderTable(response, ticketpageSize, pageNum) {
	var data = Array.isArray(response?.data) ? response.data : response;

		if (!Array.isArray(data)) {
			console.error("No valid data array found in response.");
			return;
		}
		$("#time_details").show();
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
		var body = "";
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {				
				let dateStr = usr.billdate;
				let formattedDate =moment(dateStr, "YYYYMMDDHH:mm:ss").format("DD MMM YYYY");
				body += '<tr onclick="rowClick(event)">' +
					   '<td class="tdview"><strong>#' + sno + '</strong><br>' + formattedDate +'</td>' +
					  '<td class="tdview">' + usr.consultant + '</td>' +
					  '<td class="tdview">' + usr.billtype + '</td>' +
					  '<td class="tdview" id="amount_view" data-amountpayable="' + usr.amountpayable + '" data-amountreceived="' + usr.amountreceived + '" data-prev_bal="' + usr.prev_balance + '" data-advance="' + usr.advance + '" data-billdate="' + usr.billdate + '" data-mrd="' + usr.phid + '" data-billno="' + usr.bill_no + '" data-billtype="' + usr.billtype + '"  data-paymenttype="' + usr.paymenttype + '" style="cursor: pointer;color: rgb(16 35 255);">' + usr.bill_no + '</td>' +
					  '<td class="tdview">' + usr.services + '</td>' +
					  '<td class="tdview">' + usr.paymenttype + '</td>' +
						'<td class="tdview">0.00</td>' +	
					  '<td class="tdview">' + usr.prev_balance + '</td>' +				
					  '<td class="tdview">' + usr.amountpayable + '</td>' +						
					  '<td class="tdview">' + usr.amountreceived + '</td>' +						
				   '</tr>';						
					   '</tr>';
					   sno++;
			});
		}

		$("#time_details").html(body);
		$("#mycounts").text(totalItems);
	}

	function lastrenderTable(response, ticketpageSize, pageNum) {
		var data = Array.isArray(response?.last_data) ? response.last_data : response;

		if (!Array.isArray(data)) {
			console.error("No valid data array found in response.");
			return;
		}
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
		var body = "";
		let lastdata = last_next_data(pageData,body);		

		$("#all_details").html(lastdata);
		$("#lastcounts").text(data.length);
	}
	
	function nextrenderTable(response, ticketpageSize, pageNum) {
		var data = Array.isArray(response?.next_data) ? response.next_data : response;

		if (!Array.isArray(data)) {
			console.error("No valid data array found in response.");
			return;
		}
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
		
		var body = "";
		let lastdata = last_next_data(pageData,body);		

		$("#next_details").html(lastdata);
		$("#nextcounts").text(data.length);
	}
	
	function last_next_data(pageData,body){
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
				const phid = usr.patientkey && usr.patientkey.includes("ivf") ? usr.patientkey.split("ivf")[1] : "-";;
				let dateStr = usr.appointmentdate;
				let dob = usr.dob;		
				let formattedDate = moment(dateStr).format("DD MMM YYYY");
					dob = dob ? moment(dob).format("DD MMM YYYY") : "-";
				body += '<tr onclick="rowClick(event)">' +
					   '<td class="tdview"><strong>#' + sno + '</strong></td>' +
					    '<td class="tdview">' + phid +'</td>' +
					    '<td class="tdview">Tamilselvi</td>' +
					    '<td class="tdview">' + formattedDate +'</td>' +
						'<td class="tdview"> 00:00</td>' +
						'<td class="tdview">Consultation</td>' +
						'<td class="tdview">DR. Aravind</td>' +				
						'<td class="tdview">Keerthika R</td>' +				
				   '</tr>';						
					   '</tr>';
					   sno++;
			});
		}
		return body;
	}
	
function amtrenderTable(data, ticketpageSize, pageNum) {	
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var	pageData = data.slice(startIdx, endIdx);
		const offset = startIdx; 
		var body = "";
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
			   const realIndex = offset + index;
			   if (realIndex === data.length - 1) return;
				body += '<tr onclick="rowClick(event)">' +
					   '<td class="tdview"><strong>#' + sno + '</strong></td>' +
					   '<td class="tdview">' + usr.name + '</td>' +
					    '<td class="tdview">' + usr.qty + '</td>' +
					    '<td class="tdview">' + usr.amt + '</td>' +			
					    '<td class="tdview">0.00</td>' +			
					    '<td class="tdview">0.00</td>' +			
					    '<td class="tdview">'+ usr.discountamt +'</td>' +			
					    '<td class="tdview">' + parseInt(usr.amt * usr.qty)+ '</td>' +			
				   '</tr>';						
					   '</tr>';
					   sno++;
			});
		}

		$("#amt_details").html(body);
	}
function pharrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var	pageData = data.slice(startIdx, endIdx);
		const offset = startIdx; 
		var body = "";
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
			   const realIndex = offset + index;
			   if (realIndex === data.length - 1) return;
				body += '<tr onclick="rowClick(event)">' +
					   '<td class="tdview"><strong>#' + sno + '</strong></td>' +
					   '<td class="tdview">' + usr.name + '</td>' +
					    '<td class="tdview">' + usr.batch + '</td>' +
					    '<td class="tdview">' + usr.expiry + '</td>' +
					    '<td class="tdview">' + usr.qty + '</td>' +
					    '<td class="tdview">' + usr.mrp + '</td>' +
					    '<td class="tdview">' + usr.taxpercentage + '</td>' +
					    '<td class="tdview">' + usr.discountpercentage + '</td>' +
					    '<td class="tdview">' + usr.prodvalue + '</td>' +			
				   '</tr>';						
					   '</tr>';
					   sno++;
			});
		}

		$("#pharmacy_details").html(body);
	}
	
function upprenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
		var body = "";
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
				
				let dateStr = usr.date;
				let dob = usr.dob;		
				let formattedDate = moment(dateStr).format("DD MMM YYYY");
					dob = dob ? moment(dob).format("DD MMM YYYY") : "-";
				let fullURL = patientDashboardBaseURL.replace('__PHID__', usr.phid);
				body += '<tr onclick="rowClick(event)">' +
					   '<td class="tdview"><strong>#' + sno + '</strong><br>' + formattedDate +'</td>' +
					 /* '<td class="tdview"><a href="' + fullURL + '">' + usr.phid + '</a></td>'+*/
					  '<td class="tdview" id="" data-id="' + usr.id + '" style="cursor: pointer;color: rgb(16 35 255);">Dr. Kanaga M.B.B.S</td>' +
					    '<td class="tdview">00:00</td>' +
						'<td class="tdview"> OPMR2458-7894</td>' +
						'<td class="tdview">Notaray Plus legal charges on donor</td>' +
						'<td class="tdview">Cash</td>' +				
				   '</tr>';						
					   '</tr>';
					   sno++;
			});
		}

		$("#upp_appt_details").html(body);
	}
function prerenderTable(data, ticketpageSize, pageNum) {
		$("#nxt_appt_details").show();
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
		var body = "";
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
				
				let dateStr = usr.date;
				let dob = usr.dob;		
				let formattedDate = moment(dateStr).format("DD MMM YYYY");
					dob = dob ? moment(dob).format("DD MMM YYYY") : "-";
				let fullURL = patientDashboardBaseURL.replace('__PHID__', usr.phid);
				body += '<tr onclick="rowClick(event)">' +
					   '<td class="tdview"><strong>#' + sno + '</strong><br>' + formattedDate +'</td>' +
					 /* '<td class="tdview"><a href="' + fullURL + '">' + usr.phid + '</a></td>'+*/
					  '<td class="tdview" id="" data-id="' + usr.id + '" style="cursor: pointer;color: rgb(16 35 255);">Dr. Kanaga M.B.B.S</td>' +
					    '<td class="tdview">O/P</td>' +
						'<td class="tdview"> OPMR2458-7894</td>' +
						'<td class="tdview">Notaray Plus legal charges on donor</td>' +
						'<td class="tdview">Cash</td>' +				
				   '</tr>';						
					   '</tr>';
					   sno++;
			});
		}

		$("#nxt_appt_details").html(body);
	}

function ticketrenderTable(response, ticketpageSize, pageNum) {
    var data = Array.isArray(response?.data) ? response.data : response;

    if (!Array.isArray(data)) {
        console.error("No valid data array found in response.");
        return;
    }

    var startIndex = (pageNum - 1) * ticketpageSize;
    var endIndex = Math.min(startIndex + ticketpageSize, data.length);
    var pageData = data.slice(startIndex, endIndex);

    var body = "";
    body = ticketData(pageData, body);

    $("#daily_details").html(body);
    $("#today_visits").text(data.length);
    $("#dcounts").text(data.length);
    $("#checkin_report").text(data.length);
}

function getBatteryIconHtml(value,total) {
    let iconClass = 'fa-battery-empty';
    let color = '#f44336'; // red by default
	let percentage = ((value / total) * 100).toFixed(2)
    if (percentage >= 90) {
        iconClass = 'fa-battery-full';
        color = '#4CAF50'; // green
    } else if (percentage >= 60) {
        iconClass = 'fa-battery-three-quarters';
        color = '#8BC34A';
    } else if (percentage >= 30) {
        iconClass = 'fa-battery-half';
        color = '#FFC107';
    } else if (percentage >= 10) {
        iconClass = 'fa-battery-quarter';
        color = '#FF9800';
    }

    return `<i class="fas ${iconClass}" style="color: ${color}; font-size: 1.5em;" title="${percentage}%"></i>`;
}

	
	function ticketData(pageData,body){
		var sno = 1;
		 if (pageData.length === 0) {
			body += '<tr><td colspan="18" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
			$.each(pageData, function(index, usr) {

						let dateStr = usr.checkin_date;
						let dob = usr.dob;
						let formattedDate = moment(dateStr).format("DD MMM YYYY");
						dob = dob ? moment(dob).format("DD MMM YYYY") : "-";
						let fullURL = patientDashboardBaseURL.replace('__PHID__', usr.phid);

						body += '<tr onclick="rowClick(event)">' +
							'<td class="tdview"><strong>#' + sno + '</strong><br>' + formattedDate + '</td>' +
							'<td class="tdview" id="timeline_view" data-id="' + usr.id + '" data-phid="' + usr.phid + '" style="cursor: pointer;color: rgb(16 35 255);">' + usr.phid + '</td>' +
							'<td class="tdview">' + usr.familyid + '</td>' +
							'<td class="tdview"><a href="' + fullURL + '">' + usr.name + '</a></td>' +
							'<td class="tdview">' + usr.mobile + '</td>' +
							'<td class="tdview">' + usr.age + '</td>' +
							'<td class="tdview">' + (usr.treatment_category || '') + '</td>' +
							'<td class="tdview">' + (usr.stage_of_treatment || '')  + '</td>' +
							'<td class="tdview">' + (usr.consultingdr_name || '') + '</td>' +
							'<td class="tdview">' + (usr.cc_name || '') + '</td>' +
							'<td class="tdview">' + (usr.cc_audit_name || '') + '</td>' +
							'<td class="tdview">' + usr.city + '</td>' +
							'<td class="tdview">' +
								'<div class="tooltip-hover">' +
									usr.patient_area +
									'<div class="tooltip-text">' + usr.street + ', ' + usr.area + '</div>' +
								'</div>' +
							'</td>' +
							'<td class="tdview">' +
								usr.ptsource +
								' <i class="fa fa-info-circle" onclick="showPopup()" style="cursor: pointer; color: #007bff; margin-left: 5px;" title="PT Source"></i>' +
							'</td>' +
							'<td class="tdview">' + usr.purpose + '</td>' +
							/*'<td class="tdview">' +
								'<div class="tooltip-container">' +
									'<span class="trigger-text"><i class="fa fa-info-circle" style="cursor: pointer; color: #007bff; margin-left: 5px;" title="Phar due"></i></span>' +
									'<div class="tooltip-table" style="width: 655px;">' +
										'<table>' +
											'<tr style="color: #e19710;"><td><b>O/P Payable:</b></td><td>80.00</td><td><b>I/P Payable:</b></td><td>75.00</td><td><b>Pharmacy Payable:</b></td><td>30.00</td><td><b>Total Payable:</b></td><td>30.00</td></tr>' +
											'<tr style="color: #008000eb;"><td><b>O/P Received:</b></td><td>80.00</td><td><b>I/P Received:</b></td><td>75.00</td><td><b>Pharmacy Received:</b></td><td>30.00</td><td><b>Total Received:</b></td><td>30.00</td></tr>' +
											'<tr style="color: #ff0000e8;"><td><b>O/P Due:</b></td><td>80.00</td><td><b>I/P Due:</b></td><td>75.00</td><td><b>Pharmacy Due:</b></td><td>30.00</td><td><b>Total Due:</b></td><td>30.00</td></tr>' +
										'</table>' +
									'</div>' +
								'</div>' +
							'</td>' +*/
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;">O/P Payable: 80.00</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;">I/P Payable: 75.00</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;">Pharmacy Payable: 30.00</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;">Total Payable: 130.00</td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
							'<td class="tdview" id="nxt_appt_date" data-pid="' + usr.id + '">Last Visit:<span class="clickable-date" data-pid="' + usr.id + '" style="cursor: pointer; color: rgb(16 35 255);" data-type="last">20-05-2025</span> & Next Visit:<span class="clickable-date" style="cursor: pointer; color: rgb(16 35 255);" data-pid="' + usr.id + '" data-type="next">26-05-2025</span></td>'
						let treatmentTableRows = '';
						if (usr.treatment_category && usr.treat_amt) {
							const categories = usr.treatment_category.split(',').map(item => item.trim());
							const amounts = usr.treat_amt.split(',').map(item => item.trim());
							
							const categoryMaxMap = {
									'ANC': 750,
									'ED ICSI': 450,
									'ANC +VE MISCARRIED': 150,
									'ANC FOLLOW UP': 800
								};

								// Fallback dynamic max (from entries not in categoryMaxMap)
								let dynamicMax = 100;
								amounts.forEach((amount, idx) => {
									const cat = categories[idx];
									if (!(cat in categoryMaxMap) && !isNaN(amount)) {
										dynamicMax = Math.max(dynamicMax, amount);
									}
								});
								
							categories.forEach((cat, idx) => {
								const amount = amounts[idx] || 0;
								const maxAmount = categoryMaxMap[cat] || dynamicMax;
								treatmentTableRows += '<tr><td>' + getBatteryIconHtml(amount, maxAmount) + ' ' + cat + ':</td><td>' + amount + '</td></tr>';
							});
						}

						body += '<td class="tdview">' +
							'<div>' +
								'<div style="width: 200px; margin-top: 5px;">' +
									'<table>' + treatmentTableRows + '</table>' +
								'</div>' +
							'</div>' +
						'</td>' +
							'<td style="display:none;" id="pdfview" class="tdview" >'+ usr.fs_study_pdfs +'</td>' +
							'<td style="display:none;" id="pdfview2" class="tdview" >'+ usr.antag_doses_pdfs +'</td>' +
							'<td style="display:none;" id="pdfview3" class="tdview" >'+ usr.trigger_used_pdfs +'</td>' +
							'<td style="display:none;" id="pdfview4" class="tdview" >'+ usr.inj_pdfs +'</td>' +
							'<td style="display:none;" id="pdfview5" class="tdview" >'+ usr.consent_pdfs +'</td>' +
							'<td style="display:none;" id="pdfview6" class="tdview" >'+ usr.blue_book_pdfs +'</td>' +
						'<td class="tdview documentview"  ><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>'+
						'<td class="tdview documentview2"  ><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>'+
						'<td class="tdview documentview3"  ><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>'+
						'<td class="tdview documentview4"  ><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>'+
						'<td class="tdview documentview5"  ><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>'+
						'<td class="tdview documentview6"  ><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>'+
						'<td class="tdview" id="open_edit_checkin" data-id="' + usr.id + '" style="cursor:pointer">' +
							'<img src="../assets/images/edit.png" style="width: 30px;" alt="Icon" class="icon">' +
						'</td>' +						
						'</tr>';					
						sno++;
					});

		}
		return body;
	}
	
	$(document).on('click', '.documentview', function (e) {
        $('#exampleModal4').modal('show');
        var row = $(this).closest('tr');
        // Get data from the row
        var pdffiles = row.find('#pdfview').text();
        pdffiles = pdffiles.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        var firstImage = pdffiles.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        $('#pdfmainview').attr('src', "../document_data/" + imageNamefirst);
        var imageArray = pdffiles.split(',');
        var views = '';
		console.log(imageArray);
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            imageNames = imageName.replace(/\\/g, ''); // Remove [ ] " and extra spaces
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<button style="font-size: 11px;" type="button" id="pdfviewdata" class="btn btn-primary pdf-btn">'+ imageNames +'</button>';
        });
         $('#image_view_pdfs').html("");
        $('#image_view_pdfs').html(views);
		
		// Add event listener for the PDF buttons
			$(document).on('click', '.pdf-btn', function() {
				// Remove 'active' class from all buttons
				$('.pdf-btn').removeClass('active');				
				// Add 'active' class to the clicked button
				$(this).addClass('active');
			});
    });
	
	$(document).on('click', '.documentview2', function (e) {
        $('#exampleModal5').modal('show');
        var row = $(this).closest('tr');
        // Get data from the row
        var pdffiles = row.find('#pdfview2').text();
        pdffiles = pdffiles.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        var firstImage = pdffiles.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        $('#pdfmainview2').attr('src', "../document_data/" + imageNamefirst);
        var imageArray = pdffiles.split(',');
        var views = '';
		console.log(imageArray);
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            imageNames = imageName.replace(/\\/g, ''); // Remove [ ] " and extra spaces
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<button style="font-size: 11px;" type="button" id="pdfviewdata2" class="btn btn-primary pdf-btn2">'+ imageNames +'</button>';
        });
         $('#image_view_pdfs2').html("");
        $('#image_view_pdfs2').html(views);
		
		// Add event listener for the PDF buttons
			$(document).on('click', '.pdf-btn2', function() {
				// Remove 'active' class from all buttons
				$('.pdf-btn2').removeClass('active');				
				// Add 'active' class to the clicked button
				$(this).addClass('active');
			});
    });
	
	$(document).on('click', '.documentview3', function (e) {
        $('#exampleModal6').modal('show');
        var row = $(this).closest('tr');
        // Get data from the row
        var pdffiles = row.find('#pdfview3').text();
        pdffiles = pdffiles.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        var firstImage = pdffiles.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        $('#pdfmainview3').attr('src', "../document_data/" + imageNamefirst);
        var imageArray = pdffiles.split(',');
        var views = '';
		console.log(imageArray);
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            imageNames = imageName.replace(/\\/g, ''); // Remove [ ] " and extra spaces
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<button style="font-size: 11px;" type="button" id="pdfviewdata3" class="btn btn-primary pdf-btn3">'+ imageNames +'</button>';
        });
         $('#image_view_pdfs3').html("");
        $('#image_view_pdfs3').html(views);
		
		// Add event listener for the PDF buttons
			$(document).on('click', '.pdf-btn3', function() {
				// Remove 'active' class from all buttons
				$('.pdf-btn3').removeClass('active');				
				// Add 'active' class to the clicked button
				$(this).addClass('active');
			});
    });
	
	$(document).on('click', '.documentview4', function (e) {
        $('#exampleModal7').modal('show');
        var row = $(this).closest('tr');
        // Get data from the row
        var pdffiles = row.find('#pdfview4').text();
        pdffiles = pdffiles.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        var firstImage = pdffiles.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        $('#pdfmainview4').attr('src', "../document_data/" + imageNamefirst);
        var imageArray = pdffiles.split(',');
        var views = '';
		console.log(imageArray);
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            imageNames = imageName.replace(/\\/g, ''); // Remove [ ] " and extra spaces
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<button style="font-size: 11px;" type="button" id="pdfviewdata4" class="btn btn-primary pdf-btn4">'+ imageNames +'</button>';
        });
         $('#image_view_pdfs4').html("");
        $('#image_view_pdfs4').html(views);
		
		// Add event listener for the PDF buttons
			$(document).on('click', '.pdf-btn4', function() {
				// Remove 'active' class from all buttons
				$('.pdf-btn4').removeClass('active');				
				// Add 'active' class to the clicked button
				$(this).addClass('active');
			});
    });
	
	$(document).on('click', '.documentview5', function (e) {
        $('#exampleModal8').modal('show');
        var row = $(this).closest('tr');
        // Get data from the row
        var pdffiles = row.find('#pdfview5').text();
        pdffiles = pdffiles.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        var firstImage = pdffiles.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        $('#pdfmainview5').attr('src', "../document_data/" + imageNamefirst);
        var imageArray = pdffiles.split(',');
        var views = '';
		console.log(imageArray);
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            imageNames = imageName.replace(/\\/g, ''); // Remove [ ] " and extra spaces
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<button style="font-size: 11px;" type="button" id="pdfviewdata5" class="btn btn-primary pdf-btn5">'+ imageNames +'</button>';
        });
         $('#image_view_pdfs5').html("");
        $('#image_view_pdfs5').html(views);
		
		// Add event listener for the PDF buttons
			$(document).on('click', '.pdf-btn5', function() {
				// Remove 'active' class from all buttons
				$('.pdf-btn5').removeClass('active');				
				// Add 'active' class to the clicked button
				$(this).addClass('active');
			});
    });
	$(document).on('click', '.documentview6', function (e) {
        $('#exampleModal9').modal('show');
        var row = $(this).closest('tr');
        // Get data from the row
        var pdffiles = row.find('#pdfview6').text();
        pdffiles = pdffiles.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        var firstImage = pdffiles.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        $('#pdfmainview6').attr('src', "../document_data/" + imageNamefirst);
        var imageArray = pdffiles.split(',');
        var views = '';
		console.log(imageArray);
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            imageNames = imageName.replace(/\\/g, ''); // Remove [ ] " and extra spaces
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<button style="font-size: 11px;" type="button" id="pdfviewdata6" class="btn btn-primary pdf-btn6">'+ imageNames +'</button>';
        });
         $('#image_view_pdfs6').html("");
        $('#image_view_pdfs6').html(views);
		
		// Add event listener for the PDF buttons
			$(document).on('click', '.pdf-btn6', function() {
				// Remove 'active' class from all buttons
				$('.pdf-btn6').removeClass('active');				
				// Add 'active' class to the clicked button
				$(this).addClass('active');
			});
    });
	
	 $(document).on('click', '#pdfviewdata', function (e) {
        fetchvalue=$(this).text();
      $('#pdfmainview').attr('src', "../document_data/" + fetchvalue);	  
    });
	
	 $(document).on('click', '#pdfviewdata2', function (e) {
        fetchvalue=$(this).text();
      $('#pdfmainview2').attr('src', "../document_data/" + fetchvalue);	  
    });
	
	 $(document).on('click', '#pdfviewdata3', function (e) {
        fetchvalue=$(this).text();
      $('#pdfmainview3').attr('src', "../document_data/" + fetchvalue);	  
    });
	
	 $(document).on('click', '#pdfviewdata4', function (e) {
        fetchvalue=$(this).text();
      $('#pdfmainview4').attr('src', "../document_data/" + fetchvalue);	  
    });
	
	 $(document).on('click', '#pdfviewdata5', function (e) {
        fetchvalue=$(this).text();
      $('#pdfmainview5').attr('src', "../document_data/" + fetchvalue);	  
    });
	
	 $(document).on('click', '#pdfviewdata6', function (e) {
        fetchvalue=$(this).text();
      $('#pdfmainview6').attr('src', "../document_data/" + fetchvalue);	  
    });
	
	 $('#blue_book_type').on('change', function () {
		var selectedText = $("#blue_book_type option:selected").text().trim();

		if (selectedText && selectedText !== "Select PDF") {
		  $('#blueBookLabel').text(selectedText);
		  $('#blue_book_upload').show();
		} else {
		  $('#blueBookLabel').text('Blue Book PDF:');
		  $('#blue_book_upload').hide();
		}
	  });
	
	  $('#consent_type_pdf').on('change', function () {
		var selectedText = $("#consent_type_pdf option:selected").text().trim();
		if (selectedText && selectedText !== "Select PDF") {
		  $('#fileLabel').text(selectedText);
		  $('#consent_pdf').show();
		} else {
		  $('#fileLabel').text('Consent Forms PDF:');
		  $('#consent_pdf').hide();
		}
	  });
	
	 $('select[name="trigger_used"]').on('change', function () {
		var selectedText = $(this).find("option:selected").text().trim();
		if (selectedText && selectedText !== "Select Package Type") {
		  $('#triggerLabel').text(selectedText);
		  $('#trigger_used_pdf').show();
		} else {
		  $('#triggerLabel').text('');
		  $('#trigger_used_pdf').hide();
		}
	  });
	
	 $('#antag_doses').on('change', function () {
			var selectedText = $("#antag_doses option:selected").text().trim();			
			if (selectedText && selectedText !== "Select Type") {
			  $('#antagLabel').text(selectedText);
			  $('#antag_doses_pdf').show();
			} else {
			  $('#antagLabel').text('');
			  $('#antag_doses_pdf').hide();
			}
	  });
	
	  $('#fs_study').on('change', function () {
		var pdfText = $("#fs_study option:selected").text();
		const pdfOptions = ["ACTUAL STUDY SHEET PDF", "CLINICAL FS STUDY SUMMARY"];

		if (pdfOptions.includes(pdfText)) {
		  $('#fileLabel1').text(pdfText);
		  $('#fs_study_pdf').show(); 
		} else {
		  $('#fileLabel1').text('');
		  $('#fs_study_pdf').hide(); 
		}
	  });	
  
    $('#package_type').on('change', function() {
      const selected = $(this).val();
      $('.package-amount').hide(); 
      if (selected === '1A COMP PACKAGE INC INJECTIONS') {
        $('#amount_comp_1A').show();
        $('#package_amt').show();
      } else if (selected === '1B COMP PACKAGE TREATMENT ONLY' || selected === '1C COMP PACKAGE TREATMENT ONLY') {
        $('#amount_comp_1B').show();
      }else{
		  $('#amount_comp_1AB').show();
	  }
    });

	$('select[id^="package_amt"]').on('change', function () {
		  $('#final_package_amt').val($(this).val());
	});

  $(document).on('click', '#open_edit_checkin', function () {
	 var id = $(this).closest('tr').find('#open_edit_checkin').data('id');
	$('#income_id').val(id);
    $('#formContainer').slideDown(function () {
		document.getElementById("formContainer").scrollIntoView({ behavior: "smooth" });
  });
    $('.pc-content').addClass('hidden');
	 $.ajax({
		url: checkinRptEdit,
		type: "GET",
		data: {
			id: id,

		},
		success: function (response) {
			$('#paid_status').val(response.total_amount_payable);
			var response = response.checkin_report;
			
			if (response.package_type === '1A COMP PACKAGE INC INJECTIONS') {
				$('#amount_comp_1A').show();
				 $('#package_amt').val(response.package_amount);
				   $('#final_package_amt').val(response.package_amount); 
			  }else if (response.package_type === '2A SPLIT PACKAGE TREATMENT ONLY') {
					$('#amount_comp_1AB').show();
					$('#package_amt3').val(response.package_amount);
				    $('#final_package_amt').val(response.package_amount); 
			  } else if (response.package_type === '1B COMP PACKAGE TREATMENT ONLY' || response.package_type === '1C COMP PACKAGE TREATMENT ONLY') {
				$('#amount_comp_1B').show();
				 $('#package_amt2').val(response.package_amount);
				   $('#final_package_amt').val(response.package_amount); 
			  }else{
				  $('#package_amt').hide();
			  } 
			$('#category').val(response.category);
			$('#stage_trt').val(response.stage_of_treatment_id);
			$('#trt_category').val(response.treatment_category_id);
			$('#update_blue_book_pdfs').val(response.blue_book_pdfs);
			$('#update_consent_pdfs').val(response.consent_pdfs);
			$('#update_fs_study_pdfs').val(response.fs_study_pdfs);
			$('#update_inj_pdfs').val(response.inj_pdfs);
			$('#update_trigger_used_pdfs').val(response.trigger_used_pdfs);
			$('#antag_doses').val(response.antag_doses_till_trigger);
			$('#wife_name').val(response.name);
			$('#w_mrd_no').val(response.phid);
			$('#h_mrd_no').val(response.husband_mrd_number);
			$('#husband_name').val(response.husband_name);
			$('#procedure_name').val(response.procedure_name);
			$('#cycle_no').val(response.cycle_no);
			$('#package_type').val(response.package_type);
			$('#fs_study').val(response.fs_study_injections_used);
			$('#trigger_used').val(response.trigger_used);
			$('#actual_discount').val(response.actual_discount);
			$('#expected_discount').val(response.expected_discount);
			$('#approved_discount').val(response.approved_discount);
			$('#consent_type_pdf').val(response.consent_type_pdf);
			$('#aft_discount').val(response.aft_discount);			
			$('#blue_book_type').val(response.blue_book_pdf);
			$('#cc_auddit_name').val(response.cc_handled);
			$('#cc_audit_employment_id').val(response.cc_handled_id);
			$('#cc_employment_id').val(response.cc_consultant_id);
			$('#cc_name').val(response.consultant_name);
			$('#loan_management').val(response.loan_management);
			$('#crm_incharge').val(response.crm_incharge);
			$('#crm_incharge_id').val(response.crm_incharge_id);
		},
		error: function (xhr, status, error) {
			console.error("AJAX Error:", status, error);
		}
	  });
  });

  $(document).on('click', '#formCancel', function () {
    $('#formContainer').slideUp();
	 $('.pc-content').removeClass('hidden');
  });
	
	$(document).on('click', '.clickable-date', function (e) {
		$('#exampleModal2').modal('show');
		$("#appt_details11").show();		
		$("#upcom_details11").show();		
		var row = $(this).closest('tr');
		var id = row.find('#nxt_appt_date').data('pid');
		var moredatefittervale = $('#dateallviews').text();
		$.ajax({
                url: checkinLastFetch,
                type: "GET",
				data: {
						id: id,
						moredatefittervale: moredatefittervale
					},
                success: function(response) {
						$("#appt_details11").hide();
						$("#upcom_details11").hide();
					handleSuccess(response.last_data,2);
					handleSuccess(response.last_data,3);
                }
            });
	});
	
	 const Secret = "d6401b40cebeda6f22c7e7ee1efe0ed4"; 

    function calculateHMAC(secret, stringToSign) {
        const hash = CryptoJS.HmacSHA1(stringToSign, secret);
        return CryptoJS.enc.Base64.stringify(hash);
    }

    function hmac(phid) {
        const verb = "POST";
        const contentMd5 = ""; 
        const contentType = "application/x-www-form-urlencoded";
        const date = "Wed, 31 May 2025 10:00:00 GMT";
        const path = "/api/get/patienttimeline/draravinds-ivf/"+phid;

        const stringToSign =
            verb + "\n" +
            contentMd5 + "\n" +
            contentType + "\n" +
            date + "\n" +
            "\n" +
            path.toLowerCase();

        return calculateHMAC(Secret, stringToSign);
    }
	var phid;
	$(document).on('click', '#amount_view', function (e) {
		$('#exampleModal3').modal('show');	
		$('#exampleModal1').modal('hide');		
				
		var row = $(this).closest('tr');
			phid = row.find('#amount_view').data('mrd');
		var	billno = row.find('#amount_view').data('billno');
		var	billtype = row.find('#amount_view').data('billtype');
		var	paymenttype = row.find('#amount_view').data('paymenttype');
		var	prev_balance = row.find('#amount_view').data('prev_bal');
		var	amountreceived = row.find('#amount_view').data('amountreceived');
		var	amountpayable = row.find('#amount_view').data('amountpayable');
		var	advance = row.find('#amount_view').data('advance');
		var	advanceRaw = parseFloat(advance) || 0;
		var	billdate = row.find('#amount_view').data('billdate');
			billdate = billdate.toString();
		const year = billdate.substring(0, 4);
		const month = billdate.substring(4, 6);
		const day = billdate.substring(6, 8);
		if(billtype == 'Pharmacy'){
					$("#pharmacy_details11").show();
					$("#pharmacy_details").hide();
					$("#not_pharmacy").hide();
					$("#pharmacy").show();
				}else{
					$("#amt_details11").show();
					$("#amt_details").hide();
					$("#not_pharmacy").show()
					$("#pharmacy").hide();
				}
		const formattedDate = `${day}-${month}-${year}`;
		const hmacOutput = hmac(phid);
		$.ajax({
                url: checkinTimeLine,
                type: "GET",
				data: {
						type:"amountdetails",
						billno: billno,
						phid: phid,
						authorization:hmacOutput
					},
                success: function(response) {			
					if(billtype == 'Pharmacy'){
						$("#pharmacy_details11").hide();
						const tbody = document.getElementById('product-amt');
							tbody.innerHTML = "";
							let totalAmt = 0;
							response.forEach((item, index) => {
							  if (!item.name || !item.mrp || !item.qty) return;
							  const qty = parseFloat(item.qty) || 0;
							  const rowTotal = parseFloat(item.prodvalue);
									totalAmt += rowTotal;
							  const row = document.createElement('tr');
							  row.innerHTML = `
								<td>${index + 1}</td>
								<td>${item.name}</td>
								<td>${item.batch}</td>
								<td>${item.expiry}</td>
								<td>${qty}</td>
								<td>${item.mrp}</td>
								<td>${item.taxpercentage}%</td>
								<td>${item.discountpercentage}%</td>
								<td>${item.prodvalue}</td>
							  `;
							  tbody.appendChild(row);
							});
							const totalRow = document.createElement('tr');
							totalRow.innerHTML = `
							  <td colspan="8" style="text-align:right;"><strong>Total</strong></td>
							  <td>${totalAmt.toFixed(2)}</td>
							`;
							tbody.appendChild(totalRow);
							// Add discount row
							let totalDiscount = (totalAmt*5)/100;
							const discountRow = document.createElement('tr');
							discountRow.innerHTML = `
							  <td colspan="8" style="text-align:right;"><strong>Discount(5.00%)</strong></td>
							  <td>-${totalDiscount.toFixed(2)}</td>
							`;
							tbody.appendChild(discountRow);							
							const prebalRow = document.createElement('tr');
							prebalRow.innerHTML = `
							  <td colspan="8" style="text-align:right;"><strong>Previous Balance</strong></td>
							  <td>${parseFloat(prev_balance).toFixed(2)}</td>
							`;
							tbody.appendChild(prebalRow);
							
							const amtReceivable = document.createElement('tr');
							amtReceivable.innerHTML = `
							  <td colspan="8" style="text-align:right;"><strong>Amount Receivable</strong></td>
							  <td>${parseFloat(amountpayable).toFixed(2)}</td>
							`;
							tbody.appendChild(amtReceivable);
							const amountReceied = document.createElement('tr');
							amountReceied.innerHTML = `
							  <td colspan="8" style="text-align:right;"><strong>Amount Received</strong></td>
							  <td>${parseFloat(amountreceived).toFixed(2)}</td>
							`;
							tbody.appendChild(amountReceied);
						//handleSuccess(response,9);
					}else{
						$("#amt_details11").hide();
						 const tbody = document.getElementById('product-tbody');
							tbody.innerHTML = "";
							let totalAmt = 0;
							response.forEach((item, index) => {
							  if (!item.name || !item.amt || !item.qty) return;
							  const price = parseFloat(item.amt) || 0;
							  const qty = parseFloat(item.qty) || 0;
							  const discount = parseFloat(item.discountamt) || 0;
							  const total = (price * qty) - discount;
							  const rowTotal = price * qty;
									totalAmt += rowTotal;
							  const row = document.createElement('tr');
							  row.innerHTML = `
								<td>${index + 1}</td>
								<td>${item.name}</td>
								<td>${qty}</td>
								<td>${price.toFixed(2)}</td>
								<td>0.00</td> <!-- Tax -->
								<td>0.00</td> <!-- Disc -->
								<td>${discount.toFixed(2)}</td>
								<td>${total.toFixed(2)}</td>
							  `;
							  tbody.appendChild(row);
							});
							const totalRow = document.createElement('tr');
							totalRow.innerHTML = `
							  <td colspan="7" style="text-align:right;"><strong>Total</strong></td>
							  <td>${totalAmt.toFixed(2)}</td>
							`;
							tbody.appendChild(totalRow);
							const advanceRow = document.createElement('tr');
							advanceRow.innerHTML = `
							  <td colspan="7" style="text-align:right;"><strong>Advance</strong></td>
							  <td>${advanceRaw.toFixed(2)}</td>
							`;
							tbody.appendChild(advanceRow);
							const prevbalRow = document.createElement('tr');
							prevbalRow.innerHTML = `
							  <td colspan="7" style="text-align:right;"><strong>Previous Balance</strong></td>
							  <td>${parseFloat(prev_balance).toFixed(2)}</td>
							`;
							tbody.appendChild(prevbalRow);
							const amountpayableRow = document.createElement('tr');
							amountpayableRow.innerHTML = `
							  <td colspan="7" style="text-align:right;"><strong>Amount Receivable</strong></td>
							  <td>${parseInt(amountpayable).toFixed(2)}</td>
							`;
							tbody.appendChild(amountpayableRow);
							const amountReceied = document.createElement('tr');
							amountReceied.innerHTML = `
							  <td colspan="7" style="text-align:right;"><strong>Amount Received</strong></td>
							  <td>${parseFloat(amountreceived).toFixed(2)}</td>
							`;
							tbody.appendChild(amountReceied);							
						//handleSuccess(response,8);
					}				
					 const lastItem = response[response.length - 1];
					 const modal = document.getElementById('exampleModal3');
					 const table = modal.querySelector('table');
					 if (table) {
						const rows = table.querySelectorAll('tr');

						rows.forEach(row => {
							const labelCell = row.children[0];
							const valueCell = row.children[1];

							if (!labelCell || !valueCell) return;

							const label = labelCell.textContent.trim();

							switch (label) {
								case 'Location:':
									valueCell.textContent = lastItem.street+', '+lastItem.state;
									break;
								case 'Consultant:':
									valueCell.textContent = lastItem.consultingdr_name;
									break;
								case 'Patient Type:':
									valueCell.textContent = billtype;
									break;
								case 'ID:':
									valueCell.textContent = phid;
									break;
								case 'Name:':
									valueCell.textContent = lastItem.name;
									break;
								case 'Mobile:':
									valueCell.textContent = lastItem.mobile;
									break;
								case 'Age:':
									valueCell.textContent = lastItem.age;
									break;
								case 'Gender:':
									valueCell.textContent = lastItem.gender;
									break;
								case 'Payment Type:':
									valueCell.textContent = paymenttype;
									break;
								case 'Bill No:':
									valueCell.textContent = billno;
									break;
								case 'Bill Date:':
									valueCell.textContent = formattedDate;
									break;
							}
						});
					}
					
                }
            });
	});
	
	$(document).on('click', '#timeline_view', function (e) {
		$('#exampleModal1').modal('show');	
		$("#time_details11").show();
		$("#time_details").hide();
		$(".my_search_view").hide();		
		var row = $(this).closest('tr');
		var id = row.find('#timeline_view').data('id');
			phid = row.find('#timeline_view').data('phid');
		const hmacOutput = hmac(phid);
		$.ajax({
                url: checkinTimeLine,
                type: "GET",
				data: {
						id: id,
						phid: phid,
						authorization:hmacOutput
					},
                success: function(response) {
						$("#time_details11").hide();
					handleSuccess(response,5);
                }
            });
	});
	
    $('.ranges, .applyBtn').on('click', function() {
		console.log("fitterremovedata");		
		console.log(fitterremovedata);			
		
        // Check if the click happened on a specific class
        if ($(this).hasClass('ranges')) {
			if(activeTabId == "" || activeTabId == "analytics-tab-1"){
				var datefilltervalue = $('#dateallviews').text();
				//var url = dateCheckUrl;
				var url = dateCheckinUrl;
				var urlstatus = 1;
			}
			if(activeTabId == "analytics-tab-2"){
				var datefilltervalue = $('#lastdateviewsall').text(); 
				var url = lastdatefillter;
				var urlstatus = 4;
			}
			if(activeTabId == "analytics-tab-3"){
				var datefilltervalue = $('#nextdateviewsall').text(); 
				var url = nextdatefillter;
				var urlstatus = 6;
				fitterremovedata = fitterremovedata1;
				if(fitterremovedata.length ==  0){
					 var defaultLocation = "Aathur";
					$('#location_views').val(defaultLocation);
				
					$('.dropdown-options div').each(function() {
						if ($(this).text().trim() === defaultLocation) {
							$(this).addClass('selected'); // Add class for styling the selected item
						}
					});
				}
			}			
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata,"checkinreport");
            
        } else if ($(this).hasClass('applyBtn')) {
            var datefilltervaluenew = $('.drp-selected').text(); // Get the current text value when '.applyBtn' is clicked
				$('.drp-selected').text("");
            var dateRange = datefilltervaluenew.split(' - ');
            function convertDateFormat(dateStr) {
                let parts = dateStr.split('/');
                return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY   
            }
            var startDate = convertDateFormat(dateRange[0]);
            var endDate = convertDateFormat(dateRange[1]);
            var datefilltervalue = `${startDate} - ${endDate}`;
           if(activeTabId == "" || activeTabId == "analytics-tab-1"){
				//var url = dateCheckUrl;
				var url = dateCheckinUrl;
				var urlstatus = 1;
			}
			if(activeTabId == "analytics-tab-2"){
				var url = lastdatefillter;
				var urlstatus = 4;
			}	
			if(activeTabId == "analytics-tab-3"){ 
				var url = nextdatefillter;
				var urlstatus = 6;
			}			
			            
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata,"checkinreport");
        }
    });
	
	$(document).on('click', '#edit_checkin', function (e) {
    $('#offcanvas_edit_income').offcanvas('show');
    var id = $(this).closest('tr').find('#edit_checkin').data('id');
    $('#income_id').val(id);

    $.ajax({
        url: checkinRptEdit, 
        data: { id: id },
        type: "GET",
        success: function(response) {
            var checkinReport = response.checkin_report;
            var employeeData = response.employee_data?.data || []; 
			
            /*var ccNameDropdown = $('#cc_name_list');
            ccNameDropdown.empty();
            //ccNameDropdown.append('<div class="custom-dropdown-item">Select CC Name</div>');
            var ccAuditDropdown = $('#cc_audit_name_list');
            ccAuditDropdown.empty();
            ccAuditDropdown.append('<div class="custom-dropdown-item">Select CC Audit Name</div>');

            /*if (employeeData.length > 0) {
                employeeData.forEach(function(emp) {
                    if (emp.fullname) {
                        const item = $('<div>')
                            .addClass('custom-dropdown-item')
                            .text(emp.fullname)
                            .attr('data-cid', emp.employment_id);

                        item.on('click', function () {
                            const selectedNme = $(this).text();
                            const selectedI = $(this).data('cid');
                            $('#cc_name').val(selectedNme);
                            $('#cc_employment_id').val(selectedI);
                            ccNameDropdown.hide();
                        });

                        ccNameDropdown.append(item);
                    }
                });
            } else {
                ccNameDropdown.append('<div class="custom-dropdown-item">No employee data available</div>');
            }*/
			
            /*if (employeeData.length > 0) {
                employeeData.forEach(function(emp) {
                    if (emp.fullname) {
                        const itemc = $('<div>')
                            .addClass('custom-dropdown-item')
                            .text(emp.fullname)
                            .attr('data-id', emp.employment_id);

                        itemc.on('click', function () {
                            const selectedName = $(this).text();
                            const selectedId = $(this).data('id');
                            $('#cc_auddit_name').val(selectedName);
                            $('#cc_audit_employment_id').val(selectedId);
                            ccAuditDropdown.hide();
                        });

                        ccAuditDropdown.append(itemc);
                    }
                });
            } else {
                ccAuditDropdown.append('<div class="custom-dropdown-item">No employee data available</div>');
            }

            //$('#cc_name').on('click', function () {
            //    ccNameDropdown.show(); 
            //});
			
            $('#cc_auddit_name').on('click', function () {
                ccAuditDropdown.show(); 
            });

            $('#cc_name').on('keyup', function () {
                var filter = $(this).val().toLowerCase();
                ccNameDropdown.empty();
                ccNameDropdown.append('<div class="custom-dropdown-item">Select CC Name</div>');
				
                employeeData.forEach(function(emp) {
                    if (emp.fullname && emp.fullname.toLowerCase().includes(filter)) {
                        const items = $('<div>')
                            .addClass('custom-dropdown-item')
                            .text(emp.fullname)
                            .attr('data-cid', emp.employment_id);

                        items.on('click', function () {
                            const selectedNae = $(this).text();
                            const selctedId = $(this).data('cid');
                            $('#cc_name').val(selectedNae);
                            $('#cc_employment_id').val(selctedId);
                            ccNameDropdown.hide();
                        });
                        ccNameDropdown.append(items);
                    }
                });

                if (ccNameDropdown.children().length === 1) {
                    ccNameDropdown.append('<div class="custom-dropdown-item">No matching results</div>');
                }
            });
			
            $('#cc_auddit_name').on('keyup', function () {
                var filter = $(this).val().toLowerCase();
                ccAuditDropdown.empty();
                ccAuditDropdown.append('<div class="custom-dropdown-item">Select CC Audit Name</div>');
				
                employeeData.forEach(function(emp) {
                    if (emp.fullname && emp.fullname.toLowerCase().includes(filter)) {
                        const citem = $('<div>')
                            .addClass('custom-dropdown-item')
                            .text(emp.fullname)
                            .attr('data-id', emp.employment_id);

                        citem.on('click', function () {
                            const selecteName = $(this).text();
                            const seletedId = $(this).data('id');
                            $('#cc_auddit_name').val(selecteName);
							 $('#cc_audit_employment_id').val(seletedId);
                            ccAuditDropdown.hide();
                        });
                        ccAuditDropdown.append(citem);
                    }
                });

                if (ccAuditDropdown.children().length === 1) {
                    ccAuditDropdown.append('<div class="custom-dropdown-item">No matching results</div>');
                }
            });

            // Close dropdown if clicked outside
           //$(document).on('click', function(event) {
           //    if (!$(event.target).closest('#cc_name, #cc_name_list').length) {
           //        ccNameDropdown.hide();
           //    }
           //});
			
            // Close dropdown if clicked outside
            $(document).on('click', function(event) {
                if (!$(event.target).closest('#cc_auddit_name, #cc_audit_name_list').length) {
                    ccAuditDropdown.hide();
                }
            });*/

            // Other form fields
			$('#cc_employment_id').val(checkinReport.cc_employment_id);
			$('#cc_audit_employment_id').val(checkinReport.cc_audit_id);
			$('#cc_name').val(checkinReport.cc_name);
            $('#stage_trt').val(checkinReport.stage_of_treatment);
            $('#pt_name').val(checkinReport.pt_name);
            $('#cc_auddit_name').val(checkinReport.cc_audit_name);
            $('#ip_phar_due').val(checkinReport.ip_phar_due);
            $('#next_appt_date').val(checkinReport.next_appointment_date);
            $('#fincial_icsi').val(checkinReport.ef_fet_ilnj);
            $('#od_fet_legal').val(checkinReport.od_fet_legal);
            $('#ed_fet_legal').val(checkinReport.ed_fet_legal);

            // Treatment categories
            var selectedCategories = checkinReport.treatment_category?.split(',').map(item => item.trim()) || [];
            var treatmentAmounts = checkinReport.treat_amt?.split(',').map(item => item.trim()) || [];
			/*var selectedLocationId = checkinReport.location_id;
				if (selectedLocationId) {
					$('#location_id').val(selectedLocationId); // hidden field
					var selectedOption = $('.location-option[data-value="' + selectedLocationId + '"]');
					$('#location_display').val(selectedOption.text().trim()); // visible input
				}*/

            $('.statusCheckbox').prop('checked', false);
            selectedCategories.forEach(function(category) {
                $('input[type="checkbox"][value="' + category + '"]').prop('checked', true);
            });

            updateAmountValues(selectedCategories, treatmentAmounts);
        },
        error: function(xhr) {
            console.error('Error occurred:', xhr.responseText);
        }
    });
});


	/*$(document).on('click', '#edit_checkin', function (e) {
			$('#offcanvas_edit_income').offcanvas('show');
			var id = $(this).closest('tr').find('#edit_checkin').data('id');
			$('#income_id').val(id);
			$.ajax({
				url: checkinRptEdit,
				data:{
						id:id
					 },
                type: "GET",
				success: function(response) {
					   var selectedCategories = response.treatment_category.split(',').map(function(item) {
							return item.trim(); 
						});
					   var treatmentAmounts = response.treat_amt ? response.treat_amt.split(',').map(function(item) {
							return item.trim(); 
						}) : [];
					 $('.statusCheckbox').prop('checked', false);
					
					selectedCategories.forEach(function(category) {
						$('input[type="checkbox"][value="' + category + '"]').prop('checked', true);
					});
                    $('#stage_trt').val(response.stage_of_treatment);
                    $('#pt_name').val(response.pt_name);
                    $('#cc_name').val(response.cc_name);
                    $('#cc_audit_name').val(response.cc_audit_name);
                    $('#ip_phar_due').val(response.ip_phar_due);
                    $('#next_appt_date').val(response.next_appointment_date);
                    $('#fincial_icsi').val(response.ef_fet_ilnj);
                    $('#od_fet_legal').val(response.od_fet_legal);
                    $('#ed_fet_legal').val(response.ed_fet_legal);
					//updateSelectedValues();
					updateAmountValues(selectedCategories, treatmentAmounts);
				},
				error: function(xhr) {
					console.error('Error occurred:', xhr.responseText);
				}
			});

		});*/


    $(document).on("click", ".clear_next_views", function () {
	    fitterremovedata1.length = 0;
		$('#location_views').val('');
        $('.clear_next_views').hide();
        $(".next_search_view").hide();
		$(".value_views_next").text("");
        last_next_visit_fetch(1);
    
    });
	
	$(document).on("click", ".value_views_next", function () {
		  fitterremovedata1.length = 0;
		$('.clear_next_views').hide();
        $(".next_search_view").hide();
		var clear_filtr = $(this).attr('id');		
        $(this).text("");
		if(clear_filtr == 'loc_next_search'){
			$('#location_views').val('');	
		}
		last_next_visit_fetch(1);
	});
	
    $(document).on("click", ".clear_views", function () {
		$(".options_branch.brachviewsall").empty();
      	 $("#daily_details").hide();
		fitterremovedata.length = 0;		
		$('.checkvalues_search').val("")
        $(".value_views").text("");
        $('.morefittersclr').val("");
        $('.clear_views').hide();
        $(".csearch_view").hide();
		$(".value_views_mainsearch").text("");
        overall_fetch(2);
    
    });

	 $(document).on("click", ".value_views_mainsearch", function () {
		var datefilltervalue = $('#dateallviews').text();	
        var morefillterremvedata = $(this).text().replace(/, /g, ",");
		var clear_filtr = $(this).attr('id');		
        $(this).text("");
		
		if(clear_filtr == 'cbranch_search'){
			$('#czone_views').val('');	
		}
		if(clear_filtr == 'czone_search'){
			$('#cbranch_views').val('');	
		}
		if(clear_filtr == 'ctrt_search'){
			$('#ctrt_category').val('');	
		}
		if(clear_filtr == 'ctrt_stage'){
			$('#ctreatment_stage').val('');	
		}
		if(clear_filtr == 'pt_stage'){
			$('#pt_source_id').val('');	
		}
		if(clear_filtr == 'cc_name_audit'){
			$('#cc_audit_name').val('');	
		}
		if(clear_filtr == 'doctor_name'){
			$('#cc_doctor_name').val('');	
		}
		if(clear_filtr == 'patient_age'){
			$('#cc_patient_age').val('');	
		}
		if(clear_filtr == 'name_cc'){
			$('#name_cc_audit').val('');	
		}
		
		// Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });			
          morefilterview(fitterremovedata,datefilltervalue,1,checkinBranchfitter,"checkinreport");
   
    });   
});


