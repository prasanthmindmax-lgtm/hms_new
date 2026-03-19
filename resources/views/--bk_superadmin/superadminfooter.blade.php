<footer class="pc-footer">
      <div class="footer-wrapper container-fluid">
        <div class="row">
          <div class="col my-1">
            <!-- <p>Powered by The Mindmax</p> -->
          </div>
        </div>
      </div>
    </footer>
     <!-- Required Js -->
<script src="{{ asset('/assets/js/plugins/popper.min.js') }} "></script>
<script src="{{ asset('/assets/js/plugins/simplebar.min.js') }} "></script>
<script src="{{ asset('/assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('/assets/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('/assets/js/pcoded.js') }}"></script>
<script src="{{ asset('/assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('/assets/js/notifications/notification-active.js') }}"></script>
<script src="{{ asset('/assets/js/notifications/Lobibox.js') }}"></script>
<script src="{{ asset('/assets/js/layout-compact.js') }}"></script>
 <!-- [Page Specific JS] start -->
 <script src="{{ asset('/assets/js/plugins/apexcharts.min.js') }}"></script>
 <script src="{{ asset('/assets/js/pages/dashboard-help.js') }}"></script>
 <!-- [Page Specific JS] end -->
<script>
  layout_change('light');
</script>
<script>
  layout_theme_contrast_change('false');
</script>
<script>
  change_box_container('false');
</script>
<script>
  layout_caption_change('true');
</script>
<script>
  layout_rtl_change('false');
</script>
<script>
  preset_change('preset-1');
