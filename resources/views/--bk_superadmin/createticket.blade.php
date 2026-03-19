<!doctype html>
<html lang="en">
  <!-- [Head] start -->
@include('superadmin.superadminhead')
<style>
    	.dropzoneDragArea {
		    background-color: #fbfdff;
		    border: 1px dashed #c0ccda;
		    border-radius: 6px;
		    padding: 60px;
		    text-align: center;
		    margin-bottom: 15px;
		    cursor: pointer;
		}
		.dropzone{
			box-shadow: 0px 2px 20px 0px #f2f2f2;
			border-radius: 10px;
		}
  </style>
  <!-- [Head] end -->
  <!-- [Body] Start -->
  <script>
    function disablePastDates() {
      var today = new Date();
      var dd = String(today.getDate()).padStart(2, '0');
      var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
      var yyyy = today.getFullYear();

      today = yyyy + '-' + mm + '-' + dd;
      document.getElementById("target_date").setAttribute("min", today);
    }
  </script>
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
            <li class="breadcrumb-item"><a href="javascript: void(0)">Create Ticket</a></li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="page-header-title">
            <h2 class="mb-0">Create Ticket</h2>
            </div>
        </div>
        </div>
    </div>
    </div>
<!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-sm-12">
              <div class="card">
                <div class="card-body">
                  <div class="container">
                  <ul id="save_msgList"></ul>
                  <form method="post" action="{{ route('superadmin.storeticket') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" class="userid" name="userid" id="userid" value="">
                      <div class="row">
                        <div class="col-sm-4">
                            <div class="mb-3">
                              <label class="form-label">Locations </label>
                              <select class="mb-3 form-select" id="location_id" required name="location_id">
                                <option value="">Select Location</option>
                                  @if($locations)
                                    @foreach ($locations as $location)
                                      <option value="{{ $location->id }}">{{ $location->name}}</option>
                                    @endforeach
                                  @endif
                              </select>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="mb-3">
                              <label class="form-label">Category </label>
                              <select class="mb-3 form-select" id="category_id" required name="category_id">
                                <option value="">Select category</option>
                                  @if($categories)
                                    @foreach ($categories as $category)
                                      <option value="{{ $category->id }}">{{ $category->depart_name}}</option>
                                    @endforeach
                                  @endif
                              </select>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="mb-3">
                              <label class="form-label">Sub Category </label>
                              <select class="mb-3 form-select" id="subcategory_id" required name="subcategory_id">
                              </select>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="mb-3">
                              <label class="form-label">Target Date</label>
                              <input type="date" class="mb-3 form-select" id="target_date" name="target_date" onfocus="disablePastDates()">
                            </div>
                          </div>
                          <div class="col-sm-4">
                              <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="mb-3 form-select" required id="priority_status" name="priority_level">
                                <option value="">Select Priority</option>
                                  @if($priorities)
                                    @foreach ($priorities as $priority)
                                      <option value="{{ $priority->id }}">{{ $priority->priority_name}}</option>
                                    @endforeach
                                  @endif
                                </select>
                              </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="mb-3">
                              <label class="form-label">Users</label>
                              <select name="users" id="users" class="form-control">
                                <option value="">Select User</option>
                                 @php $users = App\Models\User::where('role', 'staff')->get();    @endphp
                                 @if ($users)
                                  @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                  @endforeach
                                 @endif
                              </select>
                            </div>
                          </div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label" for="exampleInputPassword1">Subject</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Enter Subject" required id="subject" name="subject"/>
                          </div>
                          <div class="mb-3">
                            <label class="form-label" for="exampleInputPassword1">Description</label>
                            <div id="tinymce-editor">
                              <textarea name="description" class="form-control" placeholder="Description" required id="description" name="description"></textarea>
                            </div>
                          </div>
                          <div class="mb-3 ">
                            <div class="form-group dropzone">
                              <div id="dropzoneDragArea" class="dz-default dz-message dropzoneDragArea ">
                                <span>Upload Attachments (Images and Pdf only)</span>
                              </div>
                              <div class="dropzone-previews"></div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="text-end mt-4">
                          <button type="submit" class="btn btn-outline-secondary">Clear</button>
                          <button type="submit" class="btn btn-primary add_item">Submit</button>
                        </div>
                      </div>
                  </form>
                  <button id="ticketCreate" class="btn btn-light-warning" style="display:none;">Try me!</button>
                  <button id="ticketMaxSize" class="btn btn-light-danger" style="display:none;">Try me!</button>
                    
                    </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter') 
    <script type="text/javascript">
    
    	$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		  });
    </script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#target_date').datepicker({ format: "yyyy/mm/dd" });
    }); 
    </script>
    <script>
        Dropzone.autoDiscover = false;
        // Dropzone.options.demoform = false;	
        let token = $('meta[name="csrf-token"]').attr('content');
        $(function() {
        var myDropzone = new Dropzone("div#dropzoneDragArea", { 
          paramName: "file",
          url: "{{ url('/storeSuperAdminImage') }}",
          previewsContainer: 'div.dropzone-previews',
          addRemoveLinks: true,
          autoProcessQueue: false,
          uploadMultiple: true,
          acceptedFiles: ".jpeg,.jpg,.png,.pdf",
          maxFilesize: 1, //MB
          parallelUploads: 10,
          maxFiles: 10,
          params: {
                _token: token
            },
          // The setting up of the dropzone
          init: function() {
            
              var myDropzone = this;
              //form submission code goes here
              $("form[name='demoform']").submit(function(event) {
                //Make sure that the form isn't actully being sent.
                event.preventDefault();

                URL = $("#demoform").attr('action');
                formData = $('#demoform').serialize();
                $.ajax({
                  type: 'POST',
                  url: URL,
                  data: formData,
                  success: function(result) {
                    if(result.status == "success") {
                      // fetch the useid 
                      $('#ticketCreate').click();
                      //reset the form
                      $('#demoform')[0].reset();
                      //reset dropzone
                      $('.dropzone-previews').empty();
                      var userid = result.user_id;
                      
                    $("#userid").val(userid); // inseting userid into hidden input field
                   
                      //process the queue
                      myDropzone.processQueue();
                    } else {
                      $.each(response.errors, function (key, err_value) {
                        $('#save_msgList').addClass('alert alert-danger');
                        $('#save_msgList').append('<li>' + err_value +
                            '</li>');
                      });
                    }
                  }
                });
              });

              //Gets triggered when we submit the image.
              this.on('sending', function(file, xhr, formData){
              //fetch the user id from hidden input field and send that userid with our image
                let userid = document.getElementById('userid').value;
              formData.append('userid', userid);
            });
            
              this.on("success", function (file, response) {
                   
                });

                this.on("queuecomplete", function () {
            
                });
            
                // Listen to the sendingmultiple event. In this case, it's the sendingmultiple event instead
              // of the sending event because uploadMultiple is set to true.
              this.on("sendingmultiple", function() {
                // Gets triggered when the form is actually being sent.
                // Hide the success button or the complete form.
              });
            
              this.on("successmultiple", function(files, response) {
                // Gets triggered when the files have successfully been sent.
                // Redirect user or notify of success.
              });
            
              this.on("errormultiple", function(files, response) {
                // Gets triggered when there was an error sending the files.
                // Maybe show form again, and notify user of error
              });
          }
          });
        myDropzone.on("error", function(file) {
          myDropzone.removeFile(file);
            $('#ticketMaxSize').click();
        });
       
      });
    </script>

    <script>
      $(document).ready(function() {
        $(document).on('change','#category_id', function() {
        let category = $(this).val();
            $.ajax({
                method: 'post',
                url: "/tasks/getSuperAdminSubCategory",
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
    });
</script>
    
    
  </body>
  <!-- [Body] end -->
</html>
