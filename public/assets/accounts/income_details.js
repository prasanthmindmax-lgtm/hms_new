$(document).ready(function(){


  overall_bills_views();

   // Handle items per page change
   $('#itemsPerPageSelectaccount').change(function() {
    var pageSizebill = parseInt($(this).val());
    paginatebill(dataSourceusersbill, pageSizebill);
    billtbl(dataSourceusersbill, pageSizebill, 1);  // Initially show the first page
});

});

function overall_bills_views()
{
  $.ajax({
    url: billalldetails,
    type: "GET",
    success: handleSuccessbill,
    error: handleErrorbill,
  });
}

// Handle successful AJAX response
function handleSuccessbill(responseData) {
  // console.log(responseData);
  dataSourceusersbill = responseData;
  totalItemscamp = responseData.length; // Get total items count
  var pageSizebill = parseInt($('#itemsPerPageSelectaccount').val()); // Get selected items per page
  paginatebill(dataSourceusersbill, pageSizebill);
  billtbl(dataSourceusersbill, pageSizebill, 1); // Show first page initially
}
function handleErrorbill(xhr, status, error) {
  console.error("AJAX Error:", status, error);
}
// Render table rows based on the page and page size
function billtbl(data, pageSizebill, pageNum) {
  var startIdx = (pageNum - 1) * pageSizebill;
  var endIdx = pageNum * pageSizebill;
  var pageData = data.slice(startIdx, endIdx);
  //console.log(pageData);
  var body = "";
  let camp_count=0;
  $.each(pageData, function(index, user) {

    var overalltotal =(parseInt(user.Cash) || 0) +(parseInt(user.Card) || 0) + (parseInt(user.Cheque) || 0) +(parseInt(user.DD) || 0) +(parseInt(user.Neft) || 0) +(parseInt(user.Credit) || 0) +(parseInt(user.UPI) || 0);

    //const finalUrl = bill_overall_list.replace('__BILL_ID__', user.id);
    camp_count++;
      let dateStr = user.created_at;

      let formattedDate = moment(dateStr).format("| DD MMM | YYYY |");

      body += '<tr onclick="rowClick(event)">' +
              '<td class="tdview" > <strong>#' + user.id + '</strong><br>' + formattedDate + '<br></td>' +
              '<td class="tdview" >'+ user.type +' </td>' +
              '<td class="tdview" > Vellor </td>' +
              '<td  data-cashtype="'+ user.type +'" id="types" class="tdview alldataviews" ><a data-value="Cash" href="' + bill_overall_list + '">' + user.Cash + '</a></td>'+
              '<td  class="tdview alldataviews" id="cardviewssss"> <a data-value="Card" href="' + bill_overall_list + '"> ' + user.Card + '</a></td>' +
              '<td  class="tdview alldataviews" > <a data-value="Cheque" href="' + bill_overall_list + '"> ' + user.Cheque + '</a></td>' +
              '<td  class="tdview alldataviews" > <a data-value="DD" href="' + bill_overall_list + '"> ' + user.DD + '</a></td>' +
              '<td  class="tdview alldataviews" > <a data-value="Neft" href="' + bill_overall_list + '"> ' + user.Neft + '</a></td>' +
              '<td  class="tdview alldataviews" >  <a data-value="Credit" href="' + bill_overall_list + '">' + user.Credit + '</a></td>' +
              '<td   class="tdview alldataviews" > <a data-value="UPI" href="' + bill_overall_list + '"> ' + user.UPI+ '</a></td>' +
              '<td class="tdview" >  ' + overalltotal + '</td>' +
             '</tr>';  });

  $("#bill_details_all").html(body);

}
function paginatebill(data, pageSizebill) {
  var totalPages = Math.ceil(data.length / pageSizebill);
  var paginationHtmlbill = '';
  for (var i = 1; i <= totalPages; i++) {
      paginationHtmlbill += '<button class="page-btnviewss " style="background-color:#b163a6;"  data-page="' + i + '">' + i + '</button>';
  }
  $('#paginationaccount').html(paginationHtmlbill);
  // Bind click event to each pagination button
  $('.page-btnviewss').click(function() {
      var pageNum = $(this).data('page');
      $('.page-btnviewss').removeClass('active');
      $(this).addClass('active');
      billtbl(data, pageSizebill, pageNum);
  });
}

$(document).on("click", ".alldataviews", function (e) {
  var clickedTd = $(this);  // Get the clicked TD
  var columnText = clickedTd.find('a').data('value');

  var row = clickedTd.closest('tr'); // Get the parent row of the clicked TD

  var cashtype = row.find('#types').data('cashtype'); // Get the cash type for that row

  sessionStorage.setItem("clicked_data", columnText);
  sessionStorage.setItem("cashtype", cashtype);
});


