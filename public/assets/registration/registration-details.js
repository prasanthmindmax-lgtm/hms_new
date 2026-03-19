$(document).ready(function() {
    // Fetch data and initialize pagination
	//my_ticket_fetch(1);
    overall_fetch(1);
    $(".search_views").hide();
	$(".search_report").hide();
	var loaderInterval = null;
var loaderProgress = 0; 

function startLoader() {
    const progressBar = $('#progress-bar');
    const progressText = $('#progress-bar');
    const errorMessage = $('#error-message');

    loaderProgress = 0;  

    progressText.text(`Loading: 0%`);
    progressBar.css({
        'width': '0%',
        'background-color': '#007bff'
    }).show();
    errorMessage.hide().text('');

    loaderInterval = setInterval(() => {
        if (loaderProgress < 90) {
            loaderProgress += 10;
            progressText.text(`Loading: ${loaderProgress}%`);
            progressBar.css('width', `${loaderProgress}%`);
        }
    }, 100);
}

function stopLoader(success = true, error = '') {
    const progressBar = $('#progress-bar');
    const progressText = $('#progress-bar');
    const errorMessage = $('#error-message');

    clearInterval(loaderInterval);  

    if (success) {
        loaderProgress = 100; 
        progressText.text(`Loading: 100% - Done`);
        progressBar.css({
            'width': '100%',
            'background-color': '#007bff'
        });

        setTimeout(() => {
            progressBar.hide();
            $("#daily_details").show(); 
        }, 500);
    } else {
        progressText.text('Error loading data');
        progressBar.css('background-color', 'red');
        errorMessage.text(`Failed to load data: ${error || 'Unknown error'}`).show();

        setTimeout(() => {
            progressBar.hide();
            $("#daily_details").hide(); 
        }, 1000);
    }
}

function morefilterview(uniqueResults, moredatefittervale, urlstatus, url,filtrData) {
	startLoader();
	$("#daily_details").hide();
	 if(uniqueResults!="")
    {
    var morefilltersall=uniqueResults.join(" AND ");
		stopLoader(true);
	   handleSuccess(filtrData,urlstatus);
   /* $.ajax({
        url: url,
        type: "GET",
        data: {
            morefilltersall: morefilltersall,
            moredatefittervale:moredatefittervale,
			apistatus:'regreport'
        },
        success: function (responseData) {
			stopLoader(true);
           handleSuccess(responseData,urlstatus);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });*/
	}else
   {
	if(urlstatus == 1){
      $(".search_views").hide();
	  $('.clear_views').hide();
      overall_fetch(2);
	}
   }
}	

	var ticketdataSource = [];  // Data will be fetched here
	var resData = [];  // Data will be fetched here

	// Fetch the data and initialize pagination
	function overall_fetch(statusid) {
		startLoader();
		$("#table2").hide();
		$("#table1").show();
		var moredatefittervale = $('#dateallviews').text();
		$(".value_views_mainsearch").text("");
	
		$.ajax({
			url: regfetchUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid,
				apistatus:'regreport'
			},
			success: function (responseData) {
				stopLoader(true);
				resData =responseData.data;
				var container =$(".options_branch.brachviewsall");
				container.empty(); 
				responseData.dropdown.forEach(function(doc) {					
						var options = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', doc.name)
							.text(doc.name);
						container.append(options);					
				});
				handleSuccess(responseData.data,1);
			},
			error: function (xhr, status, error) {
				stopLoader(false, error); 
            console.error("❌ AJAX Error:", status, error);
			}
		});
	}
	
	var fitterremovedata = [];	
var totalItems;                                                 		
function handleSuccess(responseData,urlstatus) {	
	if(urlstatus == 1){
		$("#daily_details").show();	
		ticketdataSource = responseData; 
		totalItems = responseData.length; 
		var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); 
		ticketrenderPagination(ticketdataSource, ticketpageSize);			
		ticketrenderTable(ticketdataSource, ticketpageSize, 1); 
	}else{
		ticketdataSource = responseData; 
		totalItems = responseData.length; 
		var ticketpageSize = parseInt($('#reportPerPageSelect').val()); 
		reportrenderPagination(ticketdataSource, ticketpageSize);
		reportrenderTable(ticketdataSource, ticketpageSize, 1)
	}
}

