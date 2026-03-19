$(document).ready(function () {
  let clicked_data = sessionStorage.getItem("clicked_data"); // Retrieve stored data
  let cashtype = sessionStorage.getItem("cashtype"); // Retrieve stored data

  $("#incometypesss").text(cashtype);
  $("#incomemdl").text(clicked_data);



    overallfetchbill_lists(clicked_data,cashtype);

       // Handle items per page change
   $('#itemsPerPageSelectbitslsls').change(function() {
    var pageSizebill_overall = parseInt($(this).val());
    paginatebill(dataSourceusersbill_overall, pageSizebill_overall);
    billtbl(dataSourceusersbill_overall, pageSizebill_overall, 1);  // Initially show the first page
});


});

function overallfetchbill_lists(clicked_data,cashtype)
{
  $.ajax({
    url: bill_overall_list_get,
    type: "GET",
    data:
    {
      clicked_data:clicked_data,
      cashtype:cashtype,
    },
    success: handleSuccessbill_overall,
    error: handleErrorbill_overall,
  });
}

// Handle successful AJAX response
function handleSuccessbill_overall(responseData) {
  // console.log(responseData);
  dataSourceusersbill_overall = responseData;
  totalItemscamp = responseData.length; // Get total items count
  var pageSizebill_overall = parseInt($('#itemsPerPageSelectbitslsls').val()); // Get selected items per page
  paginatebill(dataSourceusersbill_overall, pageSizebill_overall);
  billtbl_overall(dataSourceusersbill_overall, pageSizebill_overall, 1); // Show first page initially
}
function handleErrorbill_overall(xhr, status, error) {
  console.error("AJAX Error:", status, error);
}

// Render table rows based on the page and page size
function billtbl_overall(data, pageSizebill_overall, pageNum) {
  var startIdx = (pageNum - 1) * pageSizebill_overall;
  var endIdx = pageNum * pageSizebill_overall;
  var pageData = data.slice(startIdx, endIdx);
  //console.log(pageData);
  var body = "";
  let camp_count=0;
  $.each(pageData, function(index, user) {

    //const finalUrl = bill_overall_list.replace('__BILL_ID__', user.id);
    camp_count++;
      let dateStr = user.created_at;

      let formattedDate = moment(dateStr).format("| DD MMM | YYYY |");

      body += '<tr onclick="rowClick(event)">' +
              '<td class="tdview" > <strong>#' + user.id + '</strong><br>' + formattedDate + '<br></td>' +
              '<td class="tdview" > <strong>' + user.billdate+ '</strong></td>' +
              '<td class="tdview" > <strong>' + user.billno + '</strong></td>' +
              '<td class="tdview" > <strong><a href="'+patientdashboard+'">' + user.phid + '</a></strong></td>' +
              '<td class="tdview" > <strong>' + user.patientname + '</strong></td>' +
              '<td class="tdview" > <strong>' + user.consultant + '</strong></td>' +
              '<td class="tdview" > <strong>' + user.amt + '</strong></td>' +
              '<td class="tdview" > <strong>' + user.billtype + '</strong></td>' +
              '<td class="tdview" > <strong>' + user.paymenttype + '</strong></td>' +
              '<td class="tdview" > <strong>' + user.user + '</strong></td>' +

             '</tr>';  });

  $("#bill_overall_list_all").html(body);

}
function paginatebill(data, pageSizebill_overall) {
  var totalPages = Math.ceil(data.length / pageSizebill_overall);
  var paginationHtmlbill_overall = '';
  for (var i = 1; i <= totalPages; i++) {
      paginationHtmlbill_overall += '<button class="page-btnviewss " style="background-color:#b163a6;"  data-page="' + i + '">' + i + '</button>';
  }
  $('#paginationbitslsls').html(paginationHtmlbill_overall);
  // Bind click event to each pagination button
  $('.page-btnviewss').click(function() {
      var pageNum = $(this).data('page');
      $('.page-btnviewss').removeClass('active');
      $(this).addClass('active');
      billtbl_overall(data, pageSizebill_overall, pageNum);
  });
}