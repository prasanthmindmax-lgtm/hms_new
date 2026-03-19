$(document).ready(function () {

 // Fetch branches and populate dropdown
  branchviewsall();
  zoneviewsall();
  marketer_name_fetch();
});

// AJAX call to fetch branch data
function branchviewsall() {
  $.ajax({
    url: branchfetchviews,
    type: "GET",
    success: handleSuccessbranch,
    error: handleErrorbranch,
  });
}

// Handle successful AJAX response
function handleSuccessbranch(responseData) {
  let branchviewsed = "";
  let branchviewseadddoct="";
  $.each(responseData, function (index, user) {
    branchviewsed += `<div data-value="${user.id}">${user.branch_name}</div>`;

    branchviewseadddoct += `<option value="${user.id}">${user.branch_name}</option>`;
  });


  $(".brachviewsall").html(branchviewsed);
  $(".cityvalues").html(branchviewseadddoct);

}

// Handle AJAX error
function handleErrorbranch(xhr, status, error) {
  console.error("AJAX Error:", status, error);
}

function zoneviewsall()
{
 $.ajax({
      url: zonefetchviews,
      type: "GET",
      success: handleSuccesszone,
      error: handleErrorzone,
    });
}

function handleSuccesszone(responseData) {
  let zoneviewsed = "";
  $.each(responseData, function (index, user) {
    zoneviewsed += `<div data-value="${user.zone_name}">${user.zone_name}</div>`;
  });
  $(".zoneviewsall").html(zoneviewsed);
}

// Handle AJAX error
function handleErrorzone(xhr, status, error) {
  console.error("AJAX Error:", status, error);
}

function marketer_name_fetch()
{
  $.ajax({
    url: marketernamesurls,
    type: "GET",
    success: handleSuccessmarket,
    error: handleErrormarket,
  });
}


function handleSuccessmarket(responseData) {
  let marketerviewsed = "";
  $.each(responseData, function (index, user) {
    marketerviewsed += `<label><input type="checkbox" value="${user.user_fullname}" onchange="updateSelectedValues()">${user.user_fullname}-${user.username}</label>`;


  });

  $(".marketernameall").html(marketerviewsed);
}

// Handle AJAX error
function handleErrormarket(xhr, status, error) {
  console.error("AJAX Error:", status, error);
}
