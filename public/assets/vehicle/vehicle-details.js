$(document).ready(function() {
    // Fetch data and initialize pagination
    overall_fetch(1);
	document_fetch(1);
    $(".search_view").hide();
    $(".my_search_view").hide();
	$('#start_date').hide();
	$('#end_date').hide();
	$('#close-button').on('click', function() {
		location.reload();
	});
	var targetTab = "";
	
	 $(".nav-link").click(function() {
         targetTab = $(this).data("bs-target");
		 
		if(targetTab == "#analytics-tab-2-pane"){
			$("#documentbtn").show();
			$("#document_btn").hide();
		}
		if(targetTab == "#analytics-tab-1-pane"){
			$("#documentbtn").hide();
			$("#document_btn").show();
		}
    });
	
	function  morefilltersremoveviews(fitterremovedata,url,urlstatus)
{

    if(fitterremovedata!="")
    {
        var fitterremovedataall=fitterremovedata.join(" AND ");
   

   
   $.ajax({
    url: url,
    type: "GET",
    data: {
        fitterremovedataall: fitterremovedataall,

    },
    success: function (responseData) {
        handleSuccess(responseData,urlstatus);
    },
    error: function (xhr, status, error) {
        $("#ticket_details1").hide();
        console.error("AJAX Error:", status, error);
    }
  });

   }else
   {
	if(urlstatus == 1){
      $(".search_view").hide();
	  $('.clear_views').hide();
      overall_fetch(2);
	}else{
		$('.clear_my_views').hide();
      $(".my_search_view").hide();
      document_fetch(1);
	}
   }

}
	
	var ticketdataSource = [];  // Data will be fetched here
    var marketersearchvalue = [];
	// Fetch the data and initialize pagination
	function overall_fetch(statusid) {
		var moredatefittervale = $('#dateallviews').text();
		$(".value_views_mainsearch").text("");
		$.ajax({
			url: vehiclefetchUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid
			},
			success: function (responseData) {
				handleSuccess(responseData,1);
			},
			error: function (xhr, status, error) {
				$("#ticket_details1").hide();
				console.error("AJAX Error:", status, error);
			}
		});
	}	
	
	// Fetch the data and initialize pagination
	function document_fetch(statusid) {
		var moredatefittervale = $('#mydateallviews').text();
		$(".value_views_mysearch").text("");
		$("#my_ticket_details1").show();
		$.ajax({
			url: vehicledocumentUrl,
			type: "GET",
			data: {
				moredatefittervale: moredatefittervale,
				statusid:statusid
			},
			success: function (responseData) {
				handleSuccess(responseData,3);
			},
			error: function (xhr, status, error) {
				console.error("AJAX Error:", status, error);
			}
		});
	}
	
	
function morefilterview(uniqueResults,urlstatus,url,moredatefittervale) {
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
            handleSuccess(responseData,urlstatus);
        },
        error: function (xhr, status, error) {
            $("#ticket_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
	}else
   {
	if(urlstatus == 1){
      $(".search_view").hide();
	  $('.clear_views').hide();
      overall_fetch(2);
	}else{
		$('.clear_my_views').hide();
      $(".my_search_view").hide();
      document_fetch(2);
	}
   }
}
	
	function handleSuccess(responseData,urlstatus) {
	if(urlstatus == 1){
			$("#ticket_details1").hide();
			$("#ticket_details").show();
			ticketdataSource = responseData; 
			totalItems = responseData.length; // Set the data fetched from the server
			var ticketpageSize = parseInt($('#itemsPerPageSelect').val()); // Get selected items per page
			ticketrenderPagination(ticketdataSource, ticketpageSize);
			ticketrenderTable(ticketdataSource, ticketpageSize, 1); // Show first page initially
	}
	
	if(urlstatus == 3){
			$("#my_ticket_details1").hide();
			$("#my_ticket_details").show();
			myticketdataSource = responseData; 
			mytotalItems = responseData.length; // Set the data fetched from the server
			var myticketpageSize = parseInt($('#myitemsPerPageSelect').val()); // Get selected items per page
			myticketrenderPagination(myticketdataSource, myticketpageSize);
			myticketrenderTable(myticketdataSource, myticketpageSize, 1); // Show first page initially
	}
}

	// Render table rows based on the page and page size
	function myticketrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);

		var body = "";
		$.each(pageData, function(index, user) {
			let dateStr = user.expire_date;
			let formattedDate = moment(dateStr).format("DD-MM-YYYY");
		
			body += '<tr onclick="rowClick(event)">' +
						'<td class="tdview"> ' + user.vehicle_no + '</td>' +
					   '<td class="tdview" id="idfetch" data-id="'+ user.did +'">'+ user.type +'</td>' +
					   '<td class="tdview" id="model">' + user.make + '</td>' +		
					   '<td style="display:none;" id="pdffiles" class="tdview" >'+ user.document_name +'</td>' +
					   '<td class="tdview" id="model">' + user.registration_number + '</td>' +		
						'<td class="tdview">';

						// Corrected fuel_type handling
						if (user.document_type == "1") {
							body += 'Insurance Document';
						} else if (user.document_type == "2") {
							body += 'Registration Certificate Document';
						} else if (user.document_type === "3") {
							body += 'Vehicle Verification Certificate';
						} else if (user.document_type === "4") {
							body += 'Vehicle Inspection Certificate';
						} 

						// Closing the table cell and row tags
						body += '</td>' +
						'<td class="tdview">';

						// Corrected fuel_type handling
						if (user.fuel_type == "1") {
							body += 'Petrol';
						} else if (user.fuel_type == "2") {
							body += 'Diesel';
						} else if (user.fuel_type === "3") {
							body += 'Electronic Vehicle';
						} else if (user.fuel_type === "4") {
							body += 'CNG';
						} else {
							body += '<span class="new-badge">' + user.fuel_type + '</span>';
						}

						// Closing the table cell and row tags
						body += '</td>' +
						 '<td style="display:none;" id="year_of_manufacture" class="tdview" >'+ user.year_of_manufacture +'</td>' +
						 '<td style="display:none;" id="pffiles" class="tdview" >'+ user.document_name +'</td>' +
						 '<td style="display:none;" id="expire_dates" class="tdview" >'+ user.expire_dates +'</td>' +
						'<td class="tdview documentclk"  ><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>' +
						'<td class="tdview upload_document"  ><img src="../assets/images/policy.png" style="width: 35px;"  alt="Icon" class="icon"></td>' +	
						'<td class="tdview">' + formattedDate + '</td>' +								
				   '</tr>';
		});
		
		$("#my_ticket_details").html(body);
		$("#today_visits").text(mytotalItems);
		$("#mycounts").text(mytotalItems);
	}
	
	// Render pagination controls based on data
	function myticketrenderPagination(data, ticketpageSize) {
		var totalPages = Math.ceil(data.length / ticketpageSize);
		var paginationHtml = '';

		for (var i = 1; i <= totalPages; i++) {
			paginationHtml += '<button class="my-page-bttn " style="background-color:#6a6ee4;color: #fff;"  data-page="' + i + '">' + i + '</button>';
		}

		$('#myticketpagination').html(paginationHtml);

		// Bind click event to each pagination button
		$('.my-page-bttn').click(function() {
			var pageNum = $(this).data('page');
			$('.my-page-bttn').removeClass('active');
			$(this).addClass('active');
			myticketrenderTable(data, ticketpageSize, pageNum);
		});
	}
	
