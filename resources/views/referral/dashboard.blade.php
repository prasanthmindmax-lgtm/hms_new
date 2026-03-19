<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->

  
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

  <body >
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
          <div class="row ">
            <div class="col-md-12">

            <div class="row justify-content-center">

            <div class="col-sm-9">
              <input type="text" id="icon-search" class="form-control mb-4"
              style="
    height: 35px;
    font-size: 11px;
"  placeholder="Search for AWB, Order ID, Buyer Mobile Number, Email, SKU, Pickup ID">
            </div>

            <div class="col-sm-3">
            <a href="#" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 editbtn" data-bs-toggle="modal" data-bs-target="#customer-edit_add-modal" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
    
    
"><i class="ti ti-plus f-18"></i> Add Tickets</a></div>

          </div>
            
             

            
            
            </div>
            
          </div>
        </div>
      </div>
<!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        
        <!-- [ Main Content ] end -->
        <div class="row">
        <div class="col-xl-12 col-md-12" style="
    margin-top: -37px;
">
            
        <div class="card-body border-bottom pb-0">
                <div class="d-flex align-items-center justify-content-between">
                  
                </div>
                <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button
                      class="nav-link active"
                      id="analytics-tab-1"
                      data-bs-toggle="tab"
                      data-bs-target="#analytics-tab-1-pane"
                      type="button"
                      role="tab"
                      aria-controls="analytics-tab-1-pane"
                      aria-selected="true"
                      >Recent Tickets</button
                    >
                  </li>
                  <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="analytics-tab-2"
                      data-bs-toggle="tab"
                      data-bs-target="#analytics-tab-2-pane"
                      type="button"
                      role="tab"
                      aria-controls="analytics-tab-2-pane"
                      aria-selected="false"
                      >Open Tickets</button
                    >
                  </li>
                  <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="analytics-tab-3"
                      data-bs-toggle="tab"
                      data-bs-target="#analytics-tab-3-pane"
                      type="button"
                      role="tab"
                      aria-controls="analytics-tab-3-pane"
                      aria-selected="false"
                      >Closed Tickets</button
                    >
                  </li>
                </ul>
              </div>

</div>
</div><br>


          
          
              
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel" aria-labelledby="analytics-tab-1" tabindex="0"
                >

                <div class="row">

                

<div class="col-xl-3 col-md-3">
<div class="card">
  
<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
    <i class="fa fa-calendar"></i>&nbsp;
    <span></span> <i class="fa fa-caret-down"></i>
</div>
</div>
</div>


<div class="col-xl-2 col-md-2">
<div class="">
<a href="#" class="btn btn-outline-primary d-inline-flex d-xxl-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_mail_filter"  style="
       height: 34px;
    width: 133px;
    font-size: 12px;
    
    
"><i class="ti ti-filter f-18"></i>&nbsp; More filter</a>
</div>

</div>



<div class="col-lg-3 col-xl-3" style="
    width: 58%;
">
                    <ul class="list-inline mb-3 d-flex align-items-right justify-content-end">
                    
                     
                      <li class="list-inline-item">
                        <a href="#" class="avtar avtar-s btn-link-secondary border border-secondary">
                          <i class="ti ti-download f-18"></i>
                        </a>
                      </li>
                      <li class="list-inline-item">
                        <div class="dropdown">
                          <a
                            href="#"
                            class="avtar avtar-s btn-link-secondary border border-secondary dropdown-toggle arrow-none"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                          >
                            <i class="ti ti-files f-18"></i>
                          </a>
                          <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#">Name</a>
                            <a class="dropdown-item" href="#">Date</a>
                            <a class="dropdown-item" href="#">Ratting</a>
                            <a class="dropdown-item" href="#">Unread</a>
                          </div>
                        </div>
                      </li>
                    </ul>
                   
                  </div>

                  
                </div>

                
                <p style="
    margin-top: -9px;
" class="text-muted f-12 mb-0"><span class="text-truncate w-100">0 Tickets for Last 30 days</span></p><br>

                <div class="col-xl-12 col-md-12">
                <div class="card">
                  <table class="table table-hover" id="pc-dt-simple1">
                    <thead>
                      <tr>
                      <th>Ticket No</th>
                        <th>Emp Name</th>
                        <th >Department</th>
                        <th>Priority</th>
                        <th>Ticket</th>
                        <th>Ticket2</th>
                        <th>Ticket3</th>
                        <th>Ticket4</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    </table>

                    </div>
