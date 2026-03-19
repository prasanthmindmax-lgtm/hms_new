<!doctype html>
<html lang="en">
<!-- [Head] start -->
@include('superadmin.superadminhead')
<style type="text/css">
details {
    border: 1px solid #767676;
    border-radius: 3px;
    display: inline-flex;
    flex-direction: column;
    padding: 3px 6px;
}

details summary::marker {
    display: none;
    font-size: 0;
}

details summary::-webkit-details-marker {
    display: none;
    font-size: 0;
}

details summary::after {
    content: "\25BC"/ "";
    display: inline-block;
    font-size: 0.6rem;
    height: 1rem;
    line-height: 1rem;
    margin-left: 0.5rem;
    position: relative;
    transition: transform 0.25s;
}

details[open] summary {
    margin-bottom: 1rem;
}

details[open] summary::after {
    top: -0.15rem;
    transform: rotate(180deg);
}

form {
    display: flex;
}

fieldset {
    border: 0;
    padding: 0;
    width: 100%;
}

fieldset legend {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0, 0, 0, 0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

ul li {
    border-radius: 3px;
    margin: 0;
    padding: 4px 2px;
}

ul li:hover {
    background: #eee;
}

ul li label {
    display: flex;
    flex-grow: 1;
    justify-content: space-between;
}
</style>
<!-- <link href="../assets/css/plugins/animate.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="../assets/css/uikit.css" /> -->
<!-- [Head] end -->
<!-- [Body] Start -->

<body @@bodySetup>
    <div class="page-loader">
        <div class="bar"></div>
    </div>
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')

    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">All Tickets</a></li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h4 class="mb-0">All Tickets</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- DOM/Jquery table start -->
                <div class="col-xl-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label class="form-label">Ticket Status</label>
                                        <details class="form-control">
                                            <summary>Select Ticket Status</summary>
                                            <ul>
                                                @if($statuses)
                                                @foreach ($statuses as $status)
                                                <li>
                                                    <label for="bmw">{{ $status->status_name}}<input type="checkbox"
                                                            id="statusValue[]" name="status_id"
                                                            value="{{ $status->id }}"></label>
                                                </li>
                                                @endforeach
                                                @endif
                                            </ul>
                                        </details>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label class="form-label">Ticket Priority</label>
                                        <details class="form-control">
                                            <summary>Select Ticket Status</summary>
                                            <ul>
                                                @if($priorities)
                                                @foreach ($priorities as $priority)
                                                <li>
                                                    <label for="bmw">{{ $priority->priority_name}}<input type="checkbox"
                                                            id="priority[]" name="priority_id"
                                                            value="{{ $priority->id }}"></label>
                                                </li>
                                                @endforeach
                                                @endif
                                            </ul>
                                        </details>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label class="form-label">Location</label>
                                        <select class="form-control" id="location_id">
                                            <option value="">Select Location</option>
                                            @php $locations = App\Models\LocationModel::get(); @endphp
                                            @if($locations)
                                            @foreach ($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label class="form-label">Select Date</label>
                                        <select class="form-control" id="dateType">
                                            <option value="">Select Date Type</option>
                                            <option value="1">Created</option>
                                            <option value="2">Updated</option>
                                            <option value="3">Target</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3" id="start_date">
                                    <div class="mb-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="startDate" />

                                    </div>
                                </div>
                                <div class="col-lg-3" id="end_date">
                                    <div class="mb-3">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="endDate" />
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <p><br /></p>
                                        <button class="btn btn-primary" id="search">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive dt-responsive">
                        <table class="table table-bordered" id="ajax-crud-datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Ticket No</th>
                                        <th>Location</th>
                                        <th>Category</th>
                                        <th>SubCategory</th>
                                        <th>EmpName</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Target</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card-body pc-component btn-page">
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document" id="empDetail">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Employee</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <ul id="save_msgList"></ul>
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Personal Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0 pt-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Emp ID</p>
                                                            <p class="mb-0" id="empId"></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Name</p>
                                                            <p class="mb-0" id="username"></p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item px-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Mobile</p>
                                                            <p class="mb-0" id="mobile"></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Department</p>
                                                            <p class="mb-0" id="department">New York</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item px-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Designation</p>
                                                            <p class="mb-0" id="designation">anshan.dh81@gmail.com</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Location</p>
                                                            <p class="mb-0" id="location"></p>
                                                        </div>
                                                    </div>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')

    <script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function() {
        getAjaxRecords();
        $("#empDetail").load(location.href + " #empDetail");
        $('#start_date').hide();
        $('#end_date').hide();

        $(document).on('change', '#dateType', function() {
            let date = $(this).val();
            if (date != '') {
                $('#start_date').show();
                $('#end_date').show();
            } else {
                $('#start_date').hide();
                $('#end_date').hide();
            }
        });

        $(document).on('click', '.updateTicketStatus', function() {
            let ticket_status = $('#statusId').val();
            let ticketId = $('#ticketId').val();
            $.ajax({
                method: 'post',
                url: "/tasks/updateTicketStatus",
                data: {
                    ticket_status: ticket_status,
                    ticketId: ticketId
                },
                success: function(response) {
                    if (response.status == 400) {
                        $('#save_msgList').html("Ticket no not found");
                        $('#save_msgList').addClass('alert alert-danger');
                        $.each(response.errors, function(key, err_value) {
                            $('#save_msgList').append('<li>' + err_value + '</li>');
                        });
                        $('.updateTicketStatus').text('Save');
                    } else {
                        $("#exampleModal").modal('hide');
                        $('#tbody').load(document.URL + ' #tbody tr');
                        //$("#itemAdded").click();
                    }
                }
            })
        });

        $("#dom-jqry").on('click', '.btnSelect', function() {
            // get the current row

            var currentRow = $(this).closest("tr");
            var col1 = currentRow.find("td:eq(0)").text(); // get current row 1st TD value
            $.ajax({
                method: 'post',
                url: "/tasks/getUserValue",
                data: {
                    id: col1
                },
                success: function(response) {
                    if (response.status == '200') {
                        $('#username').text(response.user.fullname);
                        $('#empId').text(response.user.employment_id);
                        $('#mobile').text(response.user.mobile);
                        $('#department').text(response.depart);
                        $('#designation').text(response.designation);
                        $('#location').text(response.location_id);
                    }
                }
            })
            $('#ticketId').val(col1);
            $('#exampleModal').modal('show');
        });


        $(document).on('click', '#category_id', function() {
            let category = $.map($('input[name="category_id"]:checked'), function(c) {
                return c.value;
            });

            $.ajax({
                method: 'post',
                url: "/tasks/getsubcategory",
                data: {
                    category: category
                },
                success: function(res) {
                    if (res.status == '200') {
                        let all_options = "<option value=''>Select Sub Category</option>";
                        let all_subcategories = res.subcategories;
                        $.each(all_subcategories, function(index, value) {
                            all_options += "<option value='" + value.id +
                                "'>" + value.sub_category_name + "</option>";
                        });
                        $("#subcategory_id").html(all_options);
                    } else if (res.status == '401') {
                        let all_options = "";
                        $("#subcategory_id").html(all_options);
                    }
                }
            });
        });

        $(document).on('change', '#update_status', function() {
            let category = $(this).val();
            $.ajax({
                method: 'post',
                url: "/tasks/updateStatus",
                data: {
                    category: category
                },
                success: function(res) {
                    if (res.status == '200') {
                        let all_options = "<option value=''>Select Sub Category</option>";
                        let all_subcategories = res.subcategories;
                        $.each(all_subcategories, function(index, value) {
                            all_options += "<option value='" + value.id +
                                "'>" + value.sub_category_name + "</option>";
                        });
                        $("#subcategory_id").html(all_options);

                    }
                }
            })
        });

        $(document).on('click', '#search', function() {
            getAjaxRecords();
        });

        $("#ajax-crud-datatable").on('click', '.btnSelect', function() {
        // get the current row
        var currentRow=$(this).closest("tr"); 
        var col1=currentRow.find("td:eq(0)").text(); // get current row 1st TD value
          $.ajax({
              method: 'post',
              url: "/tasks/getSuperAdminUserValue",
              data: {
                  id: col1
              },
              success: function(response) {
                  if (response.status == '200') {
                    $('#username').text(response.user.fullname);
                    $('#empId').text(response.user.employment_id);
                    $('#mobile').text(response.user.mobile);
                    $('#department').text(response.depart);
                    $('#designation').text(response.designation);
                    $('#location').text(response.location);
                  } 
              }
        })
        $('#ticketId').val(col1);
        $('#exampleModal').modal('show');  
      });

        function getAjaxRecords() 
        { 
            $('#ajax-crud-datatable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('superadmin.getSuperAdminTickets') }}",
                    type: "POST",
                    data: function (data1) {
                        data1.extra_search = 3;
                        data1.locations = $('#location_id').val();
                        data1.priorityId = $.map($('input[name="priority_id"]:checked'), function(c) {
                                            return c.value; });
                        data1.statusId = $.map($('input[name="status_id"]:checked'), function(c) {
                                            return c.value; });
                        data1.categoryId = $.map($('input[name="category_id"]:checked'), function(c) {
                                            return c.value; });
                        data1.subcategoryId = $('#subcategory_id').val();
                        data1.dateType = $('#dateType').val();
                        if (data1.dateType != '') {
                            data1.startDate = $('#startDate').val();
                            data1.endDate = $('#endDate').val();
                            if (data1.startDate != '') {
                                if (data1.endDate == '') {
                                    alert('End date should not be empty');
                                    return false;
                                }
                            }
                            if (data1.endDate <= data1.startDate) {
                                alert('End date should not be smaller than start date');
                                return false;
                            }
                        }               
                    }
                },
                columns: [
                    { data: 'ticket_no', 
                        "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a href='https://draravinds.com/tasks/superadmin/ticketDetail/"+oData.ticket_no+"'>"+oData.ticket_no+"</a>");
                        }
                    },
                    { data: 'location', name: 'location' },
                    { data: 'department', name: 'department' },
                    { data: 'subcategory', name: 'subcategory' },
                    { data: 'fullname', 
                        "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<span id='changeStatus' class='btnSelect' style='color:blue'>"+oData.fullname+"</span>");
                        }
                    },
                    { data: 'status_name', 
                        "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<span class='badge' style='background-color:"+oData.status_color+"'>"+oData.status_name+"</span>");
                        }
                    },
                    { data: 'priority_name', 
                        "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<label class='badge' style='background:#eaf6f2; color:"+oData.priority_color+"'>"+oData.priority_name+"</label>");
                        }
                    },
                    { data: 'created', name: 'created'},
                    { data: 'updated', name: 'updated', orderable: false},
                    { data: 'targetDate', name: 'targetDate', orderable: false},
                ],
                createdRow: function (row, data, dataIndex, cells) {
                        if (data.stylesheet) {
                            $.each(data.stylesheet, function (k, rowStyle) {
                                $(cells[rowStyle.col]).css(rowStyle.style);
                            });
                        }
                    },
                order: [[0, 'desc']]
            });
        }
    });
    </script>
    <script src="//cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js" type="text/javascript"></script>
    <script src="{{ asset('/assets/js/plugins/sweetalert2.all.min.js') }} "></script>
    <script src="{{ asset('/assets/js/pages/ac-alert.js') }}"></script>
</body>
<!-- [Body] end -->

</html>