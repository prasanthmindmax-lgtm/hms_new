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

.zoom:hover {
    -ms-transform: scale(1.5);
    /* IE 9 */
    -webkit-transform: scale(1.5);
    /* Safari 3-8 */
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

.dropzone {
    box-shadow: 0px 2px 20px 0px #f2f2f2;
    border-radius: 10px;
}
@media (min-width:992px) {
    .page-container {
        max-width: 1140px;
        margin: 0 auto
    }

    .page-sidenav {
        display: block !important
    }
}

.padding {
    padding: 2rem
}

.w-32 {
    width: 32px !important;
    height: 32px !important;
    font-size: .85em
}

.tl-item .avatar {
    z-index: 2
}

.circle {
    border-radius: 500px
}

.gd-warning {
    color: #fff;
    border: none;
    background: #f4c414 linear-gradient(45deg, #f4c414, #f45414)
}

.timeline {
    position: relative;
    border-color: rgba(160, 175, 185, .15);
    padding: 0;
    margin: 0
}

.p-4 {
    padding: 1.5rem !important
}

.block,
.card {
    background: #fff;
    border-width: 0;
    border-radius: .25rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
    margin-bottom: 1.5rem
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important
}

.tl-item {
    border-radius: 3px;
    position: relative;
    display: -ms-flexbox;
    display: flex
}

.tl-item>* {
    padding: 10px
}

.tl-item .avatar {
    z-index: 2
}

.tl-item:last-child .tl-dot:after {
    display: none
}

.tl-item.active .tl-dot:before {
    border-color: #448bff;
    box-shadow: 0 0 0 4px rgba(68, 139, 255, .2)
}

.tl-item:last-child .tl-dot:after {
    display: none
}

.tl-item.active .tl-dot:before {
    border-color: #448bff;
    box-shadow: 0 0 0 4px rgba(68, 139, 255, .2)
}

.tl-dot {
    position: relative;
    border-color: rgba(160, 175, 185, .15)
}

.tl-dot:after,
.tl-dot:before {
    content: '';
    position: absolute;
    border-color: inherit;
    border-width: 2px;
    border-style: solid;
    border-radius: 50%;
    width: 10px;
    height: 10px;
    top: 15px;
    left: 50%;
    transform: translateX(-50%)
}

.tl-dot:after {
    width: 0;
    height: auto;
    top: 25px;
    bottom: -15px;
    border-right-width: 0;
    border-top-width: 0;
    border-bottom-width: 0;
    border-radius: 0
}

tl-item.active .tl-dot:before {
    border-color: #448bff;
    box-shadow: 0 0 0 4px rgba(68, 139, 255, .2)
}

.tl-dot {
    position: relative;
    border-color: rgba(160, 175, 185, .15)
}

.tl-dot:after,
.tl-dot:before {
    content: '';
    position: absolute;
    border-color: inherit;
    border-width: 2px;
    border-style: solid;
    border-radius: 50%;
    width: 10px;
    height: 10px;
    top: 15px;
    left: 50%;
    transform: translateX(-50%)
}

.tl-dot:after {
    width: 0;
    height: auto;
    top: 25px;
    bottom: -15px;
    border-right-width: 0;
    border-top-width: 0;
    border-bottom-width: 0;
    border-radius: 0
}

.tl-content p:last-child {
    margin-bottom: 0
}

.tl-date {
    font-size: .85em;
    margin-top: 2px;
    min-width: 100px;
    max-width: 100px
}

.avatar {
    position: relative;
    line-height: 1;
    border-radius: 500px;
    white-space: nowrap;
    font-weight: 700;
    border-radius: 100%;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: center;
    justify-content: center;
    -ms-flex-align: center;
    align-items: center;
    -ms-flex-negative: 0;
    flex-shrink: 0;
    border-radius: 500px;
    box-shadow: 0 5px 10px 0 rgba(50, 50, 50, .15)
}



</style>
<!-- https://demo.themesberg.com/pixel-pro/css/pixel.css -->
<link rel="stylesheet" href="https://demo.themesberg.com/pixel-pro/css/pixel.css" />
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
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Ticket Detail</a></li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Ticket Detail</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                    <div class="card-header">
                        <h5><i data-feather="lock" class="icon-svg-primary wid-20"></i><span class="p-l-5">Ticket No:@if($ticketDetail->ticket_no) # {{ $ticketDetail->ticket_no }} @else '-' @endif</span></h5>
                    </div>
                    <div class="card-body border-bottom py-2">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="d-inline-block mb-0">{{ $ticketDetail->subject }}</h4>
                            </div>
                        </div>
                    </div>
                        @if($ticketActivities)
                            @foreach ($ticketActivities as $ticketActivity) 
                                @php $createUser = App\Models\UserProfile::where('user_id',  $ticketActivity->staff_id)->first();    @endphp
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
                                                <h4 class="d-inline-block">@if ($ticketActivity->staff_id ==  auth()->user()->id ){{ 'You' }} @else {{ $createUser->fullname }} @endif</h4>
                                                @php $statusMaster = App\Models\StatusModel::where('id',  $ticketActivity->ticket_status)->first();    @endphp
                                                <span class="badge" style="background-color: {{ $statusMaster->status_color }} ">{{ $statusMaster->status_name ?? ''}}</span>
                                                @php $priorityMaster = App\Models\PriorityModel::where('id',  $ticketActivity->priotity_level)->first();    @endphp
                                                <label class="badge" style="background: #eaf6f2; color: {{ $priorityMaster->priority_color }}">{{ $priorityMaster->priority_name }}</label>
                                                <p class="text-muted"><b>Created:</b>{{ date('d-m-Y H:i:s', strtotime($ticketActivity->updated_at)); }} &nbsp;&nbsp; <b>Target:</b> {{ date('d-m-Y', strtotime($ticketDetail->target_date )); }}</p>
                                            </div>
                                            </div>
                                            <div class="col-auto">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-inline-block f-20">
                                                <a href="#" data-bs-toggle="tooltip" title="Delete">
                                                    <i data-feather="trash-2" class="icon-svg-danger wid-20"></i>
                                                </a>
                                                </li>
                                            </ul>
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
                                                    <div class="horizontal-scroll-block1 grow overflow-x-auto ">
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
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                    
                            <div class="card task-card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5>TimeLine</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled task-list">
                                    @foreach ($ticketActivities as $ticketActivity)
                                    @php $createUser1 = App\Models\UserProfile::where('user_id',  $ticketActivity->staff_id)->first();    @endphp
                                    <li>
                                        <i class="feather f-w-600 task-icon" style="background-color: #AA6BA1"></i>
                                        <p class="m-b-5">{{ date('jS M, Y H:i:s', strtotime($ticketActivity->updated_at)) }}</p>
                                        <h5 class="text-muted">{{ $createUser1->fullname }}</h5>
                                    </li>
                                    @endforeach
                                </ul>
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
                $(document).on('change', '#category_id', function() {
                    let category = $(this).val();
                    $.ajax({
                        method: 'post',
                        url: "/getSuperAdminSubCategory",
                        data: {
                            category: category
                        },
                        success: function(res) {
                            if (res.status == '200') {
                                let all_options =
                                    "<option value=''>Select Sub Category</option>";
                                let all_subcategories = res.subcategories;
                                $.each(all_subcategories, function(index, value) {
                                    all_options += "<option value='" + value.id +
                                        "'>" + value.sub_category_name +
                                        "</option>";
                                });
                                $("#subcategory_id").html(all_options);
                            }
                        }
                    });
                });

                $(document).on('click', '.editbtn', function(e) {
                    $('#exampleModal').modal('show');
                });

                $(document).on('click', '.editbtn1', function(e) {
                    $('#updateStatusModal').modal('show');
                });


                $(document).on('click', '.approve_ticket', function(e) {
                    e.preventDefault();

                    $(this).text('Sending...');
                    var id = $('#ticket_id').val();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "/adminApproveTicket/" + id,
                        dataType: "json",
                        success: function(response) {
                            // console.log(response);
                            if (response.status == 404) {
                                $('#success_message').addClass('alert alert-success');
                                $('#success_message').text(response.message);
                                $('.approve_ticket').text('Yes Sent');
                            } else {
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
                                    if (result.status == "success") {
                                        // fetch the useid 
                                        var userid = result.user_id;
                                        $('#exampleModal').modal('hide');
                                        $('#ticketReply').click();
                                        $("#refresh").load(location.href +
                                            " #refresh");
                                        //reset the form
                                        $('#demoform')[0].reset();
                                        //reset dropzone
                                        $('.dropzone-previews').empty();
                                        $("#userid").val(
                                        userid); // inseting userid into hidden input field
                                        //process the queue
                                        myDropzone.processQueue();
                                    } else {
                                        $.each(response.errors, function(key,
                                            err_value) {
                                            $('#save_msgList').addClass(
                                                'alert alert-danger'
                                                );
                                            $('#save_msgList').append(
                                                '<li>' + err_value +
                                                '</li>');
                                        });
                                    }
                                }
                            });
                        });

                        //Gets triggered when we submit the image.
                        this.on('sending', function(file, xhr, formData) {
                            //fetch the user id from hidden input field and send that userid with our image
                            let userid = document.getElementById('userid').value;
                            formData.append('userid', userid);
                        });

                        this.on("success", function(file, response) {

                        });

                        this.on("queuecomplete", function() {
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
            <script type="text/javascript">
            var tc = document.querySelectorAll('.horizontal-scroll-block');
            for (var t = 0; t < tc.length; t++) {
                new SimpleBar(tc[t]);
            }
            </script>
            <script src="{{ asset('/assets/js/plugins/prism.js') }} "></script>
</body>
<!-- [Body] end -->

</html>