<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <style>
  * {
    box-sizing: border-box;
  }

  .zoom {
    height: 1000px;
    width: 2000px;
  }
 h4, .h4 {
    font-size: 1.2rem;
}  
	p {
		font-size: 12px;
	}
  .zoom:hover {
    -ms-transform: scale(1.5); /* IE 9 */
    -webkit-transform: scale(1.5); /* Safari 3-8 */
    transform: scale(1.5); 
  }
</style>
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
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript: void(0)">Ticket Detail</a></li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0" style="font-size: 1.23rem;">Ticket Detail</h2>
            </div>
        </div>
        </div>
    </div>
    </div>
<!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
       
      <div class="row" id="refresh">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header">
                <h5><i data-feather="lock" class="icon-svg-primary wid-20"></i><span class="p-l-5">Ticket No: #{{ $ticketDetail->ticket_no }}</span></h5>
              </div>
              <div class="card-body border-bottom py-2">
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <h4 class="d-inline-block mb-0">{{ $ticketDetail->subject }}</h4>
                  </div>
				  @if(auth()->user()->role_id == 1 && auth()->user()->access_limits == 2)		
				  <div class="col-md-12 text-md-end">
                    <div class="btn-star" id="buttonStatus">
                      <!-- <a href="#!" class="btn btn-light-success btn-sm"></a> -->
                      <button type="button" class="btn btn-sm my-2 btn-light-success @if($ticketDetail->is_management_approve == 0) editbtn1 @endif"
                      ><i class="mx-2 feather icon-message-square"></i>@if($ticketDetail->is_management_approve == 0) {{ 'Request for Management Approval' }} @elseif($ticketDetail->is_management_approve == 1) {{ 'Waiting for approval' }} @endif </button
                      >
                    </div>
                    
                  </div>
				  @endif
                  <!-- <div class="col-md-4 text-md-end">
                    <div class="btn-star" id="buttonStatus">
                     
                      <button type="button" class="btn btn-sm my-2 btn-light-success editbtn1"
                      ><i class="mx-2 feather icon-message-square"></i>@if($ticketDetail->is_management_approve == 0) {{ 'Sent Request' }} @elseif($ticketDetail->is_management_approve == 1) {{ 'Waiting for approval' }} @endif </button
                      >
                    </div>
                  </div> -->
                </div>
              </div>
              @if($ticketActivities)
              @foreach ($ticketActivities as $ticketActivity) 
              @php $createUser = $createUser = App\Models\usermanagementdetails::
                                join('tbl_ticket_activities','users.id','=', 'tbl_ticket_activities.created_by')
                                ->where('tbl_ticket_activities.created_by', $ticketActivity->created_by)->first();  @endphp
                <div class="border-bottom card-body">
                  <div class="row">
                    <div class="col-sm-auto mb-3 mb-sm-0">
                      <div class="d-sm-inline-block d-flex align-items-center">
                        <img class="wid-60 img-radius mb-2" src="{{ asset('/assets/images/grp-icon.png') }}" alt="Generic placeholder image " />
                        
                      </div>
                    </div>
                    <div class="col">
                      <div class="row">
                        <div class="col">
                          <div class="">
                            <h4 class="mb-1">@if ($ticketActivity->created_by ==  auth()->user()->id ){{ 'You' }} @else {{ $createUser->user_fullname }} @endif</h4>
                              @php $statusMaster = App\Models\StatusModel::where('id',  $ticketActivity->ticket_status)->first();    @endphp
                              @if($statusMaster)<span class="badge" style="background-color: {{ $statusMaster->status_color }} ">{{ $statusMaster->status_name ?? ''}}</span>@endif
                              @php $priorityMaster = App\Models\PriorityModel::where('id',  $ticketActivity->priotity_level)->first();    @endphp
                              <label class="badge" style="background: #eaf6f2; color: {{ $priorityMaster->priority_color }}">{{ $priorityMaster->priority_name }}</label>
                              <p class="text-muted"><b>Created:</b>{{ date('d-m-Y H:i:s', strtotime($ticketActivity->updated_at)); }} &nbsp;&nbsp; <b>Target:</b> {{ date('d-m-Y', strtotime($ticketDetail->target_date )); }}</p>
                          </div>
                        </div>
                        
                      </div>
                      <div class="">
                        <p>{{ $ticketActivity->description }}</p>
                        
                      </div>
                      <div class="row text-center mb-2">
                      @php $ticketImages = App\Models\ImageModel::where('ticket_id',  $ticketActivity->id)->get();    @endphp
                        @if ($ticketImages)
                        @foreach ($ticketImages as $ticketImage)
                        <div class="col-xl-2 col-lg-3 col-sm-4 col-xs-12 button thumbwrap">
                          @php $val = explode('.', $ticketImage->imgName) @endphp
                          @if ($val[1] == 'pdf')
                            <a href="{{ asset('/uploads/'. $ticketImage->imgName) }}" target="_blank" type="application/pdf"><img src="{{ asset('/assets/images/pdf.png') }}" class="img-fluid" alt="PDF" ></a>
                          @else
                          <div class="vertical-scroll-block grow overflow-x-auto">
                                <div class="flex gap-2 item-start flex-nowrap">
                                  <a class="group block overflow-hidden mb-0 min-w-[115px]" data-fslightbox="gallery" href="{{ asset('/uploads/'. $ticketImage->imgName) }}">
                                    <img src="{{ asset('/uploads/'. $ticketImage->imgName) }}" class="card-img relative z-10 transition-all duration-300 group-hover:scale-[1.2]" alt="img">
                                  </a>
                                </div>
                            </div> 
                          @endif
                        </div>
                        @endforeach
                        @endif
                        
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
              @endif
              @if ($ticketDetail->is_management_approve == 0 || $ticketDetail->is_management_approve == 1 || $ticketDetail->created_by ==  auth()->user()->id)
              <div class="border-bottom card-body py-2">
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <button type="button" class="btn btn-sm my-2 btn-light-success editbtn"
                      ><i class="mx-2 feather icon-message-square"></i>Reply</button
                    >
                  </div>
                </div>
              </div>
              @endif
              
            </div>
          </div>
         
          </div>
      </div>
     @if(auth()->user()->role_id == 1 && auth()->user()->access_limits == 1)	
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
                <h5 class="modal-title" id="exampleModalLabel">Reply Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <ul id="save_msgList"></ul>
                <div class="modal-body">
                    <form method="post" action="{{ route('superadmin.manageReplyActivity') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
                      @csrf
                        <input type="hidden" class="userid" name="userid" id="userid" value="">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label pt-0">Ticket Status<span class="text-danger">*</span></label>
                            <select class="form-control" id="ticket_status" name="ticket_status" required>
                                <option value="">Select status</option>
                                @php $statues = App\Models\StatusModel::where('approve_status', '1')->get();    @endphp
                                @if ($statues)
                                  @foreach ($statues as $status)
                                      <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                                  @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label pt-0">Description<span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" placeholder="Description" required></textarea>
                        </div>
                        <div class="mb-3 ">
                          <div class="form-group dropzone">
                            <div id="dropzoneDragArea" class="dz-default dz-message dropzoneDragArea dz-clickable">
                              <span>Upload Attachments (Images and Pdf only)</span>
                            </div>
                            <div class="dropzone-previews"></div>
                          </div>
                        </div>
                        <input type="hidden" name="priotity_level" id="priotity_level" value="{{ $ticketDetail->priority }}">
                        <input type="hidden" name="ticket_id" id="ticket_id" value="{{ $ticketDetail->id }}">
                        <input type="hidden" name="staff_id" id="staff_id" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="created_by" id="created_by" value="{{ auth()->user()->id }}">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <button id="ticketReply" class="btn btn-light-primary" style="display:none;">Try me!</button>
    <button id="ticketMaxSize" class="btn btn-light-danger" style="display:none;">Try me!</button>
    @endif
	@if(auth()->user()->role_id == 1 && auth()->user()->access_limits == 2)
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
                <h5 class="modal-title" id="exampleModalLabel">Reply Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <ul id="save_msgList"></ul>
                <div class="modal-body">
                    <form method="post" action="{{ route('superadmin.adminReplyActivity') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
                    @csrf
                        <input type="hidden" class="userid" name="userid" id="userid" value="">
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label pt-0">Ticket Status<span class="text-danger">*</span></label>
                            <select class="form-control" id="ticket_status" name="ticket_status" required>
                              <option value="">Ticket Status</option>
                                @php $statues = App\Models\StatusModel::where('approve_status', '0')->get();    @endphp
                                @if ($statues)
                                    @foreach ($statues as $status)
                                        <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="recipient-name" class="col-form-label pt-0" >Description<span class="text-danger">*</span></label>
                            <textarea name="description" id="description" required class="form-control" placeholder="Description"></textarea>
                        </div>
                        <div class="mb-3 ">
                            <div class="form-group dropzone">
                              <div id="dropzoneDragArea" class="dz-default dz-message dropzoneDragArea ">
                                <span>Upload Images</span>
                              </div>
                              <div class="dropzone-previews"></div>
                            </div>
                        </div>
                        <input type="hidden" name="priotity_level" id="priotity_level" value="{{ $ticketDetail->priority }}">
                        <input type="hidden" name="ticket_id" id="ticket_id" value="{{ $ticketDetail->id }}">
                        <input type="hidden" name="assigned_by" id="assigned_by" value="{{ $ticketDetail->assigned_by }}">
                        <input type="hidden" name="department_id" id="department_id" value="{{ $ticketDetail->department_id }}">
                        <input type="hidden" name="sub_department_id" id="sub_department_id" value="{{ $ticketDetail->sub_department_id }}">
                        <input type="hidden" name="created_by" id="created_by" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="staff_id" id="staff_id" value="{{ $ticketActivities[0]->staff_id }}">
                        <input type="hidden" name="read_yes" id="read_yes" value="2">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-sm-12">
    <div class="card-body pc-component btn-page">
        <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusLabel"  aria-hidden="true"
        >
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusLabel">Send Approval Request to Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>Are you sure to send Request?</h4>
                <input type="hidden" id="ticket_id" value="{{ $ticketDetail->id }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary approve_ticket">Send Now</button>
            </div>
        </div>
      </div>
    </div>
    </div>
    <button id="ticketReply" class="btn btn-light-primary" style="display:none;">Try me!</button>
    <button id="ticketMaxSize" class="btn btn-light-danger" style="display:none;">Try me!</button>
    <button id="approveTicketRequest" class="btn btn-light-danger" style="display:none;">Try me!</button>
    
  </div>  
  @endif
  @if(auth()->user()->role_id == 1 && auth()->user()->access_limits == 3)
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
                  <h5 class="modal-title" id="exampleModalLabel">Reply Ticket</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <ul id="save_msgList"></ul>
                  <div class="modal-body">
                      <form method="post" action="{{ route('superadmin.staffReplyActivity') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
                          @csrf
                          <input type="hidden" class="userid" name="userid" id="userid" value="">
                          
                          <div class="mb-3">
                              <label for="recipient-name" class="col-form-label pt-0">Description<span class="text-danger">*</span></label>
                              <textarea name="description" required id="description" class="form-control" placeholder="Description"></textarea>
                          </div>
                          <div class="mb-3 ">
                            <div class="form-group dropzone">
                              <div id="dropzoneDragArea" class="dz-default dz-message dropzoneDragArea ">
                                <span>Upload Images</span>
                              </div>
                              <div class="dropzone-previews"></div>
                            </div>
                          </div>
                          <input type="hidden" name="priotity_level" id="priotity_level" value="{{ $ticketDetail->priority }}">
                          <input type="hidden" name="ticket_id" id="ticket_id" value="{{ $ticketDetail->id }}">
                          <input type="hidden" name="staff_id" id="staff_id" value="{{ auth()->user()->id }}">
                          <input type="hidden" name="created_by" id="created_by" value="{{ auth()->user()->id }}">
                          <input type="hidden" name="ticket_status" id="ticket_status" value="{{ $ticketDetail->ticket_status }}">
                          <input type="hidden" name="department_id" id="department_id" value="{{ $ticketDetail->department_id }}">
                          <input type="hidden" name="sub_department_id" id="sub_department_id" value="{{ $ticketDetail->sub_department_id }}">
                        
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Submit</button>
                          </div>
                      </form>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  @endif  
    </div>
	<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>	
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter') 
    <script>
    	$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    }
		});

        $(document).ready(function() {       

        $(document).on('click', '.editbtn', function (e) {
            $('#exampleModal').modal('show');
        });

        $(document).on('click', '.editbtn1', function (e) {
            $('#updateStatusModal').modal('show');
        });

     $(document).on('click', '.approve_ticket', function (e) {
          e.preventDefault();

          $(this).text('Sending...');
          var id = $('#ticket_id').val();

          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          $.ajax({
              type: "GET",
              url: "/superadmin/adminApproveActivity/" + id,
              dataType: "json",
              success: function (response) {
                  // console.log(response);
                  if (response.status == 404) {
                      $('#success_message').addClass('alert alert-success');
                      $('#success_message').text(response.message);
                      $('.approve_ticket').text('Yes Sent');
                  } else {
                      $("#refresh").load(location.href + " #refresh"); 
                      $('.approve_ticket').text('Yes Sent');
                      $('#updateStatusModal').modal('hide');
                      $('#approveTicketRequest').click();
                      $("#buttonStatus").load(location.href + " #buttonStatus");
                      $("#itemDeleted").click();
                  }
              }
          });
      });
    });    
    </script>
    <script>
      Dropzone.autoDiscover = false;
      // Dropzone.options.demoform = false;	
      let token = $('meta[name="csrf-token"]').attr('content');
      $(function() {
      var myDropzone = new Dropzone("div#dropzoneDragArea", { 
        paramName: "file",
        url: "{{ url('/storeManagemenetImage') }}",
		clickable: "#dropzoneDragArea span",
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
                    var userid = result.user_id;
                    $('#exampleModal').modal('hide');
                    $('#ticketReply').click();
                    $("#refresh").load(location.href + " #refresh");
                    //reset the form
                    $('#demoform')[0].reset();
                    //reset dropzone
                    $('.dropzone-previews').empty();
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
                $("#refresh").load(location.href + " #refresh");
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
  
  <script src="{{ asset('/assets/js/plugins/index.js') }}"></script>
    <script>
          var tc = document.querySelectorAll('.horizontal-scroll-block');
          for (var t = 0; t < tc.length; t++) {
              new SimpleBar(tc[t]);
          }
  </script>
<script src="{{ asset('/assets/js/plugins/prism.js') }} "></script>
  </body>
  <!-- [Body] end -->
</html>
