<!doctype html>
<html lang="en">
<!-- [Head] start -->
@include('superadmin.superadminhead')
<style type="text/css">
.btn-hide {
    border: none;
    background: none;
}
</style>
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
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Sub Category Master</a></li>
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
                                  <h3 class="mb-0">Sub Category Master</h3>
                                  <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                      data-bs-target="#exampleModal" data-bs-whatever="@mdo"><i
                                          class="ti ti-plus f-18"></i>New Sub Category</button>
                              </div>
                          </div>
                          <div>&nbsp;</div>
                          <div class="table-card">
                            <div class="table-responsive" id="table1">
                              <table class="table table-bordered" id="ajax-crud-datatable">
                                <thead>
                                    <tr>
                                      <th class="text-center">#</th>
                                      <th class="text-center">SubCategory</th>
                                      <th class="text-center">Category</th>
                                      <th class="text-center">Status</th>
                                      <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                              </table>
                            </div>
                          </div>
                        </div>
                    </div>
                    <button id="itemAdded" class="btn btn-success btn-hide"
                        style="background-color:#0000;border-color:#fffff;"></button>

              </div>
              <div class="row">
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
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">New Sub Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <ul id="save_msgList"></ul>
                              <div class="modal-body">
                                <form method="post" id="formsubmit" >
                                    <div class="mb-3">
                                        <label for="recipient-name" class="col-form-label pt-0">Category Name</label>
                                        <select class="form-control" name="category_name" id="category_id">
                                            <option value="">Select Category</option>
                                            @php $categories = App\Models\CategoryModel::get();    @endphp
                                            @if($categories)
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->depart_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                  <div class="mb-3">
                                        <label for="recipient-name" class="col-form-label pt-0">Sub Category Name</label>
                                        <input type="text" class="form-control" id="sub_category_names" placeholder="Category Name" />
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary add_sub_category">Submit</button>
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
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Edit Sub Category</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <ul id="save_msgList"></ul>
                              <div class="modal-body">
                                <form method="post" id="formsubmit" >
                                    <div class="mb-3">
                                        <label for="recipient-name" class="col-form-label pt-0">Category Name</label>
                                        <select class="form-control" name="category_name" id="category_id1">
                                            @php $categories = App\Models\CategoryModel::get();    @endphp
                                            {{ $categories }} 
                                            @if($categories)
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->depart_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                  <div class="mb-3">
                                        <label for="recipient-name" class="col-form-label pt-0">Sub Category Name</label>
                                        <input type="text" class="form-control" id="sub_category_name1" placeholder="Category Name" />
                                  </div>
                                  <div class="mb-3">
                                    <div class="form-check mb-2">
                                      <input class="form-check-input" type="radio" name="group4" value="1" id="flexCheckDefault" />
                                      <label class="form-check-label" for="flexCheckDefault"> Active </label>
                                    </div>
                                    <div class="form-check mb-2">
                                      <input class="form-check-input" type="radio" name="group4" value="0" id="flexCheckDefault1" />
                                      <label class="form-check-label" for="flexCheckChecked"> In-Active </label>
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary update_subcategory">Submit</button>
                                    <input type="hidden" id="stud_id">
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
                          id="deleteExampleModal"
                          tabindex="-1"
                          role="dialog"
                          aria-labelledby="exampleModalLabel"
                          aria-hidden="true"
                        >
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Delete Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h4>Confirm to Delete Data ?</h4>
                                <input type="hidden" id="deleteing_id">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary delete_student">Yes Delete</button>
                            </div>
                        </div>
                    </div>
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
    $(document).ready(function() {
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      getAjaxRecords();
      $(document).on('click', '.add_sub_category', function(e) {
          e.preventDefault();
          $(this).text('Sending..');
          var data = {
              'sub_category_name': $('#sub_category_names').val(),
              'category_id': $('#category_id').val(),
          }
          $.ajax({
              type: "POST",
              url: "/superAdminSubCategory",
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
                      $('.add_sub_category').text('Submit');
                  } else {
                      $("#exampleModal").modal('hide');
                      $("#itemAdded").click();
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
                url: "/edit-subcategory/" + id,
                success: function(response) {
                    if (response.status == 404) {
                        $('#success_message').addClass('alert alert-success');
                        $('#success_message').text(response.message);
                        $('#editModal').modal('hide');
                    } else {
                      $('#category_id1').val(response.priority.category_id);
                      $('#sub_category_name1').val(response.priority.sub_category_name);
                      if (response.priority.sub_category_status == 1) {
                          $('#flexCheckDefault').prop('checked', 'true');
                      } else if (response.priority.sub_category_status == 0) {
                          $('#flexCheckDefault1').prop('checked', 'true');
                      }
                      $('#stud_id').val(id);
                    }
                }
            });
            $('.btn-close').find('input').val('');
        });

        $(document).on('click', '.update_subcategory', function(e) {
            e.preventDefault();

            $(this).text('Updating..');
            var id = $('#stud_id').val();
            var status1 = '';
            // alert(id);
            if ($("#flexCheckDefault").prop("checked")) {
                status1 = 1;
            } else if ($("#flexCheckDefault1").prop("checked")) {
                status1 = 0;
            }

            var data = {
                'category_id': $('#category_id1').val(),
                'sub_category_name': $('#sub_category_name1').val(),
                'status': status1
            }

            $.ajax({
                type: "PUT",
                url: "/update-subcategory/" + id,
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
                        $('.update_subcategory').text('Submit');
                    } else {
                        $('#editExampleModal').modal('hide');
                        $('.update_subcategory').text('Submit');
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
                url: "/delete-subcategory/" + id,
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
                        $('#tbody').load(document.URL + ' #tbody tr');
                        $("#itemDeleted").click();
                    }
                }
            });
        });

        function getAjaxRecords() 
        { 
            $('#ajax-crud-datatable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('superadmin.subCategoryList') }}",
                    type: "POST",
                    data: function (data1) {
                        data1.extra_search = 3;             
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: true, searchable: false  },
                    { data: 'sub_category_name', name: 'sub_category_name' },
                    { data: 'category_name', name: 'category_name' },
                    { data: 'sub_category_status', 
                        "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                          if (oData.sub_category_status == 1) {
                            $(nTd).html("<label class='adge bg-light-success'><i class='fas fa-circle f-10 m-r-10'></i>Active<label>");
                          } else if (oData.sub_category_status == 0) {
                            $(nTd).html("<label class='adge bg-light-secondary'><i class='fas fa-circle f-10 m-r-10'></i>In-Active<label>");
                          }
                          
                        }
                     },
                    { data: 'action', 
                      "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                          $(nTd).html("<button class='avtar avtar-xs btn-link-success btn-pc-default editbtn' value='"+oData.id+"'><i class='ti ti-edit-circle f-18'></i></button>");
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
                order: [[0, 'asc']]
            });
        }

    });
    </script>
</body>
<!-- [Body] end -->

</html>