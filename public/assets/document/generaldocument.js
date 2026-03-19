$(document).ready(function () {
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
    $(".documentscls div").click(function () {
        var selectedText = $(this).text(); // Get selected city name
        var selectedValue = $(this).attr('data-value'); // Get data-value attribute

        // alert(selectedValue);

        $('#zone_id').val(selectedText); // Set input value (visible text)
        $('#zone_id').attr('data-selected-value', selectedValue); // Store data-value in a custom attribute
    });
    
 
    $('.dropdown-item-loc').on('click', function () {
        var selectedBranchName = $(this).text(); 
        var selectedBranchId = $(this).data('value');
        $('#zone_id').val(selectedBranchName); 
        $('#zone_id').attr('data-value', selectedBranchId); 
    });

      $('#submit-general_document').click(function (event) {
        // alert("test");
          event.preventDefault();
          let isValid = true;
          if ($('#zone_id').val() === "") {
              $('.error_doctor').text('Enter the Doctor Name');
              isValid = false;
          }
          if ($('#document_name').val() === "") {
              $('.error_employee').text('Please select the Employee Name');
              isValid = false;
          }
           if ($('#expire_date').val() === "") {
              $('.error_employee').text('Please select the Employee Name');
              isValid = false;
          }
           if ($('#pf_uploads').val() === "") {
              $('.error_employee').text('Please select the Employee Name');
              isValid = false;
          }
          if (!isValid) {
              return; 
          }
          // Create FormData object
          let formData = new FormData();
  
          formData.append('document_name', $('#document_name').val());
          formData.append('expire_date', $('#expire_date').val());
          // formData.append('zone_id', $('#zone_id').val());
          let zoneId = $('#zone_id').attr('data-value');
          formData.append('zone_id', zoneId);
          // Add images to FormData
          const files = $('#pf_uploads')[0].files;
          for (let i = 0; i < files.length; i++) {
              formData.append('images[]', files[i]);
          }
          // Include CSRF Token
          formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
          // AJAX Request
          $.ajax({
              url: general_documentaddedUrl,
              type: "POST",
              data: formData,
              processData: false, 
              contentType: false, 
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
                $("#exampleModaluser").modal('hide');
                documentoveralldata();
            },
              error: function (error) {
                  console.error(error.responseJSON);
              },
          });
      });

     $('#itemsPerPageSelectdocument').change(function() {
        var pageSizedocuments = parseInt($(this).val());
        renderPaginationdocument(dataSourcedocument, pageSizedocuments);
        renderTabledocument(dataSourcedocument, pageSizedocuments, 1);  // Initially show the first page
    });
    $(document).on('click', '.documentclk', function (e) {
        $('#exampleModal1').modal('show');
        var row = $(this).closest('tr');
        // Get data from the row
        var pdffiles = row.find('#pffiles').text();
        pdffiles = pdffiles.replace(/[\[\]\"]/g, ''); // Remove [ ] " and extra spaces
        var firstImage = pdffiles.split(',')[0].trim();
        var imageNamefirst = firstImage.split('/').pop();
        // $('#pfmain').attr('src', "../public/document_data/" + imageNamefirst);
        $('#pfmain').attr('src', "../document_data/" + imageNamefirst);
        var imageArray = pdffiles.split(',');
        var views = '';
        imageArray.forEach(function(image) {
            var imageName = image.trim().split('/').pop(); // Get the file name
            imageNames = imageName.replace(/\\/g, ''); // Remove [ ] " and extra spaces
            //alert(imageName);
                // Replace the src value of each image with a new one
                views+='<button style="font-size: 11px;" type="button" id="pdffetch" class="btn btn-primary">'+ imageNames +'</button>';
        });
         $('#button_pdfs').html("");
        $('#button_pdfs').html(views);
    });
    $(document).on("click" , '.upload_document', function (e) {
        $('#exampleModal2').modal('show');
        var row = $(this).closest('tr');
        var fetchid=row.find('#idfetch').data('id');
        var pdffilesviews = row.find('#pffiles').text();
        var expireda = row.find('#expiredatess').text();
        $("#docu_id").text(fetchid);
        $("#id_document").val(fetchid);
        $("#update_documents_all").val(pdffilesviews);
        $("#expire_dates").val(expireda);
    });
    $(document).on('click', '#pdffetch', function (e) {
        fetchvalue=$(this).text();
    //   $('#pfmain').attr('src', "../public/document_data/" + fetchvalue);
      $('#pfmain').attr('src', "../document_data/" + fetchvalue);
    });
    $("#submit-document_update").on("click",function(){
        var updated_id=$("#docu_id").text();
        let formData = new FormData();
        formData.append('id', $('#id_document').val());
        formData.append('expire_date', $('#expire_update_date').val());
        formData.append('document_type', $('#update_documents_all').val());
        formData.append('expire_dates', $('#expire_dates').val());
         // Add images to FormData
         const files = $('#pf_update')[0].files;
         for (let i = 0; i < files.length; i++) {
             formData.append('images[]', files[i]);
         }
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
                $("#exampleModaluser").modal('hide');
                documentoveralldata();
            },
               error: function (error) {
                   console.error(error.responseJSON);
               },
           });
    });
    documentoveralldata();
    document_branchwise();
});
var dataSourcedocument = [];
function documentoveralldata()
{
    $.ajax({
        url: fetchUrldocumentgeneral,
        type: "GET",
        success: function (responseData) {
            console.log(responseData);
            dataSourcedocument = responseData;
            totalItems = responseData.length; // Get total items count // Set the data fetched from the server
            var pageSizedocuments = parseInt($('#itemsPerPageSelectdocument').val()); // Get selected items per page
            renderPaginationdocument(dataSourcedocument, pageSizedocuments);
            renderTabledocument(dataSourcedocument, pageSizedocuments, 1); // Show first page initially
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
function document_branchwise()
{
    $.ajax({
        url: branchurl,
        type: "GET",
        success: function (responseData) {
            slct="";
            slctview="";
            slctzone="";
            console.log(responseData);
            $.each(responseData, function(index, user) {
                if (!slct.includes('<option value="">Select Branch</option>')) {
                    slct += '<option value="">Select Branch</option>';
                }
                slct += '<option data-id="' + user.id + '" value="' + user.id + '">' + user.Branch_name + '</option>';
                if (!slctzone.includes('<option value="">Select Branch</option>')) {
                    slctzone += '<option value="">Select Branch</option>';
                }
                slctzone += '<option data-id="' + user.zone,+ '" value="' + user.zone+ '">' + user.zone, + '</option>';
                slctview += '<label><input type="checkbox" value="' + user.Branch_name + '" onchange="updateSelectedValues()">'+ user.Branch_name +'</label>';
            });
            $("#zone_id").html(slct);
            $("#branchviews").html(slctview);
            $(".zoneid").html(slctzone);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
// Render table rows based on the page and page size
function renderTabledocument(data, pageSizedocuments, pageNum) {
    var startIdx = (pageNum - 1) * pageSizedocuments;
    var endIdx = pageNum * pageSizedocuments;
    var pageData = data.slice(startIdx, endIdx);
    //console.log(pageData);
    var body = "";
    var uniqueZones = [];
    $.each(pageData, function(index, user) {
        let dateStr = user.Created_at;
        let formattedDate = moment(dateStr).format("DD MMM YYYY HH:mm");
        var view = user.expire_date ;
        body += '<tr onclick="rowClick(event)">' +
                '<td class="tdview" id="idfetch" data-id="'+ user.id +'"> <strong>#' + user.id + '</strong><br>' + formattedDate + '<br></td>' +
                '<td class="tdview"  data-filesview="' + user.document_type + '">' + user.name + '<br>' + user.zone_name+ '<br></td>' +
               '<td style="display:none;" id="pffiles" class="tdview" >'+ user.document_type +'</td>' +
               '<td style="display:none;"id="expiredatess" class="tdview" >'+ user.expire_date +'</td>' +
               '<td class="tdview" >'+ user.document_name +'</td>' +
               '<td class="tdview documentclk"  ><img src="../assets/images/doc.png" style="width: 30px;"  alt="Icon" class="icon"></td>' +
                '<td class="tdview upload_document"  ><img src="../assets/images/policy.png" style="width: 35px;"  alt="Icon" class="icon"></td>' +
                '<td class="tdview" ><a href="#">'+ view +'</a></td>' +
                '<td class="tdview" ><img src="../assets/images/user/avatar-1.jpg" style="width: 20px;" alt="Icon"class="img-user rounded-circle" title="Admin"></td>' +
                //  '<td class="tdview"><img src="../assets/images/delete.png" style="width: 20px;" alt="Icon" class="" title="Delete"></td>'
               '</tr>';  });
    $("#document_tbl").html(body);
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

    $('#paginationdocument').html(paginationHtml);

    $('.page-btnviews').click(function () {
        const pageNum = $(this).data('page');
        renderPaginationdocument(data, pageSizedocuments, pageNum);
        renderTabledocument(data, pageSizedocuments, pageNum);
    });

    renderTabledocument(data, pageSizedocuments, currentPage);
}