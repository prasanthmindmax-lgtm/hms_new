<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Path fragments → full "Parent > Child" module path.
     * More specific paths must come before general ones.
     */
    protected array $moduleMap = [
        // ── Discount module ──────────────────────────────
        'discountform_document_new'   => 'Discount > Create Form',
        'discount-dashboard'          => 'Discount > Dashboard',
        'discount_documentadded'      => 'Discount > Create Form',
        'discountformeditsave'        => 'Discount > Edit Form',
        'discount/approve-reject'     => 'Discount > Approve / Reject',

        // ── Cancel Bill module ───────────────────────────
        'cancelbillform'              => 'Cancel Bill > Create Form',
        'cancelbill-dashboard'        => 'Cancel Bill > Dashboard',
        'cancelbill_added'            => 'Cancel Bill > Create Form',
        'cancel-bill/approve-reject'  => 'Cancel Bill > Approve / Reject',

        // ── Refund module ────────────────────────────────
        'refundform'                  => 'Refund > Create Form',
        'refundbill-dashboard'        => 'Refund > Dashboard',
        'refund_documentadded'        => 'Refund > Create Form',
        'refundformeditsave'          => 'Refund > Edit Form',
        'refund-bill/approve-reject'  => 'Refund > Approve / Reject',

        // ── Income ──────────────────────────────────────
        'income_reconciliation'       => 'Income > Reconciliation',
        'income_reconciliation_overview' => 'Income > Overview',
        'income_montly_report'        => 'Income > Monthly Report',
        'income_reconciliation_branch'=> 'Income > Branch',
        'bill_list'                   => 'Income > Bill List',
        'bill_overall_list'           => 'Income > Bill Overall',
        'incomereportfetch'           => 'Income > Report',

        // ── Branch / Financial ───────────────────────────
        'branch-financial'            => 'Branch > Financial',
        'financial-reports'           => 'Branch > Financial Report',

        // ── Bank Reconciliation ──────────────────────────
        'bank-reconciliation'         => 'Bank > Reconciliation',

        // ── Vendor / Purchase ────────────────────────────
        'purchase_maker'              => 'Vendor > Purchase Maker',
        'purchase_checker'            => 'Vendor > Purchase Checker',
        'purchase_approver'           => 'Vendor > Purchase Approver',
        'purchase_dashboard'          => 'Vendor > Purchase Orders',
        'purchase_order_create'       => 'Vendor > Create PO',
        'quotation_dashboard'         => 'Vendor > Quotation',
        'quotation_create'            => 'Vendor > Create Quotation',
        'neft_dashboard'              => 'Vendor > NEFT',
        'neft_create'                 => 'Vendor > Create NEFT',
        'grn_dashboard'               => 'Vendor > GRN',
        'grn_create'                  => 'Vendor > Create GRN',
        'grn_convert'                 => 'Vendor > GRN Convert',
        'bill_dashboard'              => 'Vendor > Bill',
        'bill_create'                 => 'Vendor > Create Bill',
        'bill_made_dashboard'         => 'Vendor > Bill Made',
        'bill_made_create'            => 'Vendor > Create Bill Made',
        'tdstax_dashboard'            => 'Vendor > TDS Tax',
        'gsttax_dashboard'            => 'Vendor > GST Tax',
        'tds_summary'                 => 'Vendor > TDS Summary',
        'tds_report'                  => 'Vendor > TDS Report',
        'gst_summary'                 => 'Vendor > GST Summary',
        'asset_dashboard'             => 'Vendor > Asset Master',
        'nature_payment_dashboard'    => 'Vendor > Nature of Payment',
        'address_dashboard'           => 'Vendor > Address',
        'vendor_type'                 => 'Vendor > Vendor Type',
        'transcationvendor'           => 'Vendor > Transaction',
        'vendor_summary'              => 'Vendor > Summary',
        'reports'                     => 'Vendor > Reports',
        'vendor_create'               => 'Vendor > Create Vendor',
        'vendor'                      => 'Vendor',
        'customer_create'             => 'Vendor > Create Customer',
        'customer'                    => 'Vendor > Customer',
        'company'                     => 'Vendor > Company',

        // ── Tickets ──────────────────────────────────────
        'ticketmaster'                => 'Ticket > Master',
        'ticketActivity'              => 'Ticket > Activity',
        'ticket'                      => 'Ticket > Management',

        // ── Camp ─────────────────────────────────────────
        'campupdatedversion'          => 'Camp > Management',
        'camp'                        => 'Camp > Management',

        // ── ART Module ───────────────────────────────────
        'profile_ivf'                 => 'ART > IVF > Profile',
        'ovarian_ivf'                 => 'ART > IVF > Ovarian',
        'eggpick_ivf'                 => 'ART > IVF > Egg Pickup',
        'embryology_ivf'              => 'ART > IVF > Embryology',
        'embryology_freezind_ivf'     => 'ART > IVF > Freezing',
        'embryology_transfer_ivf'     => 'ART > IVF > Transfer',
        'outcome_ivf'                 => 'ART > IVF > Outcome',
        'profile_odicsi'              => 'ART > ODICSI > Profile',
        'profile_edicsi'              => 'ART > EDICSI > Profile',
        'art_index'                   => 'ART > Index',
        'art'                         => 'ART',

        // ── Check-in ─────────────────────────────────────
        'checkin'                     => 'Check-In > Report',

        // ── Vehicle ──────────────────────────────────────
        'vehicledocument'             => 'Vehicle > Documents',
        'vehicle'                     => 'Vehicle > Management',

        // ── Travel ───────────────────────────────────────
        'travel'                      => 'Travel > Booking',

        // ── Daily / Security / Attendance / License ──────
        'securitydaily_document'      => 'Security > Daily Document',
        'dailyattendance_document'    => 'Attendance > Document',
        'license_document'            => 'License > Document',
        'admindaily_document'         => 'Admin > Daily Document',
        'dailysummary'                => 'Daily Summary',

        // ── Documents ────────────────────────────────────
        'discountform_document'       => 'Documents > Discount',
        'document-management'         => 'Documents > Management',

        // ── User / Access ─────────────────────────────────
        'masteraccess'                => 'Access > Master Access',
        'usermanagent'                => 'Access > User Management',

        // ── Registration / Reports ───────────────────────
        'registrationreport'          => 'Reports > Registration',
        'registrationview'            => 'Reports > Registration View',
        'patientdashboard'            => 'Reports > Patient Dashboard',

        // ── Profile ──────────────────────────────────────
        'profile'                     => 'Profile',

        // ── Auth ─────────────────────────────────────────
        'login'                       => 'Auth > Login',
        'logout'                      => 'Auth > Logout',
    ];

    /** URL fragments that should NEVER be logged (background polling) */
    protected array $skipPaths = [
        'superadmin/logs',
        'superadmin/logs-data',
        'superadmin/logs-store',
        'superadmin/logs-stats',
        'superadmin/logs-clear',
        'superadmin/disformsave_data',
        'superadmin/cancelformsave_data',
        'superadmin/refundform_data',
        'superadmin/discountform_data',
        'superadmin/cancelform_data',
        'superadmin/cancelbill_data',
        'superadmin/cancelsave_data',
        'superadmin/discountmrdno',
        'superadmin/mrd-progress',
        'superadmin/mrd-final',
        'superadmin/mrd-start',
        'superadmin/fetchdocument',
        'superadmin/fetch',
        'superadmin/fetchfitter',
        'superadmin/fetchmorefitter',
        'superadmin/fetchmorefitterremove',
        'superadmin/fetchmorefitterdate',
        'superadmin/fetchmorefitterdateclr',
        'superadmin/purchase_fetch',
        'superadmin/purchase_approver_fetch',
        'superadmin/purchase_checker_fetch',
        'superadmin/vendor_fetch',
        'superadmin/dailysummaryapi',
        'superadmin/dailysummaryfetch',
        'superadmin/userdetails',
        'superadmin/employee-data',
        'superadmin/get-menu-permissions',
        'superadmin/get-user-details',
        'superadmin/menuaccessurl',
        'superadmin/branchurls',
        'superadmin/branchselectvalue',
        'superadmin/zonefetchviews',
        'superadmin/branchfetchviews',
        'superadmin/marketernamesurls',
        'superadmin/campalldetails',
        'superadmin/activitesalldetails',
        'superadmin/expensesalldetails',
        'superadmin/incomefetchdetails',
        'superadmin/incomeupdatedetails',
        'superadmin/billalldetails',
        'superadmin/bill_overall_list_get',
        'superadmin/incomereportfetch',
        'superadmin/incomeoverviewdatefilter',
        'superadmin/incomebranchfilter',
        'superadmin/incomedatefilter',
        'superadmin/incomereportfilter',
        'superadmin/checkinurlapi',
        'superadmin/checkinreportfetch',
        'superadmin/registrationfetch',
        'superadmin/registrationfetchbranch',
        'superadmin/income_reconciliation_data',
        'superadmin/income_reconciliation_overview_data',
        'superadmin/income_montly_report_data',
        'superadmin/ticketfetch',
        'superadmin/myticketfetch',
        'superadmin/allticketfetch',
        'superadmin/fetchticketfitter',
        'superadmin/fetchmyticketfitter',
        'superadmin/fetchallticketfitter',
        'superadmin/fetchticketfitterremove',
        'superadmin/fetchmyticketfitterremove',
        'superadmin/fetchallfitterremove',
        'superadmin/vehiclefetch',
        'superadmin/vehicledocumentview',
        'superadmin/vehicledocumentfilter',
        'superadmin/travelfetch',
        'superadmin/getdocnametype',
        'superadmin/patientid',
        'superadmin/meetingid',
        'superadmin/meetingallviews',
        'superadmin/patientallviews',
        'superadmin/campfetchurlfitters',
        'superadmin/marketermainsearch',
        'superadmin/doctordetailsid',
        'superadmin/campactivitespopuop',
        'superadmin/campexpensivepopuop',
        'superadmin/admin_fetchdocument',
        'superadmin/discount_fetchdocument',
        'superadmin/security_fetchdocument',
        'superadmin/attendance_fetchdocument',
        'superadmin/license_fetchdocument',
        'superadmin/refundformdoc_detials',
        'superadmin/discountformdoc_detials',
        'superadmin/branchfetchviews',
        'superadmin/get-all-zones',
        'superadmin/get-all-branches',
        'superadmin/get-all-marketers',
        'superadmin/get-zonal-heads-by-zone',
        'superadmin/get-marketers-by-zone',
        'superadmin/get-marketers-by-zonal-head',
        'superadmin/checkinlastfetch',
        'superadmin/checkinreportfilter',
        'superadmin/checkinbranchfilter',
        'superadmin/checkindatefilter',
        'superadmin/checkinccname',
        'superadmin/leadsdata',
        'superadmin/activitydata',
        'superadmin/ticket/chat/history',
        'superadmin/dailydatefilter',
        'superadmin/dailybranchfilter',
        'superadmin/myticketmasterfetch',
        'superadmin/fetchmyticketmasterfitter',
        'superadmin/myticketmasterdate',
        'superadmin/myticketfillter',
        'superadmin/myticketdatefillter',
        'superadmin/ticketfillter',
        'superadmin/ticketdatefillter',
        'superadmin/allticketfillter',
        'superadmin/allticketdatefillter',
        'superadmin/fetchUrlmorefittervechicle',
        'superadmin/vehicledatefillter',
        'superadmin/vehiclemorefilter',
        'superadmin/vehiclemoredocfilter',
        'superadmin/vehicledocumentUrlfilter',
        'superadmin/vehicleInsuranceDocument',
        'superadmin/vehicledocumentedit',
        'superadmin/travelfilter',
        'superadmin/traveledit',
        'superadmin/campdateanddataftters',
        'superadmin/campdatefitters',
        'superadmin/campdateandsearchfitters',
        'superadmin/activitesdatefitters',
        'superadmin/activitesdatanaddatefitters',
        'superadmin/activitesdateandfittertexts',
        'superadmin/licexpdatefilter',
        'superadmin/licensedoc_detials',
        'superadmin/securityDetails',
        'superadmin/securityreport',
        'superadmin/securityshiftdata',
        'superadmin/att_detials_edit',
        'superadmin/attendance_detials',
        'superadmin/attendancedatefilter',
        'superadmin/security_detials_edit',
        'superadmin/vendor_fetch',
        'superadmin/fetchmorefitterpurchase',
        'superadmin/fetchmorefitterdateclrpurchase',
        'superadmin/purchasefetchfitter',
        'superadmin/purchase_checker_fetch',
        'superadmin/income_reconciliation_branch',
        'superadmin/patientpop',
        'superadmin/meetingpop',
        'superadmin/branchselectvalue',
        'superadmin/getSubcategory',
        'ajax/',
        '_debugbar',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Only log authenticated users
        if (!auth()->check()) {
            return $response;
        }

        // 2. Skip ALL AJAX / XHR / JSON background requests
        if ($request->ajax() || $request->wantsJson() || $request->isXmlHttpRequest()) {
            return $response;
        }

        // 3. Only log GET page navigations (menu/page visits)
        if ($request->method() !== 'GET') {
            return $response;
        }

        $path = $request->path();

        // 4. Skip background/polling URLs
        foreach ($this->skipPaths as $skip) {
            if (str_contains($path, $skip)) {
                return $response;
            }
        }

        // 5. Detect module path (most specific first)
        $module = 'Dashboard';
        foreach ($this->moduleMap as $key => $label) {
            if (str_contains($path, $key)) {
                $module = $label;
                break;
            }
        }

        // 6. Build a clean description
        $description = 'Navigated to: /' . $path;

        ActivityLog::log('Page Visit', $module, $description);

        return $response;
    }
}
