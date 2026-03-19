$(document).ready(function(){

  $('#itemsPerPageincome').change(function() {
    var pageSizeincome = parseInt($(this).val());
    paginatincome(dataSourceusersincome, pageSizeincome);
    incomefetchdatas(dataSourceusersincome, pageSizeincome, 1);  // Initially show the first page
});

  $('.numberallowsss').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});


$(document).on('click', '.income_edit_views', function (e) {
  var userId = $(this).attr('value');
   // Find the corresponding row
   var row = $(this).closest('tr');
       // Get data from the row
  var dateviews = row.find('#user_icome_date').text();
  var moc_dc_cash = row.find('#moc_dc_cash').text();
  var radiant_cash_view = row.find('#radiant_cash_view').text();
  var bankst_cash_views = row.find('#bankst_cash_views').text();
  var moc_doc_card_views = row.find('#moc_doc_card_views').text();
  var orange_views = row.find('#orange_views').text();
  var bank_st_cardss = row.find('#bank_st_cardss').text();
  var moc_upi = row.find('#moc_upi').text();
  var orange_upi_viewssss = row.find('#orange_upi_viewssss').text();
  var banks_st_upi_viewss = row.find('#banks_st_upi_viewss').text();
  var moc_neft_views=row.find('#moc_neft_views').text();
  var bank_st_views_data=row.find("#bank_st_views_data").text();

  $("#icomedate_sees").text(dateviews);
  $("#moc_doc_cash").val(moc_dc_cash);
  $("#radiant_cash").val(radiant_cash_view);
  $("#bank_st_cash").val(bankst_cash_views);
  $("#moc_doc_card").val(moc_doc_card_views);
  $("#orange_card").val(orange_views);
  $("#bank_st_card").val(bank_st_cardss);
  $("#moc_doc_upi").val(moc_upi);
  $("#orange_upi").val(orange_upi_viewssss);
  $("#bank_st_upi").val(banks_st_upi_viewss);
  $("#moc_doc_neft").val(moc_neft_views);
  $("#bank_st_neft").val(bank_st_views_data);

});



overall_income_views();

  $(document).on('click', '.income_recon', function (e) {

    var dateviewsall=$("#icomedate_sees").text();
    var moc_dc_all=$("#moc_doc_cash").val();
    var radiants_all=$("#radiant_cash").val();
    var bank_st_all=$("#bank_st_cash").val();
    var moc_card_all=$("#moc_doc_card").val();
    var orange_card_all=$("#orange_card").val();
    var bank_st_card_all=$("#bank_st_card").val();
    var moc_upi_all=$("#moc_doc_upi").val();
    var ornage_upi_all=$("#orange_upi").val();
    var bank_st_upi=$("#bank_st_upi").val();
    var moc_doc_neft_all=$("#moc_doc_neft").val();
    var bank_st_neft_all=$("#bank_st_neft").val();

    overallvaleeditsall(dateviewsall,moc_dc_all,radiants_all,bank_st_all,moc_card_all,orange_card_all,bank_st_card_all,moc_upi_all,ornage_upi_all,bank_st_upi,moc_doc_neft_all,bank_st_neft_all);

 });



});

function overall_income_views()
{
  $.ajax({
    url: incomefetchdetails,
    type: "GET",
    success: handleSuccesincome,
      error: handleErrorincome
  });
}