var fitterremovedata = [];	
$("#vehicle_search").on("click", function () {
	  $(".search_view").show();
	  $('.clear_views').show();
		$(".value_views_mainsearch").text("");
	  $('.vehiclevalues_search').val('');
      let vehicle_type =  $("#vehicle_type").val(); 
      let fuel_type =  $("#vfuel_type").val();
	  
	   var resultsArray = [];
        $(".morefittersclr").each(function () {

            var value = $(this).find(":selected").text();
			if(value == "Select Vehicle Type"){
			  value = "";
			}
			  if(value == "Select Fuel Type"){
				  value = "";
			  }
			// Check if the value is not empty before processing
			if (value === "") {
				return; // Skip this iteration if the value is empty
			}
			var results = $(this).attr('name') + "='" + value.replace(/\s*-\s*/, '-').trim() + "'";
			
			resultsArray.push(results);

			   fitterremovedata=resultsArray;
		   
				});
			fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
			   fitterremovedata;
	  
	  var moreFilterValues = [
            $("#vehicle_type").find(":selected").text(),
            $("#vfuel_type").find(":selected").text()
        ];
		
	 $(".value_views").each(function (index) {
            var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; // Use "N/A" if value is empty
         
		  if(morefillterdata == "Select Vehicle Type"){
			  morefillterdata = "";
		  }
		  if(morefillterdata == "Select Fuel Type"){
			  morefillterdata = "";
		  }
            $(this).text(morefillterdata);

        });	
		ticketFilterAjax(vehicle_type,fuel_type,fetchdocUrlfitter,1);	
    });	
	