let storedRegAt = null;
let storedPhid = null;
let storedCity = null;
let debounceTimer;
$('#reportviews').on('keyup input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
        $('#report_details').hide();
        $('#report_details11').show();

        let ph_id = $('#reportviews').val().trim();		
        if (!storedRegAt || !storedPhid || !storedCity) {
            storedRegAt = $('td.tdview[data-reg_at]').data('reg_at');
            storedPhid = $('td.tdview[data-ph_id]').data('ph_id');
            storedCity = $('td.tdview[data-city]').data('city');
        }

        $.ajax({
            url: regViewUrlfitter,
            type: "GET",
            data: {
                ph_id: ph_id,
                cdate: storedRegAt,
                phid: storedPhid,
                city: storedCity,
                status_id: 1
            },
            success: function (response) {
                $('#report_details11').hide();
                $('#report_details').show();
                handleSuccess(response, 5);
            }
        });

    }, 300);
});

$(document).on("click", ".value_views_mainsearch", function () {
    $('.search_views').show();
		const badgeId = $(this).attr('id');		
		const mapping = {
			'created_by_search': '#zone_views',
			'branch_search': '#branchviews',
			'zone_search': '#mrd_phid'
		};
		if (mapping[badgeId]) {
			$(mapping[badgeId]).val(''); 
		}
		const zoneValue = $('#zone_views').val();		
		$.ajax({
            url: regfetchBranch,
            type: "GET",
            data: {
                zone:zoneValue 
            },
            success: function (response) { 
				var container =$(".options_branch.brachviewsall");
				container.empty(); 
				response.forEach(function(doc) {					
						var options = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', doc.name)
							.text(doc.name);
						container.append(options);					
				});
            }
        }); 
		triggerFilter1();
});

var filtrData = [];

function getNestedValue(obj, path) {
    return path.split('.').reduce((o, p) => (o && o[p] !== undefined ? o[p] : undefined), obj);
}

function triggerFilter1() {
    $(".clear_views").show();
        $('.search_views').css('display', 'inline');
   
    const moreDateFilterValue = $('#dateallviews').text();
    const filterConditions = {};
    const resultsArrayMarketer = [];
    
    // Collect all input filter values
    $(".marketervalues_search").each(function () {
        const value = $(this).val().trim();
        const key = $(this).attr('name');

        if (value) {
            filterConditions[key] = value;
            resultsArrayMarketer.push(`${key}='${value}'`);
        } else {
            // Ensure empty inputs are not considered in filtering
            delete filterConditions[key];
        }
    });
     
    const zoneAreaMap = {
        "KARNATAKA": ["Bengaluru - Konanakunte","Bengaluru - Electronic City","Bengaluru - Hebbal","Bengaluru - Dasarahalli"],
        "TN CHENNAI": ["Old Mahabalipuram Road","Chennai - Madipakkam","Kanchipuram","Thiruvallur","Chennai - Tambaram","Chennai - Vadapalani","Chennai - Urapakkam", "Chennai - Sholinganallur","Chengalpattu", "Thiruporur"],
        "TN SOUTH": ["Trichy", "Tanjore", "Madurai","Villupuram","Nagapattinam","Sivakasi"],
        "KERALA": ["Kerala - Palakkad", "Kerala - Kozhikode"],
        "AP + TN VELLORE": ["Tirupati", "Vellore","Thirupathur"],
        "TN CENTRAL": ["Hosur", "Salem", "Harur","Kallakurichi","Thiruvannamalai","Aathur","Namakal","Pennagaram"],
        "TN WEST 1": ["Coimbatore - Ganapathy","Coimbatore - Sundarapuram","Pollachi","Coimbatore - Thudiyalur"],
        "TN WEST 2": ["Tiruppur","Erode","Karur","Sathyamangalam"]
        // Add more zone mappings if needed
    };

    if (filterConditions['tblzones']) {
        const zoneValue = filterConditions['tblzones'].toUpperCase();
        const allowedAreas = zoneAreaMap[zoneValue] || [];

        filtrData = resData.filter(person => {
            const otherFiltersPass = Object.entries(filterConditions).every(([key, val]) => {
                if (key === 'tblzones') return true;
                const actualValue = getNestedValue(person, key);
                return String(actualValue).trim() === String(val).trim();
            });

            if (!otherFiltersPass) return false;

            if (allowedAreas.length > 0) {
                return allowedAreas.includes(person.area);
            }

            return true;
        });
    } else {
        filtrData = resData.filter(person => {
            return Object.entries(filterConditions).every(([key, val]) => {
                const actualValue = getNestedValue(person, key);
                return String(actualValue).trim() === String(val).trim();
            });
        });
    }

    const formattedFilters = resultsArrayMarketer.map(f => f.replace(/, /g, ','));
    morefilterview(formattedFilters, moreDateFilterValue, 1, fetchBranchUrlfitter, filtrData);
}