</div>
                    
                        <center><div class="error-image-block"><img class="img-fluid" src="../assets/images/pages/nodata4.png" style="
    width: 434px;
" alt="img"></div></center>
                        <br><div class="card">
                        <div id="pagination"></div>
                </div>
                </div>
                <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel" aria-labelledby="analytics-tab-2" tabindex="0">
                <div class="col-xl-12 col-md-12">
                <div class="card">
                <table class="table table-hover" id="pc-dt-simple2">
                    <thead>
                      <tr>
                        <th>Ticket No</th>
                        <th>Emp Name</th>
                        <th class="text-end">Department</th>
                        <th>Priority</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          <div class="row align-items-center">
                            <div class="col-auto pe-0">
                              <img src="../assets/images/widget/img-prod-1.jpg" alt="user-image" class="wid-55 hei-55 rounded" />
                            </div>
                            <div class="col">
                              <h6 class="mb-2"><span class="text-truncate w-100">AXZE654</span></h6>
                              <p class="text-muted f-12 mb-0"
                                ><span class="text-truncate w-100">Need medicines in hebbal branch</span></p
                              >
                            </div>
                          </div>
                        </td>
                        <td>Ravichandran VS</td>
                        <td class="text-end f-w-600">Pharmacy</td>
                        <td class="f-w-600"
                          > <small class="f-w-800 badge bg-warning">Open</span> <i class="ti ti-arrow-up"></i></small
                        ></td>
                        <td><span class="badge bg-danger">High</span></td>
                      </tr>
                    </tbody>
                  </table>
</div>
</div>
                </div>
                <div class="tab-pane fade" id="analytics-tab-3-pane" role="tabpanel" aria-labelledby="analytics-tab-3" tabindex="0">
                <div class="col-xl-12 col-md-12">
                <div class="card">
                  
                
                
                <table class="table table-hover" id="pc-dt-simple3">
                    <thead>
                      <tr>
                        <th>Ticket No</th>
                        <th>Emp Name</th>
                        <th class="text-end">Department</th>
                        <th>Priority</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          <div class="row align-items-center">
                            <div class="col-auto pe-0">
                              <img src="../assets/images/widget/img-prod-1.jpg" alt="user-image" class="wid-55 hei-55 rounded" />
                            </div>
                            <div class="col">
                              <h6 class="mb-2"><span class="text-truncate w-100">ASDE654</span></h6>
                              <p class="text-muted f-12 mb-0"
                                ><span class="text-truncate w-100">Need medicines in hebbal branch</span></p
                              >
                            </div>
                          </div>
                        </td>
                        <td>Jayachandran</td>
                        <td class="text-end f-w-600">Lab</td>
                        <td class="f-w-600"
                          > <small class="f-w-800 badge bg-success">Closed</small
                        ></td>
                        <td><span class="badge bg-warning">Medium</span></td>
                      </tr>
                    </tbody>
                  </table>
