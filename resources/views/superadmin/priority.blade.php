<!doctype html>
<html lang="en">
<!-- [Head] start -->
@include('superadmin.superadminhead')
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
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Ticket Priority Master</a>
                                </li>
                            </ul>
                        </div>
                        <!-- <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Ticket Priority Master</h2>
              </div>
            </div> -->
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <!-- [ Main Content ] end -->
            <div class="row">

                <div class="col-xl-12 col-md-12">
                    <div class="card">
                        <div class="card-body border-bottom pb-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="mb-0">Ticket Priority Master</h3>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal" data-bs-whatever="@mdo"><i
                                        class="ti ti-plus f-18"></i>New Priority</button>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-hover" id="ajax-crud-datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Color</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Column Rendering table end -->
                <!-- Multiple Table Control Elements start -->
                <!-- Row Created Callback table end -->
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card-body pc-component btn-page">
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">New Priority</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <ul id="save_msgList"></ul>
                                    <div class="modal-body">
                                        <form method="post" id="formsubmit">
                                            <div class="mb-3">
                                                <label for="recipient-name" class="col-form-label pt-0">Priority
                                                    Name</label>
                                                <input type="text" class="form-control" id="priority_name"
                                                    placeholder="Priority Name" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleColorInput" class="form-label">Color
                                                    picker</label>
                                                <input type="color" class="form-control form-control-color" id="colorId"
                                                    title="Choose your color" />
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="button"
                                                    class="btn btn-primary add_priority">Submit</button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pc-component btn-page">
                        <div class="modal fade" id="editExampleModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Edit Priority
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <ul id="save_msgList"></ul>
                                    <div class="modal-body">
                                        <form method="post" id="formsubmit">
                                            <div class="mb-3">
                                                <label for="recipient-name" class="col-form-label pt-0">Priority
                                                    Name</label>
                                                <input type="text" class="form-control" id="priority_name1"
                                                    placeholder="Priority Name" />
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleColorInput" class="form-label">Color
                                                    picker</label>
                                                <input type="color" class="form-control form-control-color"
                                                    id="colorId1" title="Choose your color" />
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="button"
                                                    class="btn btn-primary update_priority">Submit</button>
                                            </div>
                                            <input type="hidden" id="stud_id">
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pc-component btn-page">
                        <div class="modal fade" id="deleteExampleModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Delete Item</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h4>Confirm to Delete Data ?</h4>
                                        <input type="hidden" id="deleteing_id">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary delete_student">Yes
                                            Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button id="priotiyCreate" class="btn btn-light-warning" style="display:none;">Try me!</button>
                    <button id="priorityUpdate" class="btn btn-light-primary" style="display:none;">Try me!</button>
                
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')

    <script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        getAjaxRecords();
       
        $(document).on('click', '.add_priority', function(e) {
            e.preventDefault();
            $(this).text('Sending..');
            var data = {
                'priority_name': $('#priority_name').val(),
                'priority_color': $('#colorId').val()
            }

            $.ajax({
                type: "POST",
                url: "/tasks/priority",
                data: data,
                dataType: "json",
                success: function(response) {
                    // console.log(response);
                    if (response.status == 400) {
                        $('#save_msgList').html("");
                        $('#save_msgList').addClass('alert alert-danger');
                        $.each(response.errors, function(key, err_value) {
                            $('#save_msgList').append('<li>' + err_value + '</li>');
                        });
                        $('.add_priority').text('Save');
                    } else {
                        $("#exampleModal").modal('hide');
                        $('#priotiyCreate').click();
                        location.reload();
                    }
                }
            });

        });

        $(document).on('click', '.editbtn', function(e) {
            e.preventDefault();
            var id = $(this).val();
            $('#editExampleModal').modal('show');
            $.ajax({
                type: "GET",
                url: "/tasks/edit-priority/" + id,
                success: function(response) {
                    if (response.status == 404) {
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('#editModal').modal('hide');
                    } else {
                        $('#priority_name1').val(response.priority.priority_name);
                        $('#colorId1').val(response.priority.priority_color);

                        $('#tbody').load(document.URL + ' #tbody tr');
                        $('#stud_id').val(id);
                    }
                }
            });
            $('.btn-close').find('input').val('');
        });

        $(document).on('click', '.update_priority', function(e) {
            e.preventDefault();

            $(this).text('Updating..');
            var id = $('#stud_id').val();
            // alert(id);

            var data = {
                'priority_name': $('#priority_name1').val(),
                'priority_color': $('#colorId1').val(),
            }

            $.ajax({
                type: "PUT",
                url: "/tasks/update-priority/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    // console.log(response);
                    if (response.status == 400) {
                        $('#update_msgList').addClass('alert alert-danger');
                        $.each(response.errors, function(key, err_value) {
                            $('#update_msgList').append('<li>' + err_value +
                                '</li>');
                        });
                        $('.update_priority').text('Submit');
                    } else {
                        $('#editExampleModal').modal('hide');
                        $('.update_priority').text('Submit');
                        $('#priorityUpdate').click();
                        location.reload();
                    }
                }
            });

        });

        $(document).on('click', '.deletebtn', function() {
            var stud_id = $(this).val();
            $('#deleteExampleModal').modal('show');
            $('#deleteing_id').val(stud_id);
        });

        $(document).on('click', '.delete_student', function(e) {
            e.preventDefault();

            $(this).text('Deleting..');
            var id = $('#deleteing_id').val();

            $.ajax({
                type: "DELETE",
                url: "/tasks/delete-priority/" + id,
                dataType: "json",
                success: function(response) {
                    // console.log(response);
                    if (response.status == 404) {
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('.delete_student').text('Yes Delete');
                    } else {
                        $('.delete_student').text('Yes Delete');
                        $('#deleteExampleModal').modal('hide');
                        $("#itemDeleted").click();
                    }
                }
            });
        });

        function getAjaxRecords() {
            $('#ajax-crud-datatable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('superadmin.priorityList') }}",
                    type: "POST",
                    data: function(data1) {
                        data1.extra_search = 3;
                    }
                },
                columns: [
                    {  data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: true, searchable: false  },
                    {   data: 'priority_name', name: 'priority_name'  },
                    {
                        data: 'priority_color',
                        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                                $(nTd).html("<input type='color' value='"+oData.priority_color+"'>");
                        }
                    },
                    {
                        data: 'action',
                        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).html(
                                "<button class='avtar avtar-xs btn-link-success btn-pc-default editbtn' value='" +
                                oData.id +
                                "'><i class='ti ti-edit-circle f-18'></i></button>");
                        }
                    },
                ],
                createdRow: function(row, data, dataIndex, cells) {
                    if (data.stylesheet) {
                        $.each(data.stylesheet, function(k, rowStyle) {
                            $(cells[rowStyle.col]).css(rowStyle.style);
                        });
                    }
                },
                order: [
                    [0, 'asc']
                ]
            });
        }


    });
    </script>
    <script type="text/javascript">
    window.addEventListener('swal:toast', event => {
      // default settings for toasts
      const Toast = Swal.mixin({   
          toast: true,
          position: 'top-end',
          background: 'white',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
              toast.addEventListener('mouseenter', Swal.stopTimer)
              toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
      });
      // convert some attributes
      let config = Array.isArray(event.detail) ? event.detail[0] : event.detail;
      config = convertAttributes(config);
      // override default settings or add new settings
      Toast.fire(config);
    });
    function convertAttributes(attributes) {          
      // convert predefined 'words' to a real color
      switch (attributes.background) {
          case 'danger':
          case 'error':
              attributes.background = 'rgb(254, 226, 226)';
              break;
          case 'warning':
              attributes.background = 'rgb(255, 237, 213)';
              break;
          case 'primary':
          case 'info':
              attributes.background = 'rgb(207, 250, 254)';
              break;
          case 'success':
              attributes.background = 'rgb(220, 252, 231)';
              break;
      }
      // if the attribute 'text' is set, convert it to the attribute 'html'
      if (attributes.text) {      
          attributes.html = attributes.text;
          delete attributes.text;
      }
      return attributes;
    }
    document.getElementById('priotiyCreate').addEventListener('click', () => {
      window.dispatchEvent(new CustomEvent('swal:toast', {
          detail: {
            title:'Info!',
            text: 'Priority created successfully',
            icon: 'success',
            background: 'success',
          }
      }));
    });
    document.getElementById('priorityUpdate').addEventListener('click', () => {
      window.dispatchEvent(new CustomEvent('swal:toast', {
          detail: {
            title:'Info!',
            text: 'Priority updated successfully',
            icon: 'primary',
            background: 'primary',
          }
      }));
    });
  </script>
</body>
<!-- [Body] end -->

</html>