function triggerFilter() {
    $(".clear_views").show();
    $('.search_views').css('display', 'inline');

    const moreDateFilterValue = $('#dateallviews').text();
    const filterConditions = {};
    const resultsArrayMarketer = [];
    // return;
    // Collect all input filter values
    $(".marketervalues_search").each(function () {
        const value = $(this).val().trim();
        const key = $(this).attr('name');

        if (value) {
            filterConditions[key] = value;
            resultsArrayMarketer.push(`${key}='${value}'`);
        } else {
            // Ensure empty inputs are not considered in filtering
            delete filterConditions[key];
        }
    });

    // Update the text and visibility of each .value_views_mainsearch badge
    const moreFilterValues_market = [
        $("#zone_views").val(),
        $("#branchviews").val(),
        $("#mrd_phid").val(),
    ];

    $(".value_views_mainsearch").each(function (index) {
        const value = moreFilterValues_market[index] || "";
        $(this).text(value);

        if (value) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });

    // Zone-based area mapping
    const zoneAreaMap = {
        "KARNATAKA": ["Bengaluru - Konanakunte","Bengaluru - Electronic City","Bengaluru - Hebbal","Bengaluru - Dasarahalli"],
        "TN CHENNAI": ["Old Mahabalipuram Road","Chennai - Madipakkam","Kanchipuram","Thiruvallur","Chennai - Tambaram","Chennai - Vadapalani","Chennai - Urapakkam", "Chennai - Sholinganallur","Chengalpattu", "Thiruporur"],
        "TN SOUTH": ["Trichy", "Tanjore", "Madurai","Villupuram","Nagapattinam","Sivakasi"],
        "KERALA": ["Kerala - Palakkad", "Kerala - Kozhikode"],
        "AP + TN VELLORE": ["Tirupati", "Vellore","Thirupathur"],
        "TN CENTRAL": ["Hosur", "Salem", "Harur","Kallakurichi","Thiruvannamalai","Aathur","Namakal","Pennagaram"],
        "TN WEST 1": ["Coimbatore - Ganapathy","Coimbatore - Sundarapuram","Pollachi","Coimbatore - Thudiyalur"],
        "TN WEST 2": ["Tiruppur","Erode","Karur","Sathyamangalam"]
        // Add more zone mappings if needed
    };

    if (filterConditions['tblzones']) {
        const zoneValue = filterConditions['tblzones'].toUpperCase();
        const allowedAreas = zoneAreaMap[zoneValue] || [];

        filtrData = resData.filter(person => {
            const otherFiltersPass = Object.entries(filterConditions).every(([key, val]) => {
                if (key === 'tblzones') return true;
                const actualValue = getNestedValue(person, key);
                return String(actualValue).trim() === String(val).trim();
            });

            if (!otherFiltersPass) return false;

            if (allowedAreas.length > 0) {
                return allowedAreas.includes(person.area);
            }

            return true;
        });
    } else {
        filtrData = resData.filter(person => {
            return Object.entries(filterConditions).every(([key, val]) => {
                const actualValue = getNestedValue(person, key);
                return String(actualValue).trim() === String(val).trim();
            });
        });
    }

    const formattedFilters = resultsArrayMarketer.map(f => f.replace(/, /g, ','));
    morefilterview(formattedFilters, moreDateFilterValue, 1, fetchBranchUrlfitter, filtrData);
}

