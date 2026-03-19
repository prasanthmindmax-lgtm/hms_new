<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />


<body style="overflow-x: hidden;">

    <div class="page-loader">
      <div class="bar"></div>
    </div>
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')
    <!-- [ Header ] end -->
    <div class="pc-container">
        <div class="pc-content">

            <div class="container-box">
                <div class="header-bar">
                    <h5>Delivery Address<span class="dropdown-toggle"></span></h5>
                    <div>
                        <a class="btn btn-primary btn-sm address_new">+ New</a>
                        <span class="ellipsis">⋮</span>
                    </div>
                </div>
                <div style="overflow-x: auto;">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Address</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($address as $v)
                                <tr class="customer-row"
                                    data-id="{{ $v->id }}"
                                    data-address="{{ $v->address }}"
                                    data-created-by="{{ $v->created_by }}"
                                >
                                    <td>{{ $v->address }}</td>
                                    <td>{{ $v->user->user_fullname }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $v->id }}">Edit</button>
                                        <!-- <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $v->id }}">Delete</button> -->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- ✅ Pagination Controls -->
                @if($address->total() > 10)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        {{ $address->links('pagination::bootstrap-4') }}
                    </div>
                    <div>
                        <form method="GET" id="perPageForm" class="d-flex align-items-center">
                            <select name="per_page" id="per_page" class="form-control form-control-sm" style="width: 70px;">
                                @foreach([10, 25, 50, 100, 250, 500] as $size)
                                    <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                            <span class="ms-2">entries</span>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            <!-- New/Edit Address Modal -->
            <div id="newdeliveryModal" class="tds-modal" style="display: none;">
                <div class="tds-modal-content" style="max-width: 600px;">
                    <div class="tds-modal-header">
                        <h4 id="modalTitle">New Delivery Address</h4>
                        <span class="close-new-modal-delivery" style="font-size: 2rem; cursor:pointer">&times;</span>
                    </div>

                    <div class="tds-modal-body">
                        <form id="addressForm">
                            @csrf
                            <input type="hidden" id="address_id" name="id" value="">
                            
                            <div style="display: flex; gap: 10px;">
                                <div style="flex: 1;">
                                    <label>Address <span class="text-danger">*</span></label>
                                    <span class="delivery_address_modal_error text-danger" style="color: red"></span>
                                    <textarea name="address" id="delivery_address_modal" class="form-control" autocomplete="off" autocorrect="off" cols="10" rows="3" required></textarea>
                                </div>
                            </div>
                            <br />
                            <button class="btn-save delivery_save" type="submit">Save</button>
                            <button class="btn-cancel close-new-modal-delivery" type="button">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>

    

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @if ($errors->any())
    <script>
        $(document).ready(function () {
            $('#exampleModal').modal('show');
        });
    </script>
    @endif

    <script>
        $(document).ready(function () {
            // Set CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Per page change handler
            $('#per_page').on('change', function () {
                $('#perPageForm').submit();
            });

            // Open new address modal
            $(document).on('click', '.address_new', function () {
                $('#modalTitle').text('New Delivery Address');
                $('#address_id').val('');
                $('#delivery_address_modal').val('');
                $('.delivery_address_modal_error').html('');
                $('#newdeliveryModal').fadeIn();
                $('body').addClass('no-scroll');
            });

            // Edit button click handler
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                let tr = $(this).closest('tr');
                let address = tr.data('address');
                
                $('#modalTitle').text('Edit Delivery Address');
                $('#address_id').val(id);
                $('#delivery_address_modal').val(address);
                $('.delivery_address_modal_error').html('');
                $('#newdeliveryModal').fadeIn();
                $('body').addClass('no-scroll');
            });

            // Save address (Create/Update)
            $(document).on('click', '.delivery_save', function (e) {
                e.preventDefault();
                
                let id = $('#address_id').val();
                let address = $('#delivery_address_modal').val().trim();
                
                // Clear previous errors
                $('.delivery_address_modal_error').html('');
                
                // Validate
                if (!address) {
                    $('.delivery_address_modal_error').html('Address field is required');
                    return;
                }
                
                let url = '{{ route("superadmin.getdeliverysave") }}';
                let formData = {
                    id: id,
                    address: address,
                    _token: '{{ csrf_token() }}'
                };
                
                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#newdeliveryModal').fadeOut();
                            $('body').removeClass('no-scroll');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    },
                    error: function (error) {
                        if (error.responseJSON && error.responseJSON.errors) {
                            $.each(error.responseJSON.errors, function(key, value) {
                                $('.delivery_address_' + key + '_error').html(value[0]);
                            });
                        } else if (error.responseJSON && error.responseJSON.message) {
                            toastr.error(error.responseJSON.message);
                        } else {
                            toastr.error('An error occurred. Please try again.');
                            console.error(error);
                        }
                    }
                });
            });

            // Close modal handlers
            $(document).on('click', '.close-new-modal-delivery', function () {
                $('#newdeliveryModal').fadeOut();
                $('body').removeClass('no-scroll');
            });

            $(document).on('click', '.close-delete-modal', function () {
                $('#deleteModal').fadeOut();
                $('body').removeClass('no-scroll');
            });

            $(window).on('click', function (e) {
                if ($(e.target).is('#newdeliveryModal')) {
                    $('#newdeliveryModal').fadeOut();
                    $('body').removeClass('no-scroll');
                }
                if ($(e.target).is('#deleteModal')) {
                    $('#deleteModal').fadeOut();
                    $('body').removeClass('no-scroll');
                }
            });

            // Initialize flatpickr if needed
            flatpickr('.datepicker', {
                dateFormat: 'd/m/Y',
                allowInput: true
            });
        });
    </script>

    @include('superadmin.superadminfooter')
</body>
</html>