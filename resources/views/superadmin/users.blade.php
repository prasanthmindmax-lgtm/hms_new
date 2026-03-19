<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <style>
table, th, td {
  padding: 5px;
}
table {
  border-spacing: 15px;
}
</style>
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
                <li class="breadcrumb-item"><a href="javascript: void(0)">Users</a></li>
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
                  <h3 class="mb-0">Users</h3>
                  <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo"><i class="ti ti-plus f-18"></i>New Users</button> -->
                </div>  
              </div>
             <div>&nbsp;</div>
              <div class="col-sm-12">
                <!-- <div class="card table-card"> -->
                  <div class="table-responsive">
                    <table class="table table-hover" id="ajax-crud-datatable">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Emp ID</th>
                          <th>Emp Name</th>
                          <th>Department</th>
                          <th>Designation</th>
                          <th>Location</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      
                    </table>
                  </div>
                  <div class="col-sm-12">
                    <div class="card-body pc-component btn-page">
                      <div
                        class="modal fade"
                        id="exampleModal"
                        tabindex="-1"
                        role="dialog"
                        aria-labelledby="exampleModalLabel"
                        aria-hidden="true"
                      >
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content ">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">New User</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <ul id="save_msgList"></ul>
                            <div class="modal-body">
                              <form method="post" id="formsubmit" >
                                <div class="row">
                                  <div class="col-lg-6" id="categorys" style="display:none;">
                                    <div class="mb-3">
                                      <label class="form-label">Category<span class="text-danger">*</span></label>
                                      <select class="mb-3 form-select" id="category1" name="category1" required>
                                        <option value="">Select Category</option>
                                          @if ($categories)
                                            @foreach ($categories as $category)
                                              <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                          @endif
                                      </select>
                                      
                                    </div>
                                    
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                  <button type="button" class="btn btn-primary add_user">Submit</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body pc-component btn-page">
                      <div
                        class="modal fade"
                        id="editExampleModal"
                        tabindex="-1"
                        role="dialog"
                        aria-labelledby="exampleModalLabel"
                        aria-hidden="true"
                      >
                      <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content ">
                            <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Admin Permission</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <ul id="save_msgList"></ul>
                            <div class="modal-body" id="editModel">
                              <form method="post" id="formsubmit" >
                                <div class="row">
                                  <div class="col-lg-3">
                                      <div class="mb-3">
                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin1" value="1" />
                                          <label class="form-check-label" for="checkbox-1"> is_admin? </label>
                                        </div>
                                      </div>
                                  </div>
                                  <div class="col-lg-8" id="categorys1" style="display:none;">
                                    <div class="ath_container">
                                      <div class="table-responsive">
                                        <table>
                                          @if($categories)
                                          <tbody id="tbody">
                                            @php $i = 1; $numCols = 3; @endphp
                                            <tr>
                                              @foreach ($categories as $category)
                                                <td>
                                                  <div class="input_outer" id="refresh">
                                                        <input type="checkbox" id="category{{ $category->id }}" name="status_id" value="{{ $category->id }}">
                                                        <label for="row_01">{{ $category->depart_name }} </label>
                                                  </div>
                                                </td>
                                                @php if ($i%($numCols)===0 && $i > 1) { @endphp
                                                  </tr>
                                                  <tr>
                                                    @php }  @endphp 
                                                  @php $i+=1; @endphp
                                              @endforeach                     
                                            </tr>
                                            </tbody>
                                          @endif
                                          
                                        </table>
                                      </div>
                                    </div>
                                  </div>
                                  
                                </div>
                                <input type="text" id="stud_id">
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                  <button type="button" class="btn btn-primary update_user">Submit</button>
                                </div>
                              </form>
                            </div>
                            
                          </div>
                        </div>
                      </div>
                    </div>
                   
                  </div>
              </div>
                    
                
                <!-- </div> -->
              </div>
        </div>
            </div>
          </div>
          <!-- Column Rendering table end -->
          <!-- Multiple Table Control Elements start -->
          <!-- Row Created Callback table end -->
        </div>
      </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')

    <script>
    $(document).ready(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
    getAjaxRecords();
    $('#base-style').DataTable();
    $(document).on('click', '.add_user', function (e) {
    e.preventDefault();
        $(this).text('Sending..');
        let is_admin = 0;
        let category = '';
        if ($('#is_admin').is(':checked')) {
           is_admin = 1;
           category = $('#category').val();
        }
        var data = {
            'name': $('#name').val(),
            'email': $('#email').val(),
            'mobile': $('#mobile').val(),
            'category': category,
            'is_admin': is_admin,
            'password': $('#password').val(),
        }
        

        $.ajax({
            type: "POST",
            url: "/users",
            data: data,
            dataType: "json",
            success: function (response) {
                // console.log(response);
                if (response.status == 400) {
                    $('#save_msgList').html("");
                    $('#save_msgList').addClass('alert alert-danger');
                    $.each(response.errors, function (key, err_value) {
                        $('#save_msgList').append('<li>' + err_value + '</li>');
                    });
                    $('.add_user').text('Save');
                } else {
                    $("#exampleModal").modal('hide');
                    $('#tbody').load(document.URL +  ' #tbody tr');
                    $("#itemAdded").click();
                }
            }
        });

    });

    $(document).on('click', '.editbtn', function (e) {
        // $('#tbody').load(document.URL +  ' #tbody tr');
        e.preventDefault();
        var id = $(this).val();
        //alert(id);
        $('#editExampleModal').modal('show');
        $.ajax({
            type: "GET",
            url: "/edit-users/" + id,
            
            success: function (response) {
              //alert(response.status);
                if (response.status == 404) {
                    $('#success_message').addClass('alert alert-success');
                    $('#success_message').text(response.message);
                    $('#editModal').modal('hide');
                } else {
                   $('#stud_id').val(id);
                    $('#name1').val(response.user.name);
                    $('#email1').val(response.user.email);
                    $('#mobile1').val(response.user.mobile);
                    if (response.user.is_admin == 1) {
                      $('#is_admin1').prop('checked', true);
                      $('#categorys1').show();
                      if (response.userPermission) {
                        let userPermissions = response.userPermission;
                        $.each(userPermissions, function(index, value) {
                          $('#category'+value.depart_id).prop('checked', true);
                        });
                      }
                    } else {
                        $('#tbody').load(document.URL +  ' #tbody tr');
                        $('#is_admin1').prop('checked', false);
                        $('#categorys1').hide();
                    }
                    
                }
            }
        });
        $('.btn-close').find('input').val('');
    });

    $(document).on('click', '.update_user', function (e) {
        e.preventDefault();

        $(this).text('Updating..');
        var id = $('#stud_id').val();
         alert(id);
        let is_admin = 0;
        let depart_id = '';
        if ($('#is_admin1').is(':checked')) {
           is_admin = 1;
           depart_id = $.map($('input[name="status_id"]:checked'), function(c){return c.value; });;
        }
        var data = {
            'depart_id': depart_id,
            'is_admin': is_admin,
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "PUT",
            url: "/update-user/" + id,
            data: data,
            dataType: "json",
            success: function (response) {
                // console.log(response);
                if (response.status == 400) {
                    $('#update_msgList').addClass('alert alert-danger');
                    $.each(response.errors, function (key, err_value) {
                        $('#update_msgList').append('<li>' + err_value +
                            '</li>');
                    });
                    $('.update_user').text('Submit');
                } else {
                    $('#tbody').load(document.URL +  ' #tbody tr');
                    $('#editExampleModal').modal('hide');
                    $('.update_user').text('Submit');
                   
                }
            }
        });

    });

    $(document).on('click', '.deletebtn', function () {
            var stud_id = $(this).val();
            $('#deleteExampleModal').modal('show');
            $('#deleteing_id').val(stud_id);
        });

    $(document).on('click', '.delete_student', function (e) {
        e.preventDefault();

        $(this).text('Deleting..');
        var id = $('#deleteing_id').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "DELETE",
            url: "/delete-user/" + id,
            dataType: "json",
            success: function (response) {
                // console.log(response);
                if (response.status == 404) {
                    $('#success_message').addClass('alert alert-success');
                    $('#success_message').text(response.message);
                    $('.delete_student').text('Yes Delete');
                } else {
                    $('#tbody').load(document.URL +  ' #tbody tr');
                    $('.delete_student').text('Yes Delete');
                    $('#deleteExampleModal').modal('hide');
                }
            }
        });
    });

    $('#is_admin').click(function() {
      $("#categorys").toggle(this.checked);
    });

    $('#is_admin1').click(function() {
      $("#categorys1").toggle(this.checked);
    });
  });

  function getAjaxRecords() 
  {
    $('#ajax-crud-datatable').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('superadmin.userList') }}",
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
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: true, searchable: false  },
            { data: 'employment_id', name: 'employment_id' },
            { data: 'fullname', name: 'fullname' },
            { data: 'deptname', name: 'deptname' },
            { data: 'designations', name: 'designations' },
            { data: 'name', name: 'name'},
            { data: 'action', 
              "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                  $(nTd).html("<button class='avtar avtar-xs btn-link-success btn-pc-default editbtn' value='"+oData.user_id+"'><i class='ti ti-edit-circle f-18'></i></button>");
              }
            },
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

   // Check all rows
   $('#check_all').click(function () {
            $('input').prop('checked', this.checked);
    });

    // Check all columns
    $('.check_column').click(function () {
        var index = $(this).closest('th').index();
        var isChecked = $(this).prop('checked');
        $('.check_row').each(function () {
            var checkboxes = $(this).closest('tr').find(':checkbox');
            checkboxes.eq(index).prop('checked', isChecked);
        });
    });


    $('.check_row').click(function () {
        var checkboxes = $(this).closest('tr').find(':checkbox');
        checkboxes.prop('checked', this.checked);
    });

</script>
  </body>
  <!-- [Body] end -->
</html>