</script>
    <!-- file-upload Js -->
    <!-- [Page Specific JS] end -->
    <div class="offcanvas border-0 pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">Settings</h5>
        <button type="button" class="btn btn-icon btn-link-danger ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"
          ><i class="ti ti-x"></i
        ></button>
      </div>
      <div class="pct-body customizer-body">
        <div class="offcanvas-body py-0">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="pc-dark">
                <h6 class="mb-1">Theme Mode</h6>
                <p class="text-muted text-sm">Choose light or dark mode or Auto</p>
                <div class="row theme-color theme-layout">
                  <div class="col-4">
                    <div class="d-grid">
                      <button
                        class="preset-btn btn active"
                        data-value="true"
                        onclick="layout_change('light');"
                        data-bs-toggle="tooltip"
                        title="Light"
                      >
                        <svg class="pc-icon text-warning">
                          <use xlink:href="#custom-sun-1"></use>
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="d-grid">
                      <button class="preset-btn btn" data-value="false" onclick="layout_change('dark');" data-bs-toggle="tooltip" title="Dark">
                        <svg class="pc-icon">
                          <use xlink:href="#custom-moon"></use>
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="d-grid">
                      <button
                        class="preset-btn btn"
                        data-value="default"
                        onclick="layout_change_default();"
                        data-bs-toggle="tooltip"
                        title="Automatically sets the theme based on user's operating system's color scheme."
                      >
                        <span class="pc-lay-icon d-flex align-items-center justify-content-center">
                          <i class="ph-duotone ph-cpu"></i>
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <h6 class="mb-1">Theme Contrast</h6>
              <p class="text-muted text-sm">Choose theme contrast</p>
              <div class="row theme-contrast">
                <div class="col-6">
                  <div class="d-grid">
                    <button
                      class="preset-btn btn"
                      data-value="true"
                      onclick="layout_theme_contrast_change('true');"
                      data-bs-toggle="tooltip"
                      title="True"
                    >
                      <svg class="pc-icon">
                        <use xlink:href="#custom-mask"></use>
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-grid">
                    <button
                      class="preset-btn btn active"
                      data-value="false"
                      onclick="layout_theme_contrast_change('false');"
                      data-bs-toggle="tooltip"
                      title="False"
                    >
                      <svg class="pc-icon">
                        <use xlink:href="#custom-mask-1-outline"></use>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <h6 class="mb-1">Custom Theme</h6>
              <p class="text-muted text-sm">Choose your primary theme color</p>
              <div class="theme-color preset-color">
                <a href="#!" data-bs-toggle="tooltip" title="Blue" class="active" data-value="preset-1"><i class="ti ti-checks"></i></a>
                <a href="#!" data-bs-toggle="tooltip" title="Indigo" data-value="preset-2"><i class="ti ti-checks"></i></a>
                <a href="#!" data-bs-toggle="tooltip" title="Purple" data-value="preset-3"><i class="ti ti-checks"></i></a>
                <a href="#!" data-bs-toggle="tooltip" title="Pink" data-value="preset-4"><i class="ti ti-checks"></i></a>
                <a href="#!" data-bs-toggle="tooltip" title="Red" data-value="preset-5"><i class="ti ti-checks"></i></a>
                <a href="#!" data-bs-toggle="tooltip" title="Orange" data-value="preset-6"><i class="ti ti-checks"></i></a>
                <a href="#!" data-bs-toggle="tooltip" title="Yellow" data-value="preset-7"><i class="ti ti-checks"></i></a>
                <a href="#!" data-bs-toggle="tooltip" title="Green" data-value="preset-8"><i class="ti ti-checks"></i></a>
                <a href="#!" data-bs-toggle="tooltip" title="Teal" data-value="preset-9"><i class="ti ti-checks"></i></a>
                <a href="#!" data-bs-toggle="tooltip" title="Cyan" data-value="preset-10"><i class="ti ti-checks"></i></a>
              </div>
            </li>
            <li class="list-group-item">
              <h6 class="mb-1">Theme layout</h6>
              <p class="text-muted text-sm">Choose your layout</p>
              <div class="theme-main-layout d-flex align-center gap-1 w-100">
                <a href="#!" data-bs-toggle="tooltip" title="Vertical" class="active" data-value="vertical">
                  <img src="{{ asset('/assets/images/customizer/caption-on.svg') }}" alt="img" class="img-fluid" />
                </a>
                <a href="#!" data-bs-toggle="tooltip" title="Horizontal" data-value="horizontal">
                  <img src="{{ asset('/assets/images/customizer/horizontal.svg') }}" alt="img" class="img-fluid" />
                </a>
                <a href="#!" data-bs-toggle="tooltip" title="Color Header" data-value="color-header">
                  <img src="{{ asset('/assets/images/customizer/color-header.svg') }}" alt="img" class="img-fluid" />
                </a>
                <a href="#!" data-bs-toggle="tooltip" title="Compact" data-value="compact">
                  <img src="{{ asset('/assets/images/customizer/compact.svg') }}" alt="img" class="img-fluid" />
                </a>
                <a href="#!" data-bs-toggle="tooltip" title="Tab" data-value="tab">
                  <img src="{{ asset('/assets/images/customizer/tab.svg') }}" alt="img" class="img-fluid" />
                </a>
              </div>
            </li>
            <li class="list-group-item">
              <h6 class="mb-1">Sidebar Caption</h6>
              <p class="text-muted text-sm">Sidebar Caption Hide/Show</p>
              <div class="row theme-color theme-nav-caption">
                <div class="col-6">
                  <div class="d-grid">
                    <button
                      class="preset-btn btn-img btn active"
                      data-value="true"
                      onclick="layout_caption_change('true');"
                      data-bs-toggle="tooltip"
                      title="Caption Show"
                    >
                      <img src="{{ asset('/assets/images/customizer/caption-on.svg') }}" alt="img" class="img-fluid" />
                    </button>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-grid">
                    <button
                      class="preset-btn btn-img btn"
                      data-value="false"
                      onclick="layout_caption_change('false');"
                      data-bs-toggle="tooltip"
                      title="Caption Hide"
                    >
                      <img src="{{ asset('/assets/images/customizer/caption-off.svg') }}" alt="img" class="img-fluid" />
                    </button>
                  </div>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="pc-rtl">
                <h6 class="mb-1">Theme Layout</h6>
                <p class="text-muted text-sm">LTR/RTL</p>
                <div class="row theme-color theme-direction">
                  <div class="col-6">
                    <div class="d-grid">
                      <button
                        class="preset-btn btn-img btn active"
                        data-value="false"
                        onclick="layout_rtl_change('false');"
                        data-bs-toggle="tooltip"
                        title="LTR">
                        <img src="{{ asset('/assets/images/customizer/ltr.svg') }}" alt="img" class="img-fluid" />
                      </button>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="d-grid">
                      <button
                        class="preset-btn btn-img btn"
                        data-value="true"
                        onclick="layout_rtl_change('true');"
                        data-bs-toggle="tooltip"
                        title="RTL">
                        <img src="{{ asset('/assets/images/customizer/rtl.svg') }}" alt="img" class="img-fluid" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <li class="list-group-item pc-box-width">
              <div class="pc-container-width">
                <h6 class="mb-1">Layout Width</h6>
                <p class="text-muted text-sm">Choose Full or Container Layout</p>
                <div class="row theme-color theme-container">
                  <div class="col-6">
                    <div class="d-grid">
                      <button
                        class="preset-btn btn-img btn active"
                        data-value="false"
                        onclick="change_box_container('false')"
                        data-bs-toggle="tooltip" title="Full Width" >
                        <img src="{{ asset('/assets/images/customizer/full.svg') }}" alt="img" class="img-fluid" />
                      </button>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="d-grid">
                      <button
                        class="preset-btn btn-img btn"
                        data-value="true"
                        onclick="change_box_container('true')"
                        data-bs-toggle="tooltip"
                        title="Fixed Width"
                      >
                        <img src="{{ asset('/assets/images/customizer/fixed.svg') }}" alt="img" class="img-fluid" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="d-grid">
                <button class="btn btn-light-danger" id="layoutreset">Reset Layout</button>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- [Page Specific JS] start -->
    <!-- datatable Js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('/assets/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ asset('/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('/assets/referral/doctor-details.js') }}"></script>
    <script src="{{ asset('/assets/referral/doctor-added.js') }}"></script>
    <script src="{{ asset('/assets/referral/doctor-meeting.js') }}"></script>
    <script src="{{ asset('/assets/referral/doctor-patient.js') }}"></script>
    <script src="{{ asset('/assets/referral/referral_info.js') }}"></script>
    <script src="{{ asset('/assets/document/documentadded.js') }}"></script>
    <script src="{{ asset('/assets/Usermanagement/users-added.js') }}"></script>
    <script src="{{ asset('/assets/campmanagement/campmanage.js') }}"></script>
    <script src="{{ asset('/assets/campmanagement/activitesmanage.js') }}"></script>
    <script src="{{ asset('/assets/campmanagement/expensesmanage.js') }}"></script>
    <script src="{{ asset('/assets/branch/branch.js') }}"></script>
    {{-- <script src="{{ asset('/assets/menu/menu.js') }}"></script> --}}
    <script src="{{ asset('/assets/income/Income_reconciliation.js') }}"></script>
    <script src="{{ asset('/assets/accounts/income_details.js') }}"></script>
    <script src="{{ asset('/assets/accounts/billwisedata.js') }}"></script>



    <script>
    const addedUrl = "{{ route('superadmin.doctoradded') }}";
    const fetchUrl = "{{ route('superadmin.fetch') }}";
    const fetchUrlfitter = "{{ route('superadmin.fetchfitter') }}";
    const fetchUrlmorefitter = "{{ route('superadmin.fetchmorefitter') }}";
    const fetchUrlmorefitterremove = "{{ route('superadmin.fetchmorefitterremove') }}";
    const fetchUrlmorefitterdate = "{{ route('superadmin.fetchmorefitterdate') }}";
    const fetchUrlmorefitterdateclear = "{{ route('superadmin.fetchmorefitterdateclr') }}";
    const doctordetailseditsall = "{{ route('superadmin.doctordetailsedit') }}";
    const doctordetailsid = "{{ route('superadmin.doctordetailsid') }}";
    const marketermainsearch = "{{ route('superadmin.marketermainsearch') }}";
    const branchfetchviews = "{{ route('superadmin.branchfetchviews') }}";
    const zonefetchviews = "{{ route('superadmin.zonefetchviews') }}";
    const marketernamesurls = "{{ route('superadmin.marketernamesurls') }}";


    // meeting blade url in js .............................
    const meetingfetch = "{{ route('superadmin.meetingid') }}";
    const meetinginsert = "{{ route('superadmin.meetinginsert') }}";
    const meetingviews="{{ route('superadmin.meetingallviews') }}";
    const meetingdatefitter="{{ route('superadmin.meetingdatefitter') }}";
    const meetingmorefitter="{{ route('superadmin.meetingmorefitter') }}";
    const meetingremovefitter="{{ route('superadmin.meetingremovefitter') }}";
    const meetingclrfitter="{{ route('superadmin.meetingclrfitter') }}";
    const meetingdateandfitter="{{ route('superadmin.meetingdateandfitter') }}";
    // End meeting blade url in js ...........................
        // patient blade url in js .............................
        const patientfetch = "{{ route('superadmin.patientid') }}";
    const patientinsert = "{{ route('superadmin.patientinsert') }}";
    const patientviews="{{ route('superadmin.patientallviews') }}";
    const patientdatefitter="{{ route('superadmin.patientdatefitter') }}";
    const patientmorefitter="{{ route('superadmin.patientmorefitter') }}";
    const patientremovefitter="{{ route('superadmin.patientremovefitter') }}";
    const patientclrfitter="{{ route('superadmin.patientclrfitter') }}";
    const patientdateandfitter="{{ route('superadmin.patientdateandfitter') }}";
    // End patient blade url in js ...........................
    const patientpop="{{ route('superadmin.patientpop') }}";
    const meetingpop="{{ route('superadmin.meetingpop') }}";
    // doucment details updations
    const documentaddedUrl = "{{ route('superadmin.documentadded') }}";
    const documentupdatedUrl = "{{ route('superadmin.documentupdated') }}";
    const fetchUrldocument = "{{ route('superadmin.fetchdocument') }}";
    const branchurl="{{ route('superadmin.branchurls') }}";
    const menuaccessurl="{{ route('superadmin.menuaccessurl') }}";
    // added user management
    const addusermanagement = "{{ route('superadmin.usermanagentadded') }}";
    const userviewsall="{{ route('superadmin.userdetails') }}";
    //camp Management
      const campdetailsadded= "{{ route('superadmin.campdetailsadded') }}";
      const campalldetails= "{{ route('superadmin.campalldetails') }}";
      const campfetchurlfitters= "{{ route('superadmin.campdateanddataftters') }}";
      const campdatefitters= "{{ route('superadmin.campdatefitters') }}";
      const campdateandsearchfitters= "{{ route('superadmin.campdateandsearchfitters') }}";

      //activity Management

      const activitesadddata = "{{ route('superadmin.activitesadddata') }}";
      const activitesalldetails= "{{ route('superadmin.activitesalldetails') }}";
      const activitesdatefitters= "{{ route('superadmin.activitesdatefitters') }}";
      const activitesdatanaddatefitters= "{{ route('superadmin.activitesdatanaddatefitters') }}";
      const activitesdateandfittertexts= "{{ route('superadmin.activitesdateandfittertexts') }}";

      // expenses management

      const expensesadddata= "{{ route('superadmin.expensesadddata') }}";
      const expensesalldetails= "{{ route('superadmin.expensesalldetails') }}";

      // income
      const incomefetchdetails= "{{ route('superadmin.incomefetchdetails') }}";
      const incomeupdatedetails= "{{ route('superadmin.incomeupdatedetails') }}";
      const billalldetails= "{{ route('superadmin.billalldetails') }}";


      // href link urls

      const bill_overall_list = "{{ route('superadmin.bill_overall_list') }}";
      const patientdashboard = "{{ route('superadmin.patientdashboard') }}";
      const bill_overall_list_get = "{{ route('superadmin.bill_overall_list_get') }}";


    </script>
    <script>
      // [ DOM/jquery ]
      var total, pageTotal;
      var table = $('#dom-jqry').DataTable();
      // [ column Rendering ]
      $('#colum-render').DataTable({
        columnDefs: [
          {
            render: function (data, type, row) {
              return data + ' (' + row[3] + ')';
            },
            targets: 0
          },
          {
            visible: false,
            targets: [3]
          }
        ]
      });
      // [ Multiple Table Control Elements ]
      $('#multi-table').DataTable({
        dom: '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>'
      });
      // [ Complex Headers With Column Visibility ]
      $('#complex-header').DataTable({
        columnDefs: [
          {
            visible: false,
            targets: -1
          }
        ]
      });
      // [ Language file ]
      $('#lang-file').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'
        }
      });
      // [ Setting Defaults ]
      $('#setting-default').DataTable();
      // [ Row Grouping ]
      var table1 = $('#row-grouping').DataTable({
        columnDefs: [
          {
            visible: false,
            targets: 2
          }
        ],
        order: [[2, 'asc']],
        displayLength: 25,
        drawCallback: function (settings) {
          var api = this.api();
          var rows = api
            .rows({
              page: 'current'
            })
            .nodes();
          var last = null;
          api
            .column(2, {
              page: 'current'
            })
            .data()
            .each(function (group, i) {
              if (last !== group) {
                $(rows)
                  .eq(i)
                  .before('<tr class="group"><td colspan="5">' + group + '</td></tr>');
                last = group;
              }
            });
        }
      });
      // [ Order by the grouping ]
      $('#row-grouping tbody').on('click', 'tr.group', function () {
        var currentOrder = table.order()[0];
        if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
          table.order([2, 'desc']).draw();
        } else {
          table.order([2, 'asc']).draw();
        }
      });
      // [ Footer callback ]
      $('#footer-callback').DataTable({
        footerCallback: function (row, data, start, end, display) {
          var api = this.api(),
            data;
          // Remove the formatting to get integer data for summation
          var intVal = function (i) {
            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
          };
          // Total over all pages
          total = api
            .column(4)
            .data()
            .reduce(function (a, b) {
              return intVal(a) + intVal(b);
            }, 0);
          // Total over this page
          pageTotal = api
            .column(4, {
              page: 'current'
            })
            .data()
            .reduce(function (a, b) {
              return intVal(a) + intVal(b);
            }, 0);
          // Update footer
          $(api.column(4).footer()).html('$' + pageTotal + ' ( $' + total + ' total)');
        }
      });
      // [ Custom Toolbar Elements ]
      $('#c-tool-ele').DataTable({
        dom: '<"toolbar">frtip'
      });
      // [ Custom Toolbar Elements ]
      $('div.toolbar').html('<b>Custom tool bar! Text/images etc.</b>');
      // [ custom callback ]
      $('#row-callback').DataTable({
        createdRow: function (row, data, index) {
          if (data[5].replace(/[\$,]/g, '') * 1 > 150000) {
            $('td', row).eq(5).addClass('highlight');
          }
        }
      });
    </script>
    <script src="{{ asset('/assets/js/plugins/simple-datatables.js') }}"></script>
    <script src="{{ asset('/assets/js/plugins/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pages/ac-alert.js') }}"></script>
    <script>
      const dataTable = new simpleDatatables.DataTable('#pc-dt-simple', {
        sortable: false,
        perPage: 5
      });
      // new SimpleBar(document.querySelector('.sale-scroll'));
      // new SimpleBar(document.querySelector('.feed-scroll'));
      new SimpleBar(document.querySelector('.revenue-scroll'));
      new SimpleBar(document.querySelector('.income-scroll'));
      new SimpleBar(document.querySelector('.customer-scroll'));
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
  document.getElementById('ticketReply').addEventListener('click', () => {
    window.dispatchEvent(new CustomEvent('swal:toast', {
        detail: {
          title:'Info!',
          text: 'Ticket reply done',
          icon: 'success',
          background: 'success',
        }
    }));
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
  document.getElementById('ticketCreate').addEventListener('click', () => {
    window.dispatchEvent(new CustomEvent('swal:toast', {
        detail: {
          title:'Info!',
          text: 'Ticket created successfully',
          icon: 'success',
          background: 'success',
        }
    }));
  });
</script>
<script>
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
  document.getElementById('ticketMaxSize').addEventListener('click', () => {
    window.dispatchEvent(new CustomEvent('swal:toast', {
        detail: {
          title:'Info!',
          text: 'More than 1 MB should not be allowed',
          icon: 'danger',
          background: 'danger',
        }
    }));
  });
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
  document.getElementById('approveTicketRequest').addEventListener('click', () => {
    window.dispatchEvent(new CustomEvent('swal:toast', {
        detail: {
          title:'Info!',
          text: 'Approve request sent successfully!!',
          icon: 'primary',
          background: 'primary',
        }
    }));
  });
</script>
