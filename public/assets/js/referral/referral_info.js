$(document).ready(function(){

    $("#meeting_info_details").hide();
    $("#patients_info_details").hide();

    $("#meeting_info").on("click",function(){

        $("#meeting_info").addClass("active");
        $("#doctor_info").removeClass("active");
        $("#patients_info").removeClass("active");
        $("#doctor_info_details").hide();
        $("#patients_info_details").hide();
        $("#meeting_info_details").show();

    });

    $("#doctor_info").on("click",function(){

        $("#doctor_info").addClass("active");
        $("#meeting_info").removeClass("active");
        $("#patients_info").removeClass("active");
        $("#meeting_info_details").hide();
        $("#patients_info_details").hide();
        $("#doctor_info_details").show();
 });

    $("#patients_info").on("click",function(){

        $("#patients_info").addClass("active");
        $("#meeting_info").removeClass("active");
        $("#doctor_info").removeClass("active");
        $("#patients_info_details").show();
        $("#doctor_info_details").hide();
        $("#meeting_info_details").hide();

    });

    $(document).on("click", ".imagecheck", function() {

    var referral_id=$("#doctor_ids").text();

    patient_details_popup(referral_id);
    meeting_details_popup(referral_id);

    })

});

function patient_details_popup(referral_id)
{
    $.ajax({
        url: patientpop,
        type: "GET",
        data: {
            referral_id: referral_id,
    
        },
        success: function (responseData) {
            //console.log(responseData);
            dataSourcenew = responseData; 
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            renderTablepop(dataSourcenew,totalItems); // Show first page initially
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}

function meeting_details_popup(referral_id)
{
    $.ajax({
        url: meetingpop,
        type: "GET",
        data: {
            referral_id: referral_id,
    
        },
        success: function (responseData) {
            //console.log(responseData);
            dataSourcenew = responseData; 
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            renderTablepopmeet(dataSourcenew,totalItems)

        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}


// Render table rows based on the page and page size
function renderTablepop(data,totalItems) {

    var bodynew = "";
   
    $.each(data, function(index, user) {

        bodynew += '<tr >' +
                '<td style="padding: 5px;">'+user.wifename+'</td>' +
                '<td style="padding: 5px;">'+user.mrn_number+'</td>' +
                '<td style="padding: 5px;">'+user.husband_name+'</td>' +
                '<td style="padding: 5px;">'+user.mrn_number+'</td>' +
               '</tr>';  });

    $("#patient_popdata").html(bodynew);
    $("#total_patient").text("Total Patient Count : " + totalItems);

    
}


function renderTablepopmeet(data,totalItems) {

    var bodynew = "";
   
    $.each(data, function(index, user) {

        let dateStr = user.created_at;
        let formattedDate = moment(dateStr).format("DD MMM YYYY | HH:MM");

        bodynew += '<tr >' +
                '<td style="padding: 5px;">'+formattedDate+'</td>' +
                '<td style="padding: 5px;">'+user.meeting_feedback+'</td>' +
       
               '</tr>';  });

    $("#meeting_popdata").html(bodynew);
    $("#totalmeetins").text("Total Meeting Count : " + totalItems);

    
}