$("#my_vehicle_search").on("click", function () {
	  $('.my_search_view').show();
	  $('.clear_my_views').show();
	  $(".value_views_mysearch").text("");
	  $('.veh_search').val('');
      let vehicle_type =  $("#veh_type").val(); 
      let fuel_type =  $("#fuel_typ").val();
	  
	   var resultsArray = [];
        $(".myfittersclr").each(function () {

            var value = $(this).find(":selected").text();
			if(value == "Select Vehicle Type"){
			  value = "";
			}
			  if(value == "Select Fuel Type"){
				  value = "";
			  }
			// Check if the value is not empty before processing
			if (value === "") {
				return; // Skip this iteration if the value is empty
			}
			var results = $(this).attr('name') + "='" + value.replace(/\s*-\s*/, '-').trim() + "'";
			
			resultsArray.push(results);

			   fitterremovedata=resultsArray;
		   
				});
			fitterremovedata = fitterremovedata.map(filter => filter.replace(/, /g, ','));
			   fitterremovedata;
	  
	  var moreFilterValues = [
            $("#veh_type").find(":selected").text(),
            $("#fuel_typ").find(":selected").text()
        ];
		
	 $(".my_value_views").each(function (index) {
            var morefillterdata = moreFilterValues[index] ? moreFilterValues[index] : ""; // Use "N/A" if value is empty
         
		  if(morefillterdata == "Select Vehicle Type"){
			  morefillterdata = "";
		  }
		  if(morefillterdata == "Select Fuel Type"){
			  morefillterdata = "";
		  }
            $(this).text(morefillterdata);

        });	
		ticketFilterAjax(vehicle_type,fuel_type,fetchmoreUrlfitter,3);	
    });
	
	function ticketFilterAjax(vehicle_type,fuel_type,url,urlstatus){
	$.ajax({
			url: url,
			type: "GET",
			data: {
				vehicle_type: vehicle_type,
				fuel_type: fuel_type,
			},
			success: function (responseData) {
				handleSuccess(responseData,urlstatus);
			},
			error: function (xhr, status, error) {
				$("#ticket_details1").hide();
				console.error("AJAX Error:", status, error);
			}
	  });
}
	
    $(document).on('click', '.vehicle_marketers div', function () {
			// Get the selected registration number value
			const selectedValue = $(this).data('value');
			const selectedText = $(this).text();
			
			// Show UI elements
			$('.clear_views').show();
			$(".search_view").show();
			$(".value_views").text("");
			// Initialize an array to hold the filtered results
			let resultsArray_marketer = [];

			// Collect the values from the search inputs
			$(".vehiclevalues_search").each(function () {
				const value = $(this).val();
				
				if (value !== "") {
					const result =  $(this).attr('name') + "='" + value + "'";
					resultsArray_marketer.push(result);
				}
			});

			// Additional filter values
			const moreFilterValues_market = [
				$("#reg_number").val(),
				$("#branch_views").val(),
				$("#zone_views").val(),
			];

			// Update the UI with the selected filter values
			$(".value_views_mainsearch").each(function (index) {
				const filterValue = moreFilterValues_market[index] || ""; // Default to empty string if no value
				$(this).text(filterValue);
			});

			// Prepare data for the filter function
			 fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));

			// Call function with the processed data
			morefilterview(fitterremovedata,1,fetchUrldocfitter);
		});
	
	$(document).on('click', '.veh_options_marketers div', function () {
			$(".my_value_views").text("");
			  $('.clear_my_views').show();
			$(".my_search_view").show();
			var moredatefittervale = $('#mydateallviews').text();
			// Initialize an array to hold the filtered results
			let resultsArray_marketer = [];

			// Collect the values from the search inputs
			$(".veh_search").each(function () {
				const value = $(this).val();
				
				if (value !== "") {
					const result =  $(this).attr('name') + "='" + value + "'";
					resultsArray_marketer.push(result);
				}
			});

			// Additional filter values
			const moreFilterValues_market = [
				$("#veh_reg_number").val(),
				$("#veh_branch_views").val(),
				$("#veh_zone_views").val(),
			];

			// Update the UI with the selected filter values
			$(".value_views_mysearch").each(function (index) {
				const filterValue = moreFilterValues_market[index] || ""; // Default to empty string if no value
				$(this).text(filterValue);
			});

			// Prepare data for the filter function
			 fitterremovedata = resultsArray_marketer.map(filter => filter.replace(/, /g, ','));

			// Call function with the processed data
			morefilterview(fitterremovedata,3,fetchVehdocfitter,moredatefittervale);
		});
		
	// date fitter function