</div >
</div >
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

    <div class="offcanvas-xxl offcanvas-start ecom-offcanvas" tabindex="-1" id="offcanvas_mail_filter">
    <div class="offcanvas-body p-0">
        <div id="ecom-filter" class="collapse collapse-horizontal show">
            <div class="ecom-filter">
                <div class="card">
                    <!-- Sticky Header -->
                    <div class="card-header d-flex align-items-center justify-content-between sticky-top bg-white">
                        <h5>Filter</h5>
                        <a
                            href="#"
                            class="avtar avtar-s btn-link-danger btn-pc-default"
                            data-bs-dismiss="offcanvas"
                            data-bs-target="#offcanvas_mail_filter"
                        >
                            <i class="ti ti-x f-20"></i>
                        </a>
                    </div>
                    <!-- Scrollable Block -->
                    <div class="scroll-block position-relative">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <!-- Filters -->
                                <li class="list-group-item px-0 py-2">
                                    <a class="btn border-0 px-0 text-start w-100" data-bs-toggle="collapse" href="#filtercollapse1">
                                        <div class="float-end"><i class="ti ti-chevron-down"></i></div>
                                        Gender
                                    </a>
                                    <div class="collapse show" id="filtercollapse1">
                                        <div class="py-3">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter1" value="option1" />
                                                <label class="form-check-label" for="genderfilter1">Male</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter2" value="option2" />
                                                <label class="form-check-label" for="genderfilter2">Female</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter3" value="option3" />
                                                <label class="form-check-label" for="genderfilter3">Kids</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item px-0 py-2">
                                    <a class="btn border-0 px-0 text-start w-100" data-bs-toggle="collapse" href="#filtercollapse1">
                                        <div class="float-end"><i class="ti ti-chevron-down"></i></div>
                                        Gender
                                    </a>
                                    <div class="collapse show" id="filtercollapse1">
                                        <div class="py-3">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter1" value="option1" />
                                                <label class="form-check-label" for="genderfilter1">Male</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter2" value="option2" />
                                                <label class="form-check-label" for="genderfilter2">Female</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter3" value="option3" />
                                                <label class="form-check-label" for="genderfilter3">Kids</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item px-0 py-2">
                                    <a class="btn border-0 px-0 text-start w-100" data-bs-toggle="collapse" href="#filtercollapse1">
                                        <div class="float-end"><i class="ti ti-chevron-down"></i></div>
                                        Gender
                                    </a>
                                    <div class="collapse show" id="filtercollapse1">
                                        <div class="py-3">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter1" value="option1" />
                                                <label class="form-check-label" for="genderfilter1">Male</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter2" value="option2" />
                                                <label class="form-check-label" for="genderfilter2">Female</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter3" value="option3" />
                                                <label class="form-check-label" for="genderfilter3">Kids</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item px-0 py-2">
                                    <a class="btn border-0 px-0 text-start w-100" data-bs-toggle="collapse" href="#filtercollapse1">
                                        <div class="float-end"><i class="ti ti-chevron-down"></i></div>
                                        Gender
                                    </a>
                                    <div class="collapse show" id="filtercollapse1">
                                        <div class="py-3">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter1" value="option1" />
                                                <label class="form-check-label" for="genderfilter1">Male</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter2" value="option2" />
                                                <label class="form-check-label" for="genderfilter2">Female</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter3" value="option3" />
                                                <label class="form-check-label" for="genderfilter3">Kids</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item px-0 py-2">
                                    <a class="btn border-0 px-0 text-start w-100" data-bs-toggle="collapse" href="#filtercollapse1">
                                        <div class="float-end"><i class="ti ti-chevron-down"></i></div>
                                        Gender
                                    </a>
                                    <div class="collapse show" id="filtercollapse1">
                                        <div class="py-3">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter1" value="option1" />
                                                <label class="form-check-label" for="genderfilter1">Male</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter2" value="option2" />
                                                <label class="form-check-label" for="genderfilter2">Female</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter3" value="option3" />
                                                <label class="form-check-label" for="genderfilter3">Kids</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item px-0 py-2">
                                    <a class="btn border-0 px-0 text-start w-100" data-bs-toggle="collapse" href="#filtercollapse1">
                                        <div class="float-end"><i class="ti ti-chevron-down"></i></div>
                                        Gender
                                    </a>
                                    <div class="collapse show" id="filtercollapse1">
                                        <div class="py-3">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter1" value="option1" />
                                                <label class="form-check-label" for="genderfilter1">Male</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter2" value="option2" />
                                                <label class="form-check-label" for="genderfilter2">Female</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter3" value="option3" />
                                                <label class="form-check-label" for="genderfilter3">Kids</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item px-0 py-2">
                                    <a class="btn border-0 px-0 text-start w-100" data-bs-toggle="collapse" href="#filtercollapse1">
                                        <div class="float-end"><i class="ti ti-chevron-down"></i></div>
                                        Gender
                                    </a>
                                    <div class="collapse show" id="filtercollapse1">
                                        <div class="py-3">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter1" value="option1" />
                                                <label class="form-check-label" for="genderfilter1">Male</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter2" value="option2" />
                                                <label class="form-check-label" for="genderfilter2">Female</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter3" value="option3" />
                                                <label class="form-check-label" for="genderfilter3">Kids</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item px-0 py-2">
                                    <a class="btn border-0 px-0 text-start w-100" data-bs-toggle="collapse" href="#filtercollapse1">
                                        <div class="float-end"><i class="ti ti-chevron-down"></i></div>
                                        Gender
                                    </a>
                                    <div class="collapse show" id="filtercollapse1">
                                        <div class="py-3">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter1" value="option1" />
                                                <label class="form-check-label" for="genderfilter1">Male</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter2" value="option2" />
                                                <label class="form-check-label" for="genderfilter2">Female</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter3" value="option3" />
                                                <label class="form-check-label" for="genderfilter3">Kids</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item px-0 py-2">
                                    <a class="btn border-0 px-0 text-start w-100" data-bs-toggle="collapse" href="#filtercollapse1">
                                        <div class="float-end"><i class="ti ti-chevron-down"></i></div>
                                        Gender
                                    </a>
                                    <div class="collapse show" id="filtercollapse1">
                                        <div class="py-3">
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter1" value="option1" />
                                                <label class="form-check-label" for="genderfilter1">Male</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter2" value="option2" />
                                                <label class="form-check-label" for="genderfilter2">Female</label>
                                            </div>
                                            <div class="form-check my-2">
                                                <input class="form-check-input" type="checkbox" id="genderfilter3" value="option3" />
                                                <label class="form-check-label" for="genderfilter3">Kids</label>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <!-- Add similar structure for other filters -->
                            </ul>
                        </div>
                        <!-- Fixed Clear All Button -->
                        <div class="card-footer sticky-bottom bg-white">
                        <div class="d-flex justify-content-between">
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" class="btn btn-outline-danger w-50 me-2">Clear All</a>
        <a href="#" style="height: 34px;width: 133px; font-size: 12px;" class="btn btn-outline-primary w-50">Submit</a>
    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                    <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ticket forms</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <ul id="save_msgList"></ul>
                            <div class="modal-body">
                            <form method="post" action="{{ route('staff.saveLoanDetails') }}" name="demoform" id="demoform"  enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" class="userid" name="userid" id="userid" value="">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Applicant Name:</label>
                                            <input type="text" class="form-control" id="app_name" name="app_name" placeholder="Applicant Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Patient Name:</label>
                                            <input type="text" class="form-control" id="patient_name" name="patient_name" placeholder="Patient Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">MRV Number:</label>
                                            <input type="text" class="form-control" id="mrv_no" name="mrv_no" placeholder="MRV Number">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Contact Number:</label>
                                            <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Contact Number">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Treatment Package:</label>
                                            <input type="text" class="form-control" id="treatment_pack" name="treatment_package" placeholder="Treatment Package">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Package Amt: </label>
                                            <input type="text" class="form-control" id="loan_req_amt" name="loan_req_amt" placeholder="Package Amt">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Loan Requirement Amt:</label>
                                            <input type="text" class="form-control" id="loan_req_amt" name="loan_req_amt" placeholder="Loan Req Amt">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Applicant Pan No: </label>
                                            <input type="text" class="form-control" id="app_pan_no" name="app_pan_no" placeholder="Applicant Pan No">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Applicant Aadhar No: </label>
                                            <input type="text" class="form-control" id="app_aadhar_no" name="app_aadhar_no" placeholder="Applicant Aadhar No">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="mb-3">
                                            <label class="form-label required">Applicant Salary Type:</label>
                                            <select class="mb-3 form-select" id="appl_income_type" required name="appl_income_type">
                                            <option value="">Income Type</option>
                                            <option value="1">Salaried</option>
                                            <option value="2">Business</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label required">Monthly Income:</label>
                                            <input type="text" class="form-control" id="monthly_income" name="monthly_income" placeholder="Monthly Income">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-9">
                                        <div class="form-group dropzone">
                                        <div id="dropzoneDragArea" class="dz-default dz-message dropzoneDragArea ">
                                            <span>Upload Attachments (Images and Pdf only)</span>
                                        </div>
                                        <div class="dropzone-previews"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" style="
       height: 34px;
    width: 133px;
    font-size: 12px;

    
" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" style="
       height: 34px;
    width: 133px;
    font-size: 12px;
    
    
" class="btn btn-outline-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>

              <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


              <script type="text/javascript">
   
    
        // Set the initial start and end dates
        var start = moment().subtract(29, 'days');
        var end = moment();
    
        // Callback function to update the span text with the selected date range
        function cb(start, end) {
            // Check the selected date range and adjust the display accordingly
            if (start.isSame(end, 'day')) {
                // If the start and end date are the same, show the single date
                if (start.isSame(moment(), 'day')) {
                    $('#reportrange span').html('Today');
                } else if (start.isSame(moment().subtract(1, 'days'), 'day')) {
                    $('#reportrange span').html('Yesterday');
                } else {
                    $('#reportrange span').html(start.format('DD/MM/YYYY'));
                }
            } else {
                // For other ranges like "Last 7 Days", "This Month", etc.
                $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
            }
        }
    
        // Initialize the date range picker
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
    
        // Set initial date range text
        cb(start, end);


        $(document).on('click', '.editbtn', function (e) {
                    $('#exampleModal').modal('show');
                });

    
        $("#dashboard_color").css("color", "#96488b");
  

  
    </script>


    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
