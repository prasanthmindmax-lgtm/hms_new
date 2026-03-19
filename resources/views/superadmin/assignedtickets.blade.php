<!doctype html>
<html lang="en">
  <!-- [Head] start -->
@include('admin.adminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->

  <body @@bodySetup>
    <div class="page-loader">
      <div class="bar"></div>
    </div>
    <!-- [ Sidebar Menu ] start -->
    @include('admin.adminnav')
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
    @include('admin.adminheader')   
    
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
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item"><a href="javascript: void(0)">Assigned Tickets</a></li>
            </ul>
        </div>
        <div class="col-md-12">
            <div class="page-header-title">
            <h4 class="mb-0">Assigned Tickets</h4>
            </div>
        </div>
        </div>
    </div>
    </div>
<!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
          <!-- DOM/Jquery table start -->
          <div class="col-xl-12 col-md-12">
            <div class="card">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover" id="pc-dt-simple">
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
                          > <small class="f-w-800 badge bg-success">Close</small
                        ></td>
                        <td><span class="badge bg-warning">Medium</span></td>
                      </tr>
                      <tr>
                        <td>
                          <div class="row align-items-center">
                            <div class="col-auto pe-0">
                              <img src="../assets/images/widget/img-prod-1.jpg" alt="user-image" class="wid-55 hei-55 rounded" />
                            </div>
                            <div class="col">
                              <h6 class="mb-2"><span class="text-truncate w-100">AXZE654</span></h6>
                              <p class="text-muted f-12 mb-0"
                                ><span class="text-truncate w-100">Need medical hit in lab</span></p
                              >
                            </div>
                          </div>
                        </td>
                        <td>Perumal S</td>
                        <td class="text-end f-w-600">IT</td>
                        <td class="f-w-600"
                          > <small class="f-w-800 badge bg-primary">Open</small
                        ></td>
                        <td><span class="badge bg-warning">Medium</span></td>
                      </tr>
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
                        <td>Vikas</td>
                        <td class="text-end f-w-600">Maintenance</td>
                        <td class="f-w-600"
                          > <small class="f-w-800 badge bg-warning">Open</small
                        ></td>
                        <td><span class="badge bg-primary">Low</span></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- DOM/Jquery table end -->
          <!-- Column Rendering table start -->
          
          <!-- Column Rendering table end -->
          <!-- Multiple Table Control Elements start -->
          <!-- Row Created Callback table end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
    @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