function ticketdatefillterrange(datefiltervalue,fitterremovedata) {
    currentFilter = datefiltervalue;
	var morefilltersall=fitterremovedata.join(" AND ");
    $("#ticket_details1").show();
    $.ajax({
        url: vehicledatefillter,
        type: "GET",
        data: {
            datefiltervalue: currentFilter,
			morefilltersall: morefilltersall,
   
        },
        success: function (responseData) {
            handleSuccess(responseData,3);
        },
        error: function (xhr, status, error) {
            $("#ticket_details1").hide();
            console.error("AJAX Error:", status, error);
        }
    });
}

// Render pagination controls based on data
function ticketrenderPagination(data, ticketpageSize) {
    var totalPages = Math.ceil(data.length / ticketpageSize);
    var paginationHtml = '';

    for (var i = 1; i <= totalPages; i++) {
        paginationHtml += '<button class="page-bttn " style="background-color:#6a6ee4;color: #fff;"  data-page="' + i + '">' + i + '</button>';
    }

    $('#ticketpagination').html(paginationHtml);

    // Bind click event to each pagination button
    $('.page-bttn').click(function() {
        var pageNum = $(this).data('page');
        $('.page-bttn').removeClass('active');
        $(this).addClass('active');
        ticketrenderTable(data, ticketpageSize, pageNum);
    });
}

	// Render table rows based on the page and page size
	function ticketrenderTable(data, ticketpageSize, pageNum) {
		var startIdx = (pageNum - 1) * ticketpageSize;
		var endIdx = pageNum * ticketpageSize;
		var pageData = data.slice(startIdx, endIdx);

		var body = "";
		$.each(pageData, function(index, user) {
			let dateStr = user.created_at;
			let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm:ss");
			
			body += '<tr onclick="rowClick(event)">' +
					   '<td class="tdview" style="width: 13%;">'  + user.year_of_manufacture + '<br>' + formattedDate + '</td>' +
					   '<td class="tdview"> ' + user.vehicle_no + '</td>' +
					   '<td class="tdview"> ' + user.name + '</td>' +
					   '<td class="tdview">'+ user.type +'</td>' +
					   '<td class="tdview">' + user.make + '</td>' +
					   '<td class="tdview">' + user.registration_number + '</td>' +
					   '<td class="tdview">' + user.engine_number + '</td>' +
					   '<td class="tdview">' + user.chassis_number + '</td>' +
					   '<td class="tdview">';

			// Corrected fuel_type handling
			if (user.fuel_type == "1") {
				body += 'Petrol';
			} else if (user.fuel_type == "2") {
				body += 'Diesel';
			} else if (user.fuel_type === "3") {
				body += 'Electronic Vehicle';
			} else if (user.fuel_type === "4") {
				body += 'CNG';
			} else {
				body += '<span class="new-badge">' + user.fuel_type + '</span>';
			}

			// Closing the table cell and row tags
			body += '</td>' +
			'<td class="tdview" id="edit_vehicle" data-id="'+ user.id +'"><img src="../assets/images/edit.png" style="width: 30px;"  alt="Icon" class="icon"></td>' +
				   '</tr>';
		});


		$("#ticket_details").html(body);
		$("#today_visits").text(totalItems);
		$("#counts").text(totalItems);
	}
	

    $('.ranges, .applyBtn').on('click', function() {
        // Check if the click happened on a specific class
        if ($(this).hasClass('ranges')) {
            var datefilltervalue = $('#mydateviewsall').text(); 
            ticketdatefillterrange(datefilltervalue,fitterremovedata);
            
        } else if ($(this).hasClass('applyBtn')) {
            var datefilltervaluenew = $('.drp-selected').text(); // Get the current text value when '.applyBtn' is clicked
            var dateRange = datefilltervaluenew.split(' - ');
            function convertDateFormat(dateStr) {
                let parts = dateStr.split('/');
                return `${parts[1]}/${parts[0]}/${parts[2]}`; // Rearrange as DD/MM/YYYY   
            }
            var startDate = convertDateFormat(dateRange[0]);
            var endDate = convertDateFormat(dateRange[1]);
            var datefilltervalue = `${startDate} - ${endDate}`;
            ticketdatefillterrange(datefilltervalue,fitterremovedata);
            
        }
    });
	
    $(document).on("click", ".value_views", function () {
		
        var morefillterremvedata = $(this).text().replace(/\s*-\s*/, '-').trim();
			
        $(this).text("");
		// Find the element that contains the value
		let indexToRemove = fitterremovedata.findIndex(item => item.includes(morefillterremvedata));

		if (indexToRemove !== -1) {
		  // Remove the element at the found index
		  var removedElement = fitterremovedata.splice(indexToRemove, 1)[0]; // splice returns an array, so we access the first element	  
		}
		
		// Split the string at the first '=' and get the part before it
			let key = removedElement.split('=')[0];
			
			if(key == 'vehicle_details.fuel_type'){
				$('#vfuel_type').prop('selectedIndex', 0);
			}
			if(key == 'vehicle_type.type'){
				$('#vehicle_type').prop('selectedIndex', 0);
			}
			
       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });	
	var datefilltervalue = $('#dateallviews').text();
          morefilltersremoveviews(fitterremovedata,fetchdocUrlfitter,1);
   
    });
	
    $(document).on("click", ".my_value_views", function () {
		
        var morefillterremvedata = $(this).text().replace(/\s*-\s*/, '-').trim();
			
        $(this).text("");
		let indexToRemove = fitterremovedata.findIndex(item => item.includes(morefillterremvedata));

		if (indexToRemove !== -1) {
		  // Remove the element at the found index
		  var removedElement = fitterremovedata.splice(indexToRemove, 1)[0]; // splice returns an array, so we access the first element	  
		}
		
		// Split the string at the first '=' and get the part before it
			let key = removedElement.split('=')[0];
			
			if(key == 'vehicle_details.fuel_type'){
				$('#fuel_typ').prop('selectedIndex', 0);
			}
			if(key == 'vehicle_type.type'){
				$('#veh_type').prop('selectedIndex', 0);
			}		
       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });	
	var datefilltervalue = $('#dateallviews').text();
          morefilltersremoveviews(fitterremovedata,fetchmoreUrlfitter,3);
   
    });
	
    $(document).on("click", ".value_views_mainsearch", function () {
	    var morefillterremvedata = $(this).text().replace(/, /g, ",");		
		var clear_filtr = $(this).attr('id');		
        $(this).text("");
		if(clear_filtr == 'created_by_search'){
			$('#reg_number').val('');	
		}
		if(clear_filtr == 'branch_search'){
			$('#branch_views').val('');	
		}
		if(clear_filtr == 'zone_search'){
			$('#zone_views').val('');	
		}
		
       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });			
	
          morefilterview(fitterremovedata,1,fetchUrldocfitter);
   
    });