// When a dropdown option is selected
$(document).on('click', '.options_branch div', function () {
    const selectedValue = $(this).data('value');
    const parentInput = $(this).closest('.loct-dropdown').find('.marketervalues_search');
    parentInput.val(selectedValue).trigger('input');
	const zoneValue = $('#zone_views').val();
	$.ajax({
            url: regfetchBranch,
            type: "GET",
            data: {
                zone:zoneValue,
                branch:selectedValue 
            },
            success: function (response) { 
			//$('#branchviews').val('');
			//$('#branch_search').text('');
				var container =$(".options_branch.brachviewsall");
				container.empty(); 
				response.forEach(function(doc) {					
						var options = $('<div></div>')
							.addClass('dropdown-option')
							.attr('data-value', doc.name)
							.text(doc.name);
						container.append(options);					
				});
            }
        });
});

// Trigger filtering on typing or input changes
$(document).on('keyup input', '.marketervalues_search', function () {
    triggerFilter();
});


/*
$(document).on("click", ".value_views_mainsearch", function () {
		$('.search_views').show();
		const badgeId = $(this).attr('id');		
		const mapping = {
			'created_by_search': '#zone_views',
			'branch_search': '#branchviews',
			'zone_search': '#mrd_phid'
		};
		if (mapping[badgeId]) {
			$(mapping[badgeId]).val(''); 
		}
		$(this).remove(); 
		triggerFilter(); 
	});	

var filtrData = [];

function getNestedValue(obj, path) {
  return path.split('.').reduce((o, p) => (o && o[p] !== undefined ? o[p] : undefined), obj);
}

function triggerFilter() {
  $(".value_views").text("");
  $(".clear_views").show();
  $(".search_views").show();

  const moreDateFilterValue = $('#dateallviews').text();
  const filterConditions = {};
  const resultsArrayMarketer = [];

  // Collect all filter input values
$(".marketervalues_search").each(function () {
			var value = $(this).val().trim();
			var key = $(this).attr('name');

			if (value) {
				filterConditions[key] = value;
				resultsArrayMarketer.push(`${key}='${value}'`);
			}
			var moreFilterValues_market = [
					$("#zone_views").val(),
					$("#branchviews").val(),
					$("#mrd_phid").val(),
				];

				$(".value_views_mainsearch").each(function (index) {
					var morefillterdata_market = moreFilterValues_market[index] ? moreFilterValues_market[index] : ""; 
					$(this).text(morefillterdata_market);
				});
		});
	

  // Map zones to corresponding areas for filtering multiple branch areas
  const zoneAreaMap = {
    "KARNATAKA": ["Bengaluru - Konanakunte"],
    "TN CHENNAI": ["Chennai - Urapakkam", "Chengalpattu", "Thiruporur"],
    "TN SOUTH": ["Madurai", "Dindigul", "Harur"],
    "TN WEST 1": ["Erode"]
    // Add more zone mappings here as needed
  };

  if (filterConditions['tblzones']) {
    const zoneValue = filterConditions['tblzones'].toUpperCase();
    const allowedAreas = zoneAreaMap[zoneValue] || [];

    filtrData = resData.filter(person => {
      // Check other filters except zone first
      const otherFiltersPass = Object.entries(filterConditions).every(([key, val]) => {
        if (key === 'tblzones') return true; // skip zone in this loop
        const actualValue = getNestedValue(person, key);
        return String(actualValue).trim() === String(val).trim();
      });

      if (!otherFiltersPass) return false;

      // If zone areas defined, check if person's area is in allowed list
      if (allowedAreas.length > 0) {
        return allowedAreas.includes(person.area);
      }

      // No zone mapping - pass all
      return true;
    });
  } else {
    // No zone filter applied - filter normally by all keys
    filtrData = resData.filter(person => {
      return Object.entries(filterConditions).every(([key, val]) => {
        const actualValue = getNestedValue(person, key);
        return String(actualValue).trim() === String(val).trim();
      });
    });
  }

  const formattedFilters = resultsArrayMarketer.map(f => f.replace(/, /g, ','));
  morefilterview(formattedFilters, moreDateFilterValue, 1, fetchBranchUrlfitter, filtrData);
}

// When user clicks a dropdown option for branch or zone
$(document).on('click', '.options_branch div', function () {
  const selectedValue = $(this).data('value');
  const parentInput = $(this).closest('.loct-dropdown').find('.marketervalues_search');
  parentInput.val(selectedValue).trigger('input');
});

// Trigger filter on keyup or input on any filter input
$(document).on('keyup input', '.marketervalues_search', function () {
  triggerFilter();
});
*/
	
	// date fitter function