function overallvaleeditsall(dateviewsall,moc_dc_all,radiants_all,bank_st_all,moc_card_all,orange_card_all,bank_st_card_all,moc_upi_all,ornage_upi_all,bank_st_upi,moc_doc_neft_all,bank_st_neft_all)
{
  $.ajax({
    url: incomeupdatedetails,
    type: "GET",
    data: {
      dateviewsall: dateviewsall,
      moc_dc_all: moc_dc_all,
      radiants_all: radiants_all,
      bank_st_all: bank_st_all,
      moc_card_all: moc_card_all,
      orange_card_all: orange_card_all,
      bank_st_card_all: bank_st_card_all,
      moc_upi_all: moc_upi_all,
      ornage_upi_all: ornage_upi_all,
      bank_st_upi: bank_st_upi,
      moc_doc_neft_all: moc_doc_neft_all,
      bank_st_neft_all: bank_st_neft_all
  },
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
}


function handleSuccesincome(responseData) {
  // console.log(responseData);
  dataSourceusersincome = responseData;
  totalItemscamp = responseData.length; // Get total items count
  var pageSizeincome = parseInt($('#itemsPerPageincome').val()); // Get selected items per page
  paginatincome(dataSourceusersincome, pageSizeincome);
  incomefetchdatas(dataSourceusersincome, pageSizeincome, 1); // Show first page initially
}
function handleErrorincome(xhr, status, error) {
  console.error("AJAX Error:", status, error);
}

function incomefetchdatas(data, pageSizeincome, pageNum) {
  var startIdx = (pageNum - 1) * pageSizeincome;
  var endIdx = pageNum * pageSizeincome;
  var pageData = data.slice(startIdx, endIdx);
  //console.log(pageData);
  var body = "";
  $.each(pageData, function(index, user) {

    // cash calucation..........

    var cash_different = parseInt((user.moc_doc_cash)-(user.radiant_cash));
    var bankst_different = parseInt((user.radiant_cash)-(user.bank_st_cash));

    radiant_cash = isNaN(parseFloat(user.radiant_cash)) ? "-" : user.radiant_cash;
    bankst_cash = isNaN(parseFloat(user.bank_st_cash)) ? "-" : user.bank_st_cash;

    // card caluctation

    var card_different = parseInt((user.moc_doc_card)-(user.orange_card));
    var card_bankst_different = parseInt((user.orange_card)-(user.bank_st_card));

    orange_card_views= isNaN(parseFloat(user.orange_card)) ? "-" : user.orange_card;
    bankst_card = isNaN(parseFloat(user.bank_st_card)) ? "-" : user.bank_st_card;

    // upi caluctation

    var upi_different = parseInt((user.moc_doc_upi)-(user.orange_upi));
    var upi_bankst_different = parseInt((user.orange_upi)-(user.bank_st_upi));

    orange_upi_views= isNaN(parseFloat(user.orange_upi)) ? "-" : user.orange_upi;
    bankst_upi = isNaN(parseFloat(user.bank_st_upi)) ? "-" : user.bank_st_upi;

        // neft caluctation

        var neft_different = parseInt((user.moc_doc_neft)-(user.bank_st_neft));

        moc_doc_neft_views= isNaN(parseFloat(user.moc_doc_neft)) ? "-" : user.moc_doc_neft;
        bankst_neft = isNaN(parseFloat(user.bank_st_neft)) ? "-" : user.bank_st_neft;

        diffclor = `<td style="color:${user.moc_doc_cash === user.radiant_cash ? 'green' : 'red'}; font-weight: bold; background-color: ${user.moc_doc_cash === user.radiant_cash ? '#d4edda' : '#f8d7da'};">${cash_different}</td>`;
        diffclorrbcash = `<td style="color:${user.radiant_cash === user.bank_st_cash ? 'green' : 'red'}; font-weight: bold; background-color: ${user.radiant_cash === user.bank_st_cash ? '#d4edda' : '#f8d7da'};">${bankst_different}</td>`;

        diffclorcard = `<td style="color:${user.moc_doc_card === user.orange_card ? 'green' : 'red'}; font-weight: bold; background-color: ${user.moc_doc_card === user.orange_card ? '#d4edda' : '#f8d7da'};">${card_different}</td>`;
        diffclorban = `<td style="color:${user.orange_card === user.bank_st_card ? 'green' : 'red'}; font-weight: bold; background-color: ${user.orange_card === user.bank_st_card ? '#d4edda' : '#f8d7da'};">${card_bankst_different}</td>`;

        diffclupi = `<td style="color:${user.moc_doc_upi === user.orange_upi ? 'green' : 'red'}; font-weight: bold; background-color: ${user.moc_doc_upi === user.orange_upi ? '#d4edda' : '#f8d7da'};">${upi_different}</td>`;
        diffclorupi = `<td style="color:${user.orange_upi === user.bank_st_upi ? 'green' : 'red'}; font-weight: bold; background-color: ${user.orange_upi === user.bank_st_upi ? '#d4edda' : '#f8d7da'};">${upi_bankst_different}</td>`;

        diffclorneft = `<td style="color:${user.moc_doc_neft === user.bank_st_neft ? 'green' : 'red'}; font-weight: bold; background-color: ${user.moc_doc_neft === user.bank_st_neft ? '#d4edda' : '#f8d7da'};">${neft_different}</td>`;

      body += '<tr>' +
              '<td>1</td>' +
              '<td id="user_icome_date">'+user.income_date+'</td>' +
              '<td id="moc_dc_cash">'+user.moc_doc_cash+'</td>' +
              '<td id="radiant_cash_view">'+radiant_cash+'</td>' +
              diffclor +
              '<td id="bankst_cash_views">'+bankst_cash+'</td>' +
              diffclorrbcash +
              '<td id="moc_doc_card_views">'+user.moc_doc_card+'</td>' +
              '<td id="orange_views">'+orange_card_views+'</td>' +
              diffclorcard +
              '<td id="bank_st_cardss">'+bankst_card+'</td>' +
              diffclorban +
              '<td id="moc_upi">'+user.moc_doc_upi+'</td>' +
              '<td id="orange_upi_viewssss">'+orange_upi_views+'</td>' +
              diffclupi +
              '<td id="banks_st_upi_viewss">'+bankst_upi+'</td>' +
              diffclorupi +
              '<td id="moc_neft_views">'+moc_doc_neft_views+'</td>' +
              '<td id="bank_st_views_data">'+bankst_neft+'</td>' +
              diffclorneft +
              '<td class="income_edit_views"><img src="../assets/images/pen.png" style="width: 20px;"  alt="Icon" data-bs-toggle="offcanvas" data-bs-target="#announcement" class="icon"></td>' +
             '</tr>';  });

  $("#income_tbl_details").html(body);
}
function paginatincome(data, pageSizeincome) {
  var totalPages = Math.ceil(data.length / pageSizeincome);
  var paginationHtml = '';
  for (var i = 1; i <= totalPages; i++) {
      paginationHtml += '<button class="page-btnviewss " style="background-color:#b163a6;"  data-page="' + i + '">' + i + '</button>';
  }
  $('#paginationincomes').html(paginationHtml);
  // Bind click event to each pagination button
  $('.page-btnviewss').click(function() {
      var pageNum = $(this).data('page');
      $('.page-btnviewss').removeClass('active');
      $(this).addClass('active');
      incomefetchdatas(data, pageSizeincome, pageNum);
  });
}