$(document).on("click", ".value_views_mysearch", function () {
		var datefilltervalue = $('#mydateallviews').text();	
        var morefillterremvedata = $(this).text().replace(/, /g, ",");
		var clear_filtr = $(this).attr('id');		
        $(this).text("");
		if(clear_filtr == 'created_my_search'){
			$('#veh_reg_number').val('');	
		}
		if(clear_filtr == 'mybranch_search'){
			$('#veh_branch_views').val('');	
		}
		if(clear_filtr == 'myzone_search'){
			$('#veh_zone_views').val('');	
		}
		
       // Update the uniqueResults array to remove the corresponding filter
       fitterremovedata = fitterremovedata.filter(function (item) {
        return !item.trim().includes(morefillterremvedata.trim() + "'");
    });			
          morefilterview(fitterremovedata,3,fetchVehdocfitter,datefilltervalue);
   
    });

    $(document).on("click", ".clear_views", function () {
		fitterremovedata.length = 0;					
		$('.vehiclevalues_search').val("")
        $(".value_views").text("");
        $('.morefittersclr').val("");
        $('.clear_views').hide();
        $(".search_view").hide();
		$(".value_views_mainsearch").text("");
        overall_fetch(1);
    
    });
	
	$(document).on("click", ".clear_my_views", function () {
		
		fitterremovedata.length = 0;		
		$('.veh_search').val("")
        $(".my_value_views").text("");
        $('.myfittersclr').val("");
        $('.clear_my_views').hide();
        $(".my_search_view").hide();
		$(".value_views_mysearch").text("");
        document_fetch(2);
    
    });
		
    // Handle items per page change
    $('#itemsPerPageSelect').change(function() {
        var ticketpageSize = parseInt($(this).val());
        ticketrenderPagination(ticketdataSource, ticketpageSize);
        ticketrenderTable(ticketdataSource, ticketpageSize, 1);  // Initially show the first page
    });
	
	$('#myitemsPerPageSelect').change(function() {
        var myticketpageSize = parseInt($(this).val());
        myticketrenderPagination(myticketdataSource, myticketpageSize);
        myticketrenderTable(myticketdataSource, myticketpageSize, 1);  // Initially show the first page
    });
	
	$("#submit-document_update").on("click",function(){
        var updated_id=$("#docu_id").text();
        let formData = new FormData();
        formData.append('id', $('#id_document').val());
        formData.append('vehicle_id', $('.vehicle-dropdown-options div.selected').attr('data-value'));
        formData.append('expire_date', $('#expire_update_date').val());
        formData.append('expire_dates', $('#expire_dates').val());
        formData.append('document_type', $('#document_type').val());
        formData.append('year_of_manufacture', $('#year_of_manufacture').val());
        formData.append('make', $('#model').val());
		formData.append('update_documents_all', $('#update_documents_all').val());
         // Add images to FormData
        const file = $('#pdf_update')[0].files[0]; // Get the first (and only) file
		if (file) {
			formData.append('image', file); // Append the file with a single field name 'image'
		}
		 //console.log(formData);return;
           // Include CSRF Token
           formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
           // AJAX Request
           $.ajax({
               url: documentupdatedUrl,
               type: "POST",
               data: formData,
               processData: false, // Prevent processing of the data
               contentType: false, // Prevent setting content-type header
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
                       //location.reload(); // Optional: Refresh the page
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
					$('#exampleModal2').modal('hide');
				   $('#exampleModal2').find('input, textarea').val('');
				   $('.vehicle-dropdown-options div').removeClass('selected');
				   $('#document_type').prop('selectedIndex', 0); 
				   $('#pdf_update').replaceWith($('#pdf_update').clone(true));
				   document_fetch(1);
               },
               error: function (error) {
                   console.error(error.responseJSON);
               },
           });
    });
});