function ticketdatefillterrange(datefiltervalue,url,urlstatus,fitterremovedata,apistatus) {
	startLoader(); 
    currentFilter = datefiltervalue;
	var morefilltersall=fitterremovedata.join(" AND ");
    $("#daily_details11").show();
    $("#daily_details").hide();
    $.ajax({
        url: url,
        type: "GET",
        data: {
            moredatefittervale: currentFilter,
			morefilltersall: morefilltersall,
			apistatus:apistatus,
        },
        success: function (responseData) {
			 stopLoader(true); 
			 resData =responseData.data;			 
           handleSuccess(responseData.data,urlstatus);
        },
        error: function (xhr, status, error) {
            stopLoader(false, error); 
            console.error("❌ AJAX Error:", status, error);
        }
    });
}

// Render pagination controls based on data
function ticketrenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
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
	$("#rcounts").text(totalItems);
    $('.page-bttn').click(function() {
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

// Render pagination controls based on data
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
	$("#rpcounts").text(totalItems);
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

	// Render table rows based on the page and page size
	function ticketrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
		var body = "";
			body = ticketData(pageData,body);

		$("#daily_details").html(body);
		$("#today_visits").text(totalItems);
		$("#rcounts").text(totalItems);
	}	
	
	// Render table rows based on the page and page size
	function reportrenderTable(data, ticketpageSize, pageNum) {
		$("#report_details").show();
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);
		var body = "";
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
			
			let dateStr = usr.created_at;
			let regdate = usr.registrationdate;
			let dob = usr.dob;		
			let formattedDate = moment(dateStr, "YYYYMMDDHH:mm:ss").format("DD MMM YYYY HH:mm:ss");
			    regdate = moment(regdate, "YYYYMMDD").format("DD MMM YYYY");
				dob = dob ? moment(dob).format("DD MMM YYYY") : "-";
			
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview"><strong>#' + sno + '</strong></a><br>' + formattedDate +'</td>' +	
					'<td class="tdview">' + usr.name + '</td>' +					
					'<td class="tdview" data-mobile="' + usr.mobile + '">' + usr.mobile.substring(0, 2) + '********' + usr.mobile.substring(usr.mobile.length - 2) + '</td>' +					
					'<td class="tdview">' + dob + '</td>' +					
					'<td class="tdview">' + usr.gender + '</td>' +					
					'<td class="tdview">' + usr.agey + '</td>' +					
					'<td class="tdview" data-ph_id="' + usr.phid + '">' + usr.phid + '</td>' +					
					'<td class="tdview" data-city="' + usr.city + '">' + usr.city + '</td>' +					
					'<td class="tdview" data-reg_at="' + usr.registrationdate + '">' + regdate + '</td>' +					
				   '</tr>';
				   sno++;
			});
		}

		$("#report_details").html(body);
		$("#today_visits").text(totalItems);
		$("#rpcounts").text(totalItems);
	}
	
	function ticketData(pageData,body){
		var sno = 1;
		if (pageData.length === 0) {
			body += '<tr><td colspan="7" class="tdview" style="text-align: center;">No data available</td></tr>';
		} else {
		$.each(pageData, function(index, usr) {
				let dateStr = usr.created_at;
			let regdate = usr.registrationdate;
			let dob = usr.dob;		
			let formattedDate = moment(dateStr, "YYYYMMDDHH:mm:ss").format("DD MMM YYYY HH:mm:ss");
			    regdate = moment(regdate, "YYYYMMDD").format("DD MMM YYYY");
				dob = dob ? moment(dob).format("DD MMM YYYY") : "-";
			
			body += '<tr onclick="rowClick(event)">' +
				   '<td class="tdview"><strong>#' + sno + '</strong></a><br>' + formattedDate +'</td>' +	
					'<td class="tdview">' + usr.name + '</td>' +					
					'<td class="tdview" data-mobile="' + usr.mobile + '">' + usr.mobile+ '</td>' +					
					'<td class="tdview">' + dob + '</td>' +					
					'<td class="tdview">' + usr.gender + '</td>' +					
					'<td class="tdview">' + usr.age + '</td>' +					
					'<td class="tdview" data-ph_id="' + usr.phid + '">' + usr.phid + '</td>' +				
					'<td class="tdview">' + usr.area + '</td>' +				
					'<td class="tdview" data-reg_at="' + usr.registrationdate + '">' + regdate + '</td>' +					
				   '</tr>';
				   sno++;
			});
		}
		return body;
	}		
	
	$(document).on('click', '#regview', function (e) {
		$("#table2").show();
		$("#table1").hide();
		$('.report_search').val('');
		$("#report_details11").show();		
		$("#report_details").hide();		
		var row = $(this).closest('tr');
		var phid = row.find('#regview').data('phid');
		var area = row.find('#regview').data('area');
		var cdate = row.find('#regview').data('created_at').substring(0, 8);
		$.ajax({
                url: regViewUrlfitter,
                type: "GET",
				data: {
						phid: phid,
						cdate:cdate,
						city:area
					},
                success: function(response) {					
					/*	const dropdown = document.getElementById('mobileDropdown');
						response.forEach(user => {
							const formattedDate = user.registrationdate;
							const fullValue = `${user.mobile} | ${formattedDate} | ${user.phid} | ${area}`;
							const option = document.createElement('div');
							option.textContent = user.mobile;
							option.setAttribute('data-fullvalue', fullValue);
							dropdown.appendChild(option);
						});
						dropdown.addEventListener('click', function(e) {
							if (e.target && e.target.matches("div[data-value]")) {
								document.getElementById('reportviews').value = e.target.getAttribute('data-value');
							}
						});*/
						$("#report_details11").hide();
					handleSuccess(response,5);
                }
            });
	});	
	
    $('.ranges, .applyBtn').on('click', function() {
		console.log(fitterremovedata);
		// Check if the click happened on a specific class
        if ($(this).hasClass('ranges')) {
			
				var datefilltervalue = $('#dateallviews').text();
				var url = regfetchUrl;
				var urlstatus = 1;
						            
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata,'regreport');
            
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
           
				var url = regfetchUrl;
				var urlstatus = 1;			
			            
            ticketdatefillterrange(datefilltervalue,url,urlstatus,fitterremovedata,'regreport');
        }
    });


    $(document).on("click", ".clear_views", function () {
      	 $("#daily_details").hide();
		fitterremovedata.length = 0;		
		$('.marketervalues_search').val("")
        $(".value_views").text("");
        $('.morefittersclr').val("");
        $('.clear_views').hide();
        $(".search_views").hide();
		$(".value_views_mainsearch").text("");
        overall_fetch(1);
		
    
    });
	
	$('.report_views_mainsearch, .clear_report').on('click', function() {
		$('#mobileDropdown').empty();
		$('#branchviews').val("")
		$(".report_views_mainsearch").hide();
		$('.clear_views').hide();
		$('.clear_report').hide();
		$('.search_views').hide();
		$('.search_report').hide();
		$("#daily_details").hide();		
		fitterremovedata.length = 0;	
          overall_fetch(1);
   
    });
	
    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
    // Handle items per page change
    $('#reportPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        reportrenderPagination(ticketdataSource, ticketpageSize);
        reportrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
	
});
