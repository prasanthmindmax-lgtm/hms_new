$(document).ready(function() {	
    overall_fetch(1);
    //last_next_visit_fetch(1);

    $(".csearch_view").hide();
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
	$('#daily_details11').show();
	$("#regular_details").hide();
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
		$("#regular_details").hide();
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

	// Fetch the data and initialize pagination
	function overall_fetch(statusid) {
		
		var moredatefittervale = $('#dateallviews').text();
		$(".value_views_mainsearch").text("");
		$('#daily_details11').show();
		//return;
		$.ajax({
			//url: checkfetchUrl,
			url: regfetchUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid,
				apistatus:'regularreport'
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
	
			
function handleSuccess(responseData,urlstatus) {
	if(urlstatus == 1){			
			$("#daily_details11").hide();
			$("#regular_details").show();
			ticketdataSource = responseData; 
			totalItems = responseData.length; 
			var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
			ticketrenderPagination(ticketdataSource, ticketpageSize);
			ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
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
	

// Handle items per page change
$('#itemsPerPageSelect').change(function() {
    var ticketpageSize = parseInt($(this).val());
    ticketrenderPagination(ticketdataSource, ticketpageSize);
    ticketrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
});

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

    $('#regularpagination').html(paginationHtml);
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

    $("#regular_details").html(body);
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
		
		for (let i = 1; i <= 3; i++) {
					body += '<tr onclick="rowClick(event)">' +
							'<td class="tdview"><strong>#1</strong><br>25-05-2025</td>' +
							'<td class="tdview">OD ICSI IMSI</td>' +
							'<td class="tdview">BANGALORE -ECT</td>' +
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>WIFE</strong>: AFECT-104</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>HUSBAND</strong>: AFECT-105</td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>W. Name</strong>:  Mrs. Bhagyashree S Patil</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Age</strong>: 03/03/1980 / 43 years 5 months 29 days</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>H. Name</strong>: Mr. Sharan Patil</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Age</strong>: 01/06/1976 / 47 years 3 months 0 days</td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Open</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Comprehensive</strong>: 350000.00</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>LEGAL FEES</strong> : 25000.00</td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+							
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;">Yes</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;">Yes</td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
							'<td class="tdview"></td>' +							
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Days of Injection</strong>: 10</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Injection Used</strong>: gonal f , hmg , hcg</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Value</strong>: 56782</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Mention if done >5% Verified</strong>: 30000</td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Date of Freezing</strong>: 05-08-2025</td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Straw detach</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Cost  of Paid</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Mention if done >Split up</strong>: </td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Paid Details</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>D.O.R</strong>:</td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +							
							'<td class="tdview"></td>' +
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Non Inv</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Inv</strong>:</td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Non Inv</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Inv</strong>:</td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +
							'<td class="tdview"></td>' +
							'<td class="tdview">' + 
							  '<table>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Photo</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>ART Conseltation</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Bond</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>7S Sheet  macdoc</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>7s duty Open</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>7s duty ET</strong>: </td>' +
								'</tr>' +
								'<tr>' +
								  '<td style="white-space: nowrap;"><strong>Trail PAP</strong>: </td>' +
								'</tr>' +
							  '</table>' +
							'</td>'+
				'</tr>';
		}
		 /*if (pageData.length === 0) {
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
							'<td class="tdview" id="timeline_view" data-id="' + usr.id + '" style="cursor: pointer;color: rgb(16 35 255);">' + usr.phid + '</td>' +
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

						'<td class="tdview" id="edit_checkin" data-id="' + usr.id + '" style="cursor:pointer">' +
							'<img src="../assets/images/edit.png" style="width: 30px;" alt="Icon" class="icon">' +
						'</td>' +
						'</tr>';

						sno++;
					});

		}*/
		return body;
	}	
	
    $('.ranges, .applyBtn').on('click', function() {
		console.log("fitterremovedata");		
		console.log(fitterremovedata);			
		//if(fitterremovedata.length ==  0){
		//	 var defaultLocation = "Kerala - Palakkad";
		//	$('#cbranch_views').val(defaultLocation);
		//
		//	$('.loct-dropdown-options div').each(function() {
		//		if ($(this).text().trim() === defaultLocation) {
		//			$(this).addClass('selected'); // Add class for styling the selected item
		//		}
		//	});
		//}
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
	

    $(document).on("click", ".clear_views", function () {
		$(".options_branch.brachviewsall").empty();
      	 $("#regular_details").hide();
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