$(document).on('click', '.documentclk', function (e) {
        $('#exampleModal1').modal('show');
        var row = $(this).closest('tr');
        // Get data from the row
        var pdffiles = row.find('#pffiles').text();
        pdffiles = pdffiles.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        var firstImage = pdffiles.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        $('#pdfmain').attr('src', "../public/document_data/" + imageNamefirst);
        var imageArray = pdffiles.split(',');
        var views = '';
		console.log(imageArray);
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            imageNames = imageName.replace(/\\/g, ''); // Remove [ ] " and extra spaces
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<button style="font-size: 11px;" type="button" id="pdffetchdata" class="btn btn-primary pdf-btn">'+ imageNames +'</button>';
        });
         $('#image_pdfs').html("");
        $('#image_pdfs').html(views);
		
		// Add event listener for the PDF buttons
			$(document).on('click', '.pdf-btn', function() {
				// Remove 'active' class from all buttons
				$('.pdf-btn').removeClass('active');				
				// Add 'active' class to the clicked button
				$(this).addClass('active');
			});
    });

    $(document).on("click" , '.upload_document', function (e) {
        $('#exampleModal2').modal('show');
		$("#docu_vehicle_no").hide();
		$("#vehicle_document_type").hide();
        var row = $(this).closest('tr');
		var fetchid=row.find('#idfetch').data('id');
		var pdffilesviews = row.find('#pdffiles').text();
		var expire_dates = row.find('#expire_dates').text();
        var year_of_manufacture = row.find('#year_of_manufacture').text();
        var model = row.find('#model').text();
		$("#id_document").val(fetchid);
		$("#update_documents_all").val(pdffilesviews);
        $("#expire_dates").val(expire_dates);
        $("#model").val(model);
    });
 
 $(document).on('click', '#pdffetchdata', function (e) {
        fetchvalue=$(this).text();
      $('#pdfmain').attr('src', "../public/document_data/" + fetchvalue);	  
    });
	
 $(document).on('click', '#edit_vehicle', function (e) {
	$('#offcanvas_edit_vehicle').offcanvas('show');
		var id = $(this).closest('tr').find('#edit_vehicle').data('id');
		$.ajax({
                url: "/vehicledocumentedit/" + id,
                type: "GET",
                success: function(response) {
                    $('.branch-dropdown-options div[data-value="'+response.branch+'"]').addClass('selected');
					var selectedText = $('.branch-dropdown-options div[data-value="'+response.branch+'"]').text();
					$('.searchBranch').val(selectedText);
					$('.type-dropdown-options div[data-value="'+response.vehicle_type+'"]').addClass('selected');
					var selectedType = $('.type-dropdown-options div[data-value="'+response.vehicle_type+'"]').text();
					$('.searchType').val(selectedType);
                    $('#edit_id').val(response.id);
                    $('#vehicle_model').val(response.make);
                    $('#yr_of_manufacture').val(response.year_of_manufacture);
                    $('#registration_number').val(response.registration_number);
                    $('#engine_number').val(response.engine_number);
                    $('#chassis_number').val(response.chassis_number);
                    $('#fuel_type').val(response.fuel_type);
                }
            });	
 });