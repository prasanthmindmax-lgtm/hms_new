<?php
use App\Exports\QuotationTemplateExport;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AiCompareController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\BankStatementController;
use App\Http\Controllers\BillingStatsController;
use App\Http\Controllers\BranchFinancialController;
use App\Http\Controllers\EmailMasterController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\IncomeReconciliationController;
use App\Http\Controllers\LocationMasterController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\MenuMasterController;
use App\Http\Controllers\PettyCashController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RadiantCashPickupController;
use App\Http\Controllers\RadiantMismatchAlertController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\IndentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\LicenceDocumentCatalogController;
use App\Http\Controllers\LicenceDocumentController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WebNotificationController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/superadmin/dashboard',[SuperAdminController::class,'dashboard'])->name('superadmin.dashboard');
Route::get('/referral/dashboard',[MarketController::class,'dashboard'])->name('referral.dashboard');


Route::middleware(['auth','role_id:1','log.activity'])->group(function () {
    Route::get('/superadmin/dashboard',[SuperAdminController::class,'dashboard'])->name('superadmin.dashboard');
    Route::get('/superadmin/referral',[SuperAdminController::class,'referral'])->name('superadmin.referral');
    Route::get('/superadmin/Income_reconciliation',[SuperAdminController::class,'Income_reconciliation'])->name('superadmin.Income_reconciliation');
    Route::get('/superadmin/camp',[SuperAdminController::class,'camp'])->name('superadmin.camp');
    Route::get('/superadmin/campupdatedversion',[SuperAdminController::class,'campupdatedversion'])->name('superadmin.campupdatedversion');
    Route::get('/superadmin/usermanagent',[SuperAdminController::class,'usermanagent'])->name('superadmin.usermanagent');
    Route::get('/superadmin/document-management',[SuperAdminController::class,'documentmanagement'])->name('superadmin.document-management');
    Route::get('/superadmin/bill_list',[SuperAdminController::class,'billlistoverall'])->name('superadmin.bill_list');
    Route::get('/superadmin/branchselectvalue',[SuperAdminController::class,'branchselectvalue'])->name('superadmin.branchselectvalue');

    Route::post('/superadmin/doctoradded', [SuperAdminController::class, 'doctoradded'])->name('superadmin.doctoradded');
    Route::post('/superadmin/doctoraddedimg', [SuperAdminController::class, 'doctoraddedimgs'])->name('superadmin.doctoraddedimg');
    Route::get('/superadmin/fetch', [SuperAdminController::class, 'fetch'])->name('superadmin.fetch');
    Route::get('/superadmin/fetchfitter', [SuperAdminController::class, 'fetchfitter'])->name('superadmin.fetchfitter');
    Route::GET('/superadmin/fetchmorefitter', [SuperAdminController::class, 'fetchmorefitter'])->name('superadmin.fetchmorefitter');
    Route::get('/superadmin/fetchmorefitterremove', [SuperAdminController::class, 'fetchmorefitterremove'])->name('superadmin.fetchmorefitterremove');
    Route::get('/superadmin/fetchmorefitterdate', [SuperAdminController::class, 'fetchmorefitterdate'])->name('superadmin.fetchmorefitterdate');
    Route::get('/superadmin/fetchmorefitterdateclr', [SuperAdminController::class, 'fetchmorefitterdateclr'])->name('superadmin.fetchmorefitterdateclr');
    Route::post('/superadmin/doctordetailsedit', [SuperAdminController::class, 'doctordetailsedit'])->name('superadmin.doctordetailsedit');
    Route::get('/superadmin/doctordetailsid', [SuperAdminController::class, 'doctordetailsid'])->name('superadmin.doctordetailsid');
    Route::get('/superadmin/marketermainsearch', [SuperAdminController::class, 'marketermainsearch'])->name('superadmin.marketermainsearch');

 // doctor fitters end ......
    Route::get('/superadmin/meetingid', [SuperAdminController::class, 'meetingid'])->name('superadmin.meetingid');
    Route::post('/superadmin/meetinginsert', [SuperAdminController::class, 'meetinginsert'])->name('superadmin.meetinginsert');
    Route::get('/superadmin/meetingallviews', [SuperAdminController::class, 'meetingallviews'])->name('superadmin.meetingallviews');
    Route::get('/superadmin/meetingdatefitter', [SuperAdminController::class, 'meetingdatefitter'])->name('superadmin.meetingdatefitter');
    Route::get('/superadmin/meetingmorefitter', [SuperAdminController::class, 'meetingmorefitter'])->name('superadmin.meetingmorefitter');
    Route::get('/superadmin/meetingremovefitter', [SuperAdminController::class, 'meetingremovefitter'])->name('superadmin.meetingremovefitter');
    Route::get('/superadmin/meetingclrfitter', [SuperAdminController::class, 'meetingclrfitter'])->name('superadmin.meetingclrfitter');
    Route::get('/superadmin/meetingdateandfitter', [SuperAdminController::class, 'meetingdateandfitter'])->name('superadmin.meetingdateandfitter');

   // meeting details end .................
    Route::get('/superadmin/patientid', [SuperAdminController::class, 'patientid'])->name('superadmin.patientid');
    Route::post('/superadmin/patientinsert', [SuperAdminController::class, 'patientinsert'])->name('superadmin.patientinsert');
    Route::get('/superadmin/patientallviews', [SuperAdminController::class, 'patientallviews'])->name('superadmin.patientallviews');
    Route::get('/superadmin/patientdatefitter', [SuperAdminController::class, 'patientdatefitter'])->name('superadmin.patientdatefitter');
    Route::get('/superadmin/patientmorefitter', [SuperAdminController::class, 'patientmorefitter'])->name('superadmin.patientmorefitter');
    Route::get('/superadmin/patientremovefitter', [SuperAdminController::class, 'patientremovefitter'])->name('superadmin.patientremovefitter');
    Route::get('/superadmin/patientclrfitter', [SuperAdminController::class, 'patientclrfitter'])->name('superadmin.patientclrfitter');
    Route::get('/superadmin/patientdateandfitter', [SuperAdminController::class, 'patientdateandfitter'])->name('superadmin.patientdateandfitter');
    Route::get('/superadmin/patientpop', [SuperAdminController::class, 'patientpop'])->name('superadmin.patientpop');
    Route::get('/superadmin/meetingpop', [SuperAdminController::class, 'meetingpop'])->name('superadmin.meetingpop');

    // document management controllers

    //vasanth
    Route::get('superadmin/get-marketers-by-zone', [SuperAdminController::class, 'getMarketersByZone']);
    Route::get('superadmin/get-all-marketers', [SuperAdminController::class, 'getAllMarketers']);
    Route::get('superadmin/get-marketers-by-zonal-head', [SuperAdminController::class, 'getMarketersByZonalHead']);

    Route::post('superadmin/upload-meeting-image', [SuperAdminController::class, 'uploadMeetingImage']);


    Route::get('superadmin/get-zonal-heads-by-zone', [SuperAdminController::class, 'getZonalHeadsByZone']);
    Route::get('superadmin/get-all-zonal-heads', [SuperAdminController::class, 'getAllZonalHeads']);

    Route::get('superadmin/get-all-zones', [SuperAdminController::class, 'getAllZones']);
    Route::get('superadmin/get-all-branches', [SuperAdminController::class, 'getAllBranches']);

    //vasanth -  Master Access
    Route::get('/superadmin/masteraccess',[SuperAdminController::class,'masteraccess'])->name('superadmin.masteraccess');
    Route::get('/superadmin/employee-data', [SuperAdminController::class, 'getEmployeeData'])->name('superadmin.employee-data');
    Route::post('/superadmin/update-user-status', [SuperAdminController::class, 'updateUserStatus'])->name('update.status');
    Route::get('/superadmin/get-menu-permissions', [SuperAdminController::class,'getMenuPermissions']);
    Route::post('/superadmin/save-permissions', [SuperAdminController::class,'savePermissions']);
     Route::get('/superadmin/get-user-details', [SuperAdminController::class, 'getMenuPermissions']);


    // User management
    Route::post('/superadmin/usermanagentadded', [SuperAdminController::class, 'usermanagentadded'])->name('superadmin.usermanagentadded');
    Route::get('/superadmin/userdetails', [SuperAdminController::class, 'userdetails'])->name('superadmin.userdetails');
    Route::post('/superadmin/campdetailsadded', [SuperAdminController::class, 'campdetailsadded'])->name('superadmin.campdetailsadded');
    Route::get('/superadmin/campalldetails', [SuperAdminController::class, 'campalldetails'])->name('superadmin.campalldetails');
    Route::get('/superadmin/campdateanddataftters', [SuperAdminController::class, 'campdateanddataftters'])->name('superadmin.campdateanddataftters');
    Route::get('/superadmin/campdatefitters', [SuperAdminController::class, 'campdatefitters'])->name('superadmin.campdatefitters');
    Route::get('/superadmin/campdateandsearchfitters', [SuperAdminController::class, 'campdateandsearchfitters'])->name('superadmin.campdateandsearchfitters');
    Route::post('/superadmin/activitesadddata', [SuperAdminController::class, 'activitesadddata'])->name('superadmin.activitesadddata');
    Route::get('/superadmin/activitesalldetails', [SuperAdminController::class, 'activitesalldetails'])->name('superadmin.activitesalldetails');
    Route::get('/superadmin/activitesdatefitters', [SuperAdminController::class, 'activitesdatefitters'])->name('superadmin.activitesdatefitters');
    Route::get('/superadmin/activitesdatanaddatefitters', [SuperAdminController::class, 'activitesdatanaddatefitters'])->name('superadmin.activitesdatanaddatefitters');
    Route::get('/superadmin/activitesdateandfittertexts', [SuperAdminController::class, 'activitesdateandfittertexts'])->name('superadmin.activitesdateandfittertexts');
    Route::post('/superadmin/expensesadddata', [SuperAdminController::class, 'expensesadddata'])->name('superadmin.expensesadddata');
    Route::get('/superadmin/expensesalldetails', [SuperAdminController::class, 'expensesalldetails'])->name('superadmin.expensesalldetails');
    Route::get('/superadmin/branchfetchviews', [SuperAdminController::class, 'branchfetchviews'])->name('superadmin.branchfetchviews');
    Route::get('/superadmin/zonefetchviews', [SuperAdminController::class, 'zonefetchviews'])->name('superadmin.zonefetchviews');
    Route::get('/superadmin/marketernamesurls', [SuperAdminController::class, 'marketernamesurls'])->name('superadmin.marketernamesurls');
    Route::get('/superadmin/incomefetchdetails', [SuperAdminController::class, 'incomefetchdetails'])->name('superadmin.incomefetchdetails');
    Route::get('/superadmin/incomeupdatedetails', [SuperAdminController::class, 'incomeupdatedetails'])->name('superadmin.incomeupdatedetails');
    Route::get('/superadmin/billalldetails', [SuperAdminController::class, 'billalldetails'])->name('superadmin.billalldetails');
    Route::get('superadmin/bill_overall_list', [SuperAdminController::class, 'bill_overall_list'])->name('superadmin.bill_overall_list');
    Route::get('superadmin/bill_overall_list_get', [SuperAdminController::class, 'bill_overall_list_get'])->name('superadmin.bill_overall_list_get');
    Route::get('superadmin/patientdashboard', [SuperAdminController::class, 'patientdashboard'])->name('superadmin.patientdashboard');
    Route::get('superadmin/campactivitespopuop', [SuperAdminController::class, 'campactivitespopuop'])->name('superadmin.campactivitespopuop');
    Route::get('superadmin/campexpensivepopuop', [SuperAdminController::class, 'campexpensivepopuop'])->name('superadmin.campexpensivepopuop');
    Route::get('superadmin/campfetchurlfitters', [SuperAdminController::class, 'campfetchurlfitters'])->name('superadmin.campfetchurlfitters');

    // Route::get('superadmin/Daily_summary', [SuperAdminController::class, 'Daily_summary'])->name('superadmin.Daily_summary');
    // Route::get('superadmin/daily_summary_views', [SuperAdminController::class, 'daily_summary_views'])->name('superadmin.daily_summary_views');

//Vehicle management
Route::get('/superadmin/vehicle', [SuperAdminController::class, 'getVehicle'])->name('superadmin.vehicle');
Route::get('/superadmin/vehiclefetch', [SuperAdminController::class, 'vehicleDetails'])->name('superadmin.vehiclefetch');
Route::get('/superadmin/vehicledocument', [SuperAdminController::class, 'vehicleDocumentDetails'])->name('superadmin.vehicledocument');
Route::post('/superadmin/vehicleadded', [SuperAdminController::class, 'vehicleAdded'])->name('superadmin.vehicleadded');
Route::post('/superadmin/vehicleupdate', [SuperAdminController::class, 'vehicleUpdate'])->name('superadmin.vehicleupdate');
Route::post('/superadmin/vehicledocumentupdate', [SuperAdminController::class, 'vehicleDocumentUpdate'])->name('superadmin.vehicledocumentupdate');
Route::get('/superadmin/vehicledocumentfilter', [SuperAdminController::class, 'vehicleDocumentFilter'])->name('superadmin.vehicledocumentfilter');
Route::get('/superadmin/vehicledocumentUrlfilter', [SuperAdminController::class, 'vehicleDocumentUrlFilter'])->name('superadmin.vehicledocumentUrlfilter');
Route::get('/superadmin/vehiclemoredocfilter', [SuperAdminController::class, 'vehicleDocumentMoreFilter'])->name('superadmin.vehiclemoredocfilter');
Route::get('/superadmin/vehiclemorefilter', [SuperAdminController::class, 'vehicleMoreFilter'])->name('superadmin.vehiclemorefilter');
Route::get('/superadmin/vehicledatefillter', [SuperAdminController::class, 'vehicleDateFillter'])->name('superadmin.vehicledatefillter');

Route::get('superadmin/vehicledocumentedit', [SuperAdminController::class, 'vehicleDocumentEdit'])->name('superadmin.vehicledocumentedit');
Route::get('superadmin/fetchUrlmorefittervechicle', [SuperAdminController::class, 'fetchUrlmorefittervechicle'])->name('superadmin.fetchUrlmorefittervechicle');
Route::get('/superadmin/vehicleInsuranceDocument', [SuperAdminController::class, 'vehicleInsuranceDocument'])->name('superadmin.vehicleInsuranceDocument');
Route::get('/superadmin/vehicledocumentview', [SuperAdminController::class, 'vehicleDocumentView'])->name('superadmin.vehicledocumentview');
Route::get('/vehicle_activity_details/{id}', [SuperAdminController::class, 'vehicleActivityDetails']);
Route::post('/superadmin/add_insurance_existing', [SuperAdminController::class, 'addInsuranceExisting'])->name('superadmin.add_insurance_existing');


//Travel Booking
Route::get('/superadmin/travel', [SuperAdminController::class, 'gettravel'])->name('superadmin.travel');
Route::get('/superadmin/travelfetch', [SuperAdminController::class, 'travelDetails'])->name('superadmin.travelfetch');
Route::post('/superadmin/traveladd', [SuperAdminController::class, 'travelAdd'])->name('superadmin.traveladd');
Route::post('/superadmin/travelupdate', [SuperAdminController::class, 'travelUpdate'])->name('superadmin.travelupdate');
Route::get('/superadmin/traveledit', [SuperAdminController::class, 'travelEdit'])->name('superadmin.traveledit');
Route::get('/superadmin/travelfilter', [SuperAdminController::class, 'travelFilter'])->name('superadmin.travelfilter');

Route::post('/storeManagemenetImage',[SuperAdminController::class,'storeManagemenetImage']);
Route::get('/superadmin/ticket', [SuperAdminController::class, 'getTicket'])->name('superadmin.ticket');
Route::get('/superadmin/ticketmaster', [SuperAdminController::class, 'getTicketMaster'])->name('superadmin.getTicketMaster');
Route::get('/superadmin/ticketActivity/{id}',[SuperAdminController::class,'ticketActivity'])->name('superadmin.ticketActivity');
Route::get('/superadmin/ticketfetch', [SuperAdminController::class, 'ticketFetch'])->name('superadmin.ticketfetch');
Route::get('/superadmin/myticketfetch', [SuperAdminController::class, 'myTicketFetch'])->name('superadmin.myticketfetch');
Route::get('/superadmin/allticketfetch', [SuperAdminController::class, 'allTicketFetch'])->name('superadmin.allticketfetch');
Route::post('/superadmin/manageReplyActivity',[SuperAdminController::class,'manageReplyActivity'])->name('superadmin.manageReplyActivity');
Route::post('/superadmin/update-ticket-status',[SuperAdminController::class,'updateStatus'])->name('superadmin.updateStatus');


Route::get('/superadmin/ticketfillter', [SuperAdminController::class, 'ticketFillter'])->name('superadmin.ticketfillter');
Route::get('/superadmin/allticketfillter', [SuperAdminController::class, 'allticketFillter'])->name('superadmin.allticketfillter');
Route::get('/superadmin/myticketfillter', [SuperAdminController::class, 'myticketFillter'])->name('superadmin.myticketfillter');
Route::get('/superadmin/ticketdatefillter', [SuperAdminController::class, 'ticketDateFillter'])->name('superadmin.ticketdatefillter');
Route::get('/superadmin/allticketdatefillter', [SuperAdminController::class, 'allticketDateFillter'])->name('superadmin.allticketdatefillter');
Route::get('/superadmin/myticketdatefillter', [SuperAdminController::class, 'myticketDateFillter'])->name('superadmin.myticketdatefillter');
Route::post('/superadmin/ticketadded', [SuperAdminController::class, 'ticketAdded'])->name('superadmin.ticketadded');
Route::post('/superadmin/getSubcategory',[SuperAdminController::class,'subDepartmentBasedId'])->name('superadmin.department');

// routes/web.php
Route::get('/superadmin/income_reconciliation_data', [IncomeReconciliationController::class, 'index'])->name('superadmin.income_reconciliation');
Route::post('/superadmin/store', [IncomeController::class, 'storeRadiant'])->name('superadmin.incomestore');
Route::get('/superadmin/radiantfetch', [IncomeController::class, 'fetchRadiant'])->name('superadmin.incomeradiantfetch');
Route::post('/radiant/upload-file', [IncomeController::class, 'uploadFile'])->name('superadmin.incomeuploadFile');
Route::post('/income/recon/check', [IncomeReconciliationController::class,'checkDate'])->name('superadmin.recon.check');
Route::post('/income/verify', [IncomeController::class, 'verify'])->name('income.verify');

//overview_income
Route::get('/superadmin/income_reconciliation_overview', [IncomeReconciliationController::class, 'overviewindex'])->name('superadmin.overviewindex');
Route::get('/superadmin/income_reconciliation_overview_data', [IncomeReconciliationController::class, 'overviewdata'])->name('superadmin.overviewdata');
Route::get('/superadmin/incomeoverviewdatefilter', [IncomeReconciliationController::class, 'incomeOverviewDateFilter'])->name('superadmin.incomeOverviewDateFilter');
Route::get('/income_reconciliation_overview/download', [IncomeReconciliationController::class, 'downloadIncomeRconciliation'])->name('income.downloadIncomeRconciliation');
Route::get('/superadmin/Income-download-template', [IncomeReconciliationController::class, 'Incometemplate'])->name('superadmin.Incometemplate');
Route::post('/superadmin/import-income', [IncomeReconciliationController::class, 'importIncomeExcel'])->name('import.importIncomeExcel');

// new income
Route::get('/superadmin/income_reconciliation_overview_new', [IncomeReconciliationController::class, 'overviewindexnew'])->name('superadmin.overviewindexnew');
Route::get('/superadmin/income_reconciliation_data_new', [IncomeReconciliationController::class, 'indexNew'])->name('superadmin.indexNew');
Route::get('/income_reconciliation_overview_new/download', [IncomeReconciliationController::class, 'downloadIncomeRconciliationNew'])->name('income.downloadIncomeRconciliationNew');
//income reconciliation branch
Route::get('/superadmin/income_reconciliation_branch', [IncomeReconciliationController::class, 'indexBranch'])->name('superadmin.indexBranch');

//montly repoer
Route::get('/superadmin/income_montly_report', [IncomeReconciliationController::class, 'IncomeMontlyReport'])->name('superadmin.IncomeMontlyReport');
Route::get('/superadmin/income_montly_report_data', [IncomeReconciliationController::class, 'IncomeMontlyReportData'])->name('superadmin.IncomeMontlyReportData');
Route::get('/superadmin/incomemonthlydatefilter', [IncomeReconciliationController::class, 'incomeMonthlyDateFilter'])->name('superadmin.incomeMonthlyDateFilter');
Route::get('/income_montly_report/download', [IncomeReconciliationController::class, 'downloadincome_montly_report'])->name('income.downloadincome_montly_report');


//santh
Route::get('/superadmin/myticketmasterfetch', [SuperAdminController::class, 'myTicketMasterFetch'])->name('superadmin.myTicketMasterFetch');
Route::post('/superadmin/ticketMasteradded', [SuperAdminController::class, 'ticketMasterAdded'])->name('superadmin.ticketMasterAdded');
Route::get('/superadmin/fetchmyticketmasterfitter', [SuperAdminController::class, 'fetchmyticketmasterfitter'])->name('superadmin.fetchmyticketmasterfitter');
Route::get('/superadmin/myticketmasterdate', [SuperAdminController::class, 'myticketmasterdate'])->name('superadmin.myticketmasterdate');
Route::post('/superadmin/ticket/chat/send', [SuperAdminController::class, 'sendMessage'])->name('chat.send');
Route::get('/superadmin/ticket/chat/history/{ticket_id}', [SuperAdminController::class, 'getHistory'])->name('chat.history');


Route::post('/superadmin/storeImage',[SuperAdminController::class,'storeImage']);
Route::get('/superadmin/fetchticketfitterremove', [SuperAdminController::class, 'fetchticketfitterremove'])->name('superadmin.fetchticketfitterremove');
Route::get('/superadmin/fetchallfitterremove', [SuperAdminController::class, 'fetchallfitterremove'])->name('superadmin.fetchallfitterremove');
Route::get('/superadmin/fetchmyticketfitterremove', [SuperAdminController::class, 'fetchmyticketfitterremove'])->name('superadmin.fetchmyticketfitterremove');
Route::get('/superadmin/fetchticketfitter', [SuperAdminController::class, 'fetchticketfitter'])->name('superadmin.fetchticketfitter');
Route::get('/superadmin/fetchmyticketfitter', [SuperAdminController::class, 'fetchmyticketfitter'])->name('superadmin.fetchmyticketfitter');
Route::get('/superadmin/fetchallticketfitter', [SuperAdminController::class, 'fetchallticketfitter'])->name('superadmin.fetchallticketfitter');

Route::get('/superadmin/dailysummary', [SuperAdminController::class, 'dailySummary'])->name('superadmin.dailysummary');
Route::get('/superadmin/dailydatefilter', [SuperAdminController::class, 'dailyDateFilter'])->name('superadmin.dailydatefilter');
Route::get('/superadmin/dailybranchfilter', [SuperAdminController::class, 'dailyBranchFilter'])->name('superadmin.dailybranchfilter');
Route::get('/superadmin/dailysummaryfetch', [SuperAdminController::class, 'dailySummaryDetails'])->name('superadmin.dailysummaryfetch');
Route::get('/superadmin/dailysummaryapi', [SuperAdminController::class, 'dailySummaryAPI']);
Route::get('/superadmin/registrationreport', [SuperAdminController::class,'registrationReport'])->name('superadmin.registrationreport');
Route::get('/superadmin/registrationfetch', [SuperAdminController::class, 'registrationFetch'])->name('superadmin.registrationfetch');
Route::get('/superadmin/checkin', [SuperAdminController::class, 'checkIn'])->name('superadmin.checkin');

 // document management controllers
 Route::post('/superadmin/documentadded', [SuperAdminController::class, 'documentadded'])->name('superadmin.documentadded');
 Route::get('/superadmin/fetchdocument', [SuperAdminController::class, 'fetchdocument'])->name('superadmin.fetchdocument');
 Route::POST('/superadmin/documentupdated', [SuperAdminController::class, 'documentupdated'])->name('superadmin.documentupdated');
 Route::get('/superadmin/branchurls', [SuperAdminController::class, 'branchurls'])->name('superadmin.branchurls');
 Route::get('/superadmin/menuaccessurl', [SuperAdminController::class, 'menuaccessurl'])->name('superadmin.menuaccessurl');

//document
Route::get('/superadmin/admindaily_document',[SuperAdminController::class, 'adminDailyDocument'])->name('superadmin.admindaily_document');
Route::post('/superadmin/admindaily_documentadded', [SuperAdminController::class, 'admindaily_documentadded'])->name('superadmin.admindaily_documentadded');
Route::get('/superadmin/admin_fetchdocument',[SuperAdminController::class,'admin_fetchdocument'])->name('superadmin.admin_fetchdocument');
Route::get('/superadmin/discountform_document',[SuperAdminController::class, 'discountDocument'])->name('superadmin.discountform_document');
Route::post('/superadmin/discount_documentadded', [SuperAdminController::class, 'discount_documentadded'])->name('superadmin.discount_documentadded');
Route::get('/superadmin/discount_fetchdocument',[SuperAdminController::class,'discount_fetchdocument'])->name('superadmin.discount_fetchdocument');
Route::get('/superadmin/general_document',[SuperAdminController::class, 'generalDocument'])->name('superadmin.general_document');
Route::post('/superadmin/general_documentadded', [SuperAdminController::class, 'general_documentadded'])->name('superadmin.general_documentadded');
Route::get('/superadmin/general_fetchdocument',[SuperAdminController::class,'general_fetchdocument'])->name('superadmin.general_fetchdocument');

//new discount
Route::get('/superadmin/discountform_document_new',[SuperAdminController::class, 'discountDocumentNew'])->name('superadmin.discountDocumentNew');


//security
Route::get('/superadmin/securitydaily_document',[SuperAdminController::class, 'securityDailyDocument'])->name('superadmin.securitydaily_document');
Route::post('/superadmin/securitydaily_documentadded', [SuperAdminController::class, 'security_documentadded'])->name('superadmin.securitydaily_documentadded');
Route::get('/superadmin/security_fetchdocument',[SuperAdminController::class,'security_fetchdocument'])->name('superadmin.security_fetchdocument');
Route::get('/superadmin/security_detials_edit', [SuperAdminController::class, 'securityEdit'])->name('superadmin.security_detials_edit');
Route::post('/superadmin/edit_security',[SuperAdminController::class,'edit_security_data'])->name('superadmin.edit_security');
Route::post('/superadmin/security_detials_delete', [SuperAdminController::class,'delete_security_data'])->name('superadmin.security_detials_delete');
Route::get('/superadmin/securityreport', [SuperAdminController::class,'securityfillterreport'])->name('superadmin.securityreport');
Route::get('/superadmin/securityDetailsdata', [SuperAdminController::class,'securityDetails'])->name('superadmin.securityDetailsdata');
Route::get('/superadmin/securityshiftdata', [SuperAdminController::class, 'securityshiftdata'])->name('superadmin.securityshiftdata');

//attendance
Route::get('/superadmin/dailyattendance_document',[SuperAdminController::class, 'dailyAttendanceDocument'])->name('superadmin.dailyattendance_document');
Route::post('/superadmin/dailyattendance_documentadd', [SuperAdminController::class, 'attendance_documentadd'])->name('superadmin.dailyattendance_documentadd');
Route::get('/superadmin/attendance_fetchdocument',[SuperAdminController::class,'attendance_fetchdocument'])->name('superadmin.attendance_fetchdocument');
Route::get('/superadmin/attendance_detials',[SuperAdminController::class,'attendance_detials'])->name('superadmin.attendance_detials');
Route::get('/superadmin/attendancedatefilter',[SuperAdminController::class,'attendancedatefilter'])->name('superadmin.attendancedatefilter');
Route::get('/superadmin/att_detials_edit',[SuperAdminController::class,'att_detials_edit'])->name('superadmin.att_detials_edit');;
Route::post('/superadmin/edit_attendance',[SuperAdminController::class,'edit_attendance'])->name('superadmin.edit_attendance');
Route::post('/superadmin/attendance_delete',[SuperAdminController::class,'attendance_delete'])->name('superadmin.attendance_delete');

//license
Route::get('/superadmin/license_document',[SuperAdminController::class,'licenseDocument'])->name('superadmin.license_document');
Route::post('/superadmin/license_documentadded', [SuperAdminController::class, 'license_documentadded'])->name('superadmin.license_documentadded');
Route::get('/superadmin/license_fetchdocument',[SuperAdminController::class,'license_fetchdocument'])->name('superadmin.license_fetchdocument');
Route::get('/superadmin/licensedoc_detials',[SuperAdminController::class,'licensedoc_detials'])->name('superadmin.licensedoc_detials');
Route::get('/superadmin/licexpdatefilter',[SuperAdminController::class,'licexpdatefilter'])->name('superadmin.licexpdatefilter');

Route::post('/superadmin/doctypename',[SuperAdminController::class, 'doctypename'])->name('superadmin.doctypename');
Route::get('/superadmin/getdocnametype', [SuperAdminController::class, 'getdocnametype'])->name('superadmin.getdocnametype');

Route::get('/superadmin/registrationview',[SuperAdminController::class,'registrationView'])->name('superadmin.registrationview');
Route::get('/superadmin/registrationfetchbranch', [SuperAdminController::class, 'registrationFetchBranch'])->name('superadmin.registrationfetchbranch');
Route::post('/superadmin/dateaddedinsetedviews',[SuperAdminController::class,'dateaddedinsetedviews'])->name('superadmin.dateaddedinsetedviews');

	Route::get('/superadmin/checkinurlapi', [SuperAdminController::class, 'checkinUrlApi'])->name('superadmin.checkinurlapi');
	Route::get('/superadmin/checkinreportfetch', [SuperAdminController::class, 'checkinfetchDetails'])->name('superadmin.checkinreportfetch');
	Route::get('/superadmin/checkin', [SuperAdminController::class, 'checkIn'])->name('superadmin.checkin');
	Route::get('/superadmin/checkinreportfilter', [SuperAdminController::class, 'checkInReportFilter'])->name('superadmin.checkinreportfilter');
	Route::get('/superadmin/checkindatefilter', [SuperAdminController::class, 'checkInDateFilter'])->name('superadmin.checkindatefilter');
	Route::get('/superadmin/checkinbranchfilter', [SuperAdminController::class, 'checkInBranchFilter'])->name('superadmin.checkinbranchfilter');
	Route::post('/superadmin/checkinupdate', [SuperAdminController::class, 'checkInReportUpdate'])->name('superadmin.checkinupdate');
	Route::get('/superadmin/checkinreportedit', [SuperAdminController::class, 'checkInReportEdit'])->name('superadmin.checkinreportedit');
	Route::get('/superadmin/checkintimeline', [SuperAdminController::class, 'checkInTimeLine'])->name('superadmin.checkintimeline');
	Route::get('/superadmin/checkinlastfetch', [SuperAdminController::class, 'checkinLastNextFetch'])->name('superadmin.checkinlastfetch');
	Route::get('/superadmin/checkinlastdatefiltr', [SuperAdminController::class, 'checkinLastDateFetch'])->name('superadmin.checkinlastdatefiltr');
	Route::get('/superadmin/checkinnextdatefiltr', [SuperAdminController::class, 'checkinNextDateFetch'])->name('superadmin.checkinnextdatefiltr');
	//Route::get('/superadmin/checkinfinacialamt', [SuperAdminController::class, 'checkinFinacialAmt'])->name('superadmin.checkinfinacialamt');
	Route::post('/superadmin/checkintreatmentamt', [SuperAdminController::class, 'checkinTreatmentAmt'])->name('superadmin.checkintreatmentamt');
    Route::get('/superadmin/checkinccname', [SuperAdminController::class, 'checkinCCName'])->name('superadmin.checkinccname');

Route::get('/superadmin/incomereportfetch', [SuperAdminController::class, 'incomeReportFetch'])->name('superadmin.incomereportfetch');
Route::get('/superadmin/incomereportfilter', [SuperAdminController::class, 'incomeReportFilter'])->name('superadmin.incomereportfilter');
Route::get('/superadmin/incomedatefilter', [SuperAdminController::class, 'incomeDateFilter'])->name('superadmin.incomedatefilter');
Route::get('/superadmin/incomebranchfilter', [SuperAdminController::class, 'incomeBranchFilter'])->name('superadmin.incomebranchfilter');

//ART Module
Route::get('/superadmin/art', [SuperAdminController::class, 'getart'])->name('superadmin.art');
Route::get('/superadmin/embryology', [SuperAdminController::class, 'embryology'])->name('superadmin.embryology');
Route::get('/superadmin/embryo_transfer', [SuperAdminController::class, 'embryoTransfer'])->name('superadmin.embryo_transfer');
Route::get('/superadmin/egg_pickup', [SuperAdminController::class, 'eggPickup'])->name('superadmin.egg_pickup');
Route::get('/superadmin/embryo_freezing', [SuperAdminController::class, 'embryoFreezing'])->name('superadmin.embryo_freezing');
Route::get('/superadmin/investigation', [SuperAdminController::class, 'investigation'])->name('superadmin.investigation');
Route::post('/superadmin/store_investigation', [SuperAdminController::class, 'storeInvestigation'])->name('superadmin.store_investigation');
Route::get('/superadmin/ovarianstimulation', [SuperAdminController::class, 'ovarian_stimulation'])->name('superadmin.ovarianstimulation');
Route::get('/superadmin/outcome', [SuperAdminController::class, 'outcome'])->name('superadmin.outcome');
Route::get('/superadmin/profile', [SuperAdminController::class, 'profile'])->name('superadmin.profile');


Route::get('/superadmin/art_index', [SuperAdminController::class, 'artIndex'])->name('superadmin.art_index');

Route::get('/superadmin/profile_ivf', [SuperAdminController::class, 'profileIvf'])->name('superadmin.profile_ivf');
Route::get('/superadmin/ovarian_ivf', [SuperAdminController::class, 'ovarianIvf'])->name('superadmin.ovarian_ivf');
Route::get('/superadmin/eggpick_ivf', [SuperAdminController::class, 'eggpickIvf'])->name('superadmin.eggpick_ivf');
Route::get('/superadmin/embryology_ivf', [SuperAdminController::class, 'embryologyIvf'])->name('superadmin.embryology_ivf');
Route::get('/superadmin/embryology_freezind_ivf', [SuperAdminController::class, 'embryologyFreezindIvf'])->name('superadmin.embryology_freezind_ivf');
Route::get('/superadmin/embryology_transfer_ivf', [SuperAdminController::class, 'embryologyTransferIvf'])->name('superadmin.embryology_transfer_ivf');
Route::get('/superadmin/outcome_ivf', [SuperAdminController::class, 'outcomeIvf'])->name('superadmin.outcome_ivf');


//santh
	Route::get('/superadmin/sample', [SuperAdminController::class, 'getSample'])->name('superadmin.sample');
	Route::post('/superadmin/samplesave', [SuperAdminController::class, 'Samplesave'])->name('superadmin.Samplesave');

    //purchase
    Route::get('/superadmin/vendor', [SuperAdminController::class, 'getVendor'])->name('superadmin.getvendor');
    Route::post('/superadmin/vendorsave', [SuperAdminController::class, 'vendorsave'])->name('superadmin.vendorsave');
    Route::get('/superadmin/vendor_fetch', [SuperAdminController::class, 'vendorfetch'])->name('superadmin.vendorfetch');
    //purchase maker
    Route::get('/superadmin/purchase_maker', [SuperAdminController::class, 'getPurchaseMaker'])->name('superadmin.purchasemaker');
	Route::post('/neft-payment', [SuperAdminController::class, 'purchasesave'])->name('superadmin.purchasesave');
    Route::get('/superadmin/purchase_fetch', [SuperAdminController::class, 'purchasefetch'])->name('superadmin.purchasefetch');
    Route::GET('/superadmin/fetchmorefitterpurchase', [SuperAdminController::class, 'fetchmorefitterpurchase'])->name('superadmin.fetchmorefitterpurchase');
    Route::get('/superadmin/fetchmorefitterdateclrpurchase', [SuperAdminController::class, 'fetchmorefitterdateclrpurchase'])->name('superadmin.fetchmorefitterdateclrpurchase');
    Route::get('/superadmin/purchasefetchfitter', [SuperAdminController::class, 'purchasefetchfitter'])->name('superadmin.purchasefetchfitter');
    //purchase checker
    Route::get('/superadmin/purchase_checker', [SuperAdminController::class, 'getPurchaseChecker'])->name('superadmin.purchasechecker');
    Route::get('/superadmin/purchase_checker_fetch', [SuperAdminController::class, 'purchaseCheckerfetch'])->name('superadmin.purchaseCheckerfetch');
	Route::post('/payment_checker', [SuperAdminController::class, 'purchasecheckersave'])->name('superadmin.purchasecheckersave');
    // Route::GET('/superadmin/fetchmorefitterpurchasechecker', [SuperAdminController::class, 'filterpurchasecheker'])->name('superadmin.filterpurchasecheker');
    // Route::get('/superadmin/fitterdateclrpurchasechecker', [SuperAdminController::class, 'fitterdateclrpurchasechecker'])->name('superadmin.fitterdateclrpurchasechecker');
    // Route::get('/superadmin/purchasecheckerfetchfitter', [SuperAdminController::class, 'purchasecheckerfetchfitter'])->name('superadmin.purchasecheckerfetchfitter');

    // purchase apporver
    Route::get('/superadmin/purchase_approver', [SuperAdminController::class, 'getPurchaseApprover'])->name('superadmin.purchaseapprover');
    Route::get('/superadmin/purchase_approver_fetch', [SuperAdminController::class, 'purchaseapproverfetch'])->name('superadmin.purchaseApproverfetch');
	Route::post('/payment_approver', [SuperAdminController::class, 'purchaseapproversave'])->name('superadmin.purchaseapproversave');


//vasanth
    Route::get('superadmin/get-marketers-by-zone', [SuperAdminController::class, 'getMarketersByZone']);
    Route::get('superadmin/get-all-marketers', [SuperAdminController::class, 'getAllMarketers']);

// ART - ODICSI //
Route::get('/superadmin/profile_odicsi', [SuperAdminController::class, 'profileOdicsi'])->name('superadmin.profile_odicsi');
Route::get('/superadmin/ovarian_odicsi', [SuperAdminController::class, 'ovarianOdicsi'])->name('superadmin.ovarian_odicsi');
Route::get('/superadmin/eggpick_odicsi', [SuperAdminController::class, 'eggpickOdicsi'])->name('superadmin.eggpick_odicsi');
Route::get('/superadmin/embryology_odicsi', [SuperAdminController::class, 'embryologyOdicsi'])->name('superadmin.embryology_odicsi');
Route::get('/superadmin/embryology_freezind_odicsi', [SuperAdminController::class, 'embryologyFreezindOdicsi'])->name('superadmin.embryology_freezind_odicsi');
Route::get('/superadmin/embryology_transfer_odicsi', [SuperAdminController::class, 'embryologyTransferOdicsi'])->name('superadmin.embryology_transfer_odicsi');
Route::get('/superadmin/outcome_odicsi', [SuperAdminController::class, 'outcomeOdicsi'])->name('superadmin.outcome_odicsi');


// ART - EDICSI //
Route::get('/superadmin/profile_edicsi', [SuperAdminController::class, 'profileEdicsi'])->name('superadmin.profile_edicsi');
Route::get('/superadmin/ovarian_edicsi', [SuperAdminController::class, 'ovarianEdicsi'])->name('superadmin.ovarian_edicsi');
Route::get('/superadmin/eggpick_edicsi', [SuperAdminController::class, 'eggpickEdicsi'])->name('superadmin.eggpick_edicsi');
Route::get('/superadmin/embryology_edicsi', [SuperAdminController::class, 'embryologyEdicsi'])->name('superadmin.embryology_edicsi');
Route::get('/superadmin/embryology_freezind_edicsi', [SuperAdminController::class, 'embryologyFreezindEdicsi'])->name('superadmin.embryology_freezind_edicsi');
Route::get('/superadmin/embryology_transfer_edicsi', [SuperAdminController::class, 'embryologyTransferEdicsi'])->name('superadmin.embryology_transfer_edicsi');
Route::get('/superadmin/outcome_edicsi', [SuperAdminController::class, 'outcomeEdicsi'])->name('superadmin.outcome_edicsi');

Route::get('superadmin/discountformdoc_detials', [SuperAdminController::class, 'discountform_detials'])->name('superadmin.discountformdoc_detials');
Route::get('superadmin/discountform_edit', [SuperAdminController::class, 'discountform_edit'])->name('superadmin.discountform_edit');
Route::post('superadmin/discountformeditsave',[SuperAdminController::class, 'discountformeditsave'])->name('superadmin.discountformeditsave');
Route::get('superadmin/discountmrdno',[SuperAdminController::class, 'mrdnoapiurl'])->name('superadmin.discountmrdno');
Route::get('/superadmin/mrd-start', [SuperAdminController::class, 'startMrdProcessing'])->name('superadmin.mrd-start');
Route::get('/superadmin/mrd-progress', [SuperAdminController::class, 'getProgress'])->name('superadmin.mrd-progress');
Route::get('/superadmin/mrd-final', [SuperAdminController::class, 'getMrdFinalResult'])->name('superadmin.mrd-final');
Route::post('/superadmin/discount_datatdadded',[SuperAdminController::class, 'discount_datatdadded'])->name('superadmin.discount_datatdadded');
Route::post('superadmin/insertexpenses', [SuperAdminController::class, 'storeInline'])->name('superadmin.insertexpenses');
Route::get('/superadmin/discountform_data', [SuperAdminController::class, 'discountform_data'])->name('superadmin.discountform_data');
Route::get('superadmin/disformsave_data',[SuperAdminController::class, 'disformsave_data'])->name('superadmin.disformsave_data');
Route::post('superadmin/discount/approve-reject',[SuperAdminController::class, 'discountapproveReject'])->name('superadmin.discount_approvereject');


Route::get('superadmin/refundform',[SuperAdminController::class, 'refundformDocument'])->name('superadmin.refundform');
Route::post('/superadmin/refund_documentadded', [SuperAdminController::class, 'refund_documentadded'])->name('superadmin.refund_documentadded');
Route::get('superadmin/refundformdoc_detials', [SuperAdminController::class, 'refundformapi_detials'])->name('superadmin.refundformdoc_detials');
Route::get('superadmin/refundform_edit', [SuperAdminController::class, 'refundform_edit'])->name('superadmin.refundform_edit');
Route::post('superadmin/refundformeditsave',[SuperAdminController::class, 'refundformeditsave'])->name('superadmin.refundformeditsave');
Route::post('superadmin/refundtdsave',[SuperAdminController::class, 'refundtdsave'])->name('superadmin.refundtdsave');
Route::get('superadmin/refundform_data', [SuperAdminController::class, 'refundform_data'])->name('superadmin.refundform_data');

Route::get('superadmin/cancelbillform',[SuperAdminController::class,'cancelbillform'])->name('superadmin.cancelbillform');
Route::post('superadmin/cancelbill_added', [SuperAdminController::class,'cancelbilladd'])->name('superadmin.cancelbill_added');
Route::get('superadmin/cancelform_data',[SuperAdminController::class, 'cancelbillform_data'])->name('superadmin.cancelform_data');
Route::get('superadmin/cancelformsave_data',[SuperAdminController::class, 'cancelformsave_data'])->name('superadmin.cancelformsave_data');
Route::get('superadmin/cancelbill_data',[SuperAdminController::class, 'cancelbill_data'])->name('superadmin.cancelbill_data');
Route::get('superadmin/cancelsave_data', [SuperAdminController::class, 'cancelsave_data'])->name('superadmin.cancelsave_data');
Route::post('superadmin/cancel-bill/approve-reject',[SuperAdminController::class, 'approveReject'])->name('superadmin.approve_reject');
//cancelbill new
Route::get('superadmin/cancelbill-dashboard',[SuperAdminController::class,'cancelbill_dashboard'])->name('superadmin.cancelbill_dashboard');
//new refund dashboard
Route::get('superadmin/refundbill-dashboard', [SuperAdminController::class, 'refundbill_dashboard'])->name('superadmin.refundbill_dashboard');
Route::post('superadmin/refund-bill/approve-reject', [SuperAdminController::class, 'refundbill_approval'])->name('superadmin.refundbill_approve_reject');

// ── Activity Logs ──────────────────────────────────────────────────────────
Route::get('/superadmin/logs',          [LogController::class, 'index'])->name('superadmin.logs');
Route::get('/superadmin/logs-data',     [LogController::class, 'getData'])->name('superadmin.logs_data');
Route::get('/superadmin/logs-stats',    [LogController::class, 'stats'])->name('superadmin.logs_stats');
Route::post('/superadmin/logs-store',   [LogController::class, 'store'])->name('superadmin.logs_store');
Route::post('/superadmin/logs-clear',   [LogController::class, 'clear'])->name('superadmin.logs_clear');

//camp lead
 Route::post('superadmin/leadsadddata', [SuperAdminController::class, 'leadsadddata'])->name('superadmin.leadsadddata');
 Route::get('/superadmin/leadsdata', [SuperAdminController::class, 'leadsfilter'])->name('superadmin.leadsdata');
//camp activity
Route::post('superadmin/activitedatasave',[SuperAdminController::class, 'activitedatasave'])->name('superadmin.activitedatasave');
Route::get('superadmin/activitydata', [SuperAdminController::class, 'activitydatafilter'])->name('superadmin.activitydata');

    Route::post('/superadmin/staffReplyActivity',[SuperAdminController::class,'staffReplyActivity'])->name('superadmin.staffReplyActivity');
	Route::post('/superadmin/storeAdminImage',[SuperAdminController::class,'storeAdminImage']);
	Route::get('/superadmin/adminApproveActivity/{id}',[SuperAdminController::class,'adminApproveActivity']);
	Route::post('/superadmin/adminReplyActivity',[SuperAdminController::class,'adminReplyActivity'])->name('superadmin.adminReplyActivity');


    // new vendor modal
    //customer
    Route::get('/superadmin/customer', [VendorController::class, 'getcustomer'])->name('superadmin.getcustomer');
    Route::get('/superadmin/customer_create', [VendorController::class, 'getcustomercreate'])->name('superadmin.getcustomercreate');
    Route::post('/save-customer', [VendorController::class, 'saveCustomer'])->name('superadmin.savecustomer');
    //vendor
    Route::get('/superadmin/vendor', [VendorController::class, 'getvendor'])->name('superadmin.getvendor');
    Route::get('/superadmin/vendor_create', [VendorController::class, 'getvendorcreate'])->name('superadmin.getvendorcreate');
    Route::post('/save-vendor', [VendorController::class, 'savevendor'])->name('superadmin.savevendor');
    Route::get('/vendor-download-template', [VendorController::class, 'vendortemplate'])->name('superadmin.vendortemplate');
    Route::post('/import-vendor', [VendorController::class, 'importvendorExcel'])->name('import.vendor');
    Route::get('/export/vendor', [VendorController::class, 'exportvendor'])->name('superadmin.exportvendor');
    Route::get('/superadmin/transcationvendor', [VendorController::class, 'gettranscationvendor'])->name('superadmin.gettranscationvendor');
    Route::get('/superadmin/gettranscationvendorpagination', [VendorController::class, 'gettranscationvendorpagination'])->name('superadmin.gettranscationvendorpagination');
    Route::get('/superadmin/vendorchart', [VendorController::class, 'getvendorchart'])->name('superadmin.getvendorchart');
    Route::get('/vendor/statement', [VendorController::class, 'showStatement'])->name('vendor.showStatement');
    Route::get('/superadmin/statementprint/{id}', [VendorController::class, 'statementprint'])->name('superadmin.statementprint');
    Route::delete('/superadmin/vendor/delete', [VendorController::class, 'vendordelete'])->name('superadmin.vendor.vendordelete');
    Route::post('/superadmin/vendor/toggle-status', [VendorController::class, 'toggleVendorStatus'])->name('superadmin.vendor.togglestatus');


    //vendor report
    Route::get('/superadmin/reports', [VendorController::class, 'reportindex'])->name('superadmin.reportindex');
    Route::get('/superadmin/reports/details/{type}', [VendorController::class, 'showExpenseDetails'])->name('superadmin.reportdetails');
    Route::get('/superadmin/vendor-monthly-summary', [VendorController::class, 'getVendorMonthlySummary'])->name('superadmin.vendorMonthlySummary');
    Route::get('/superadmin/getAllCharts', [VendorController::class, 'getAllCharts'])->name('superadmin.getAllCharts');
    Route::get('/superadmin/reports/zone-chart', [VendorController::class, 'getZonePaymentChart'])->name('superadmin.zonePaymentChart');
    Route::get('/superadmin/reports/zone-chart-data', [VendorController::class, 'getZonePaymentChartData'])->name('superadmin.zonePaymentChartData');

    //vendor summary
    Route::get('/superadmin/vendor-summary', [VendorController::class, 'vendorSummary'])->name('superadmin.vendorSummary');
    Route::get('/report/monthly-income', [VendorController::class, 'monthlyIncome']);
    Route::get('/report/monthly-income-expense', [VendorController::class, 'monthlyIncomeExpense']);
    Route::get('/chart/income-payment-type', [VendorController::class, 'incomeByPaymentType']);

    // Bill
    Route::get('/superadmin/bill_dashboard', [VendorController::class, 'getbill'])->name('superadmin.getbill');
    Route::get('/superadmin/bill_create', [VendorController::class, 'getbillcreate'])->name('superadmin.getbillcreate');
    Route::post('/save-bill', [VendorController::class, 'savebill'])->name('superadmin.savebill');
    Route::get('/superadmin/bill_print', [VendorController::class, 'getbillprint'])->name('superadmin.getbillprint');
    Route::get('/superadmin/bill_pdf', [VendorController::class, 'getbillpdf'])->name('superadmin.getbillpdf');
    Route::post('/superadmin/update-asset-status', [VendorController::class, 'AssetUpdateStatus'])->name('superadmin.asset_status');
    Route::post('/multiple-bill-print', [VendorController::class, 'printMultiple'])->name('superadmin.multipleBillPrint');
    Route::post('/superadmin/update-bill', [VendorController::class, 'billtdsupdate'])->name('superadmin.billtdsupdate');
    Route::get('/bill-download-template', [VendorController::class, 'billtemplate'])->name('superadmin.vendortemplate');
    Route::post('/import-bill', [VendorController::class, 'importbillExcel'])->name('import.importbillExcel');
    Route::get('/billexport/excel', [VendorController::class, 'exportBills'])->name('superadmin.exportBills');
    Route::get('/billexport/tds', [VendorController::class, 'exportBillsTds'])->name('superadmin.exportBillsTds');
    Route::get('/billexport/gst', [VendorController::class, 'exportBillsGst'])->name('superadmin.exportBillsGst');
    Route::get('/billexport/neft', [VendorController::class, 'exportBillsNeft'])->name('superadmin.exportBillsNeft');
    Route::get('/billexport/tds-original', [VendorController::class, 'exportBillsTdsOriginal'])->name('superadmin.exportBillsTdsOriginal');
    Route::post('/check-bill-number', [VendorController::class,'checkBillNumber'])->name('check.bill.number');
    //asset
    Route::get('/superadmin/asset_dashboard', [VendorController::class, 'getasset'])->name('superadmin.getasset');

    //bill made
    Route::get('/superadmin/bill_made_dashboard', [VendorController::class, 'getbillmade'])->name('superadmin.getbillmade');
    Route::get('/superadmin/bill_made_create', [VendorController::class, 'getbillmadecreate'])->name('superadmin.getbillmadecreate');
    Route::post('/superadmin/get-vendor-details', [VendorController::class, 'getDetails']);
    Route::post('/save-bill_made', [VendorController::class, 'savebillmade'])->name('superadmin.savebillmade');
    Route::get('/superadmin/bill_made_print', [VendorController::class, 'getbillmadeprint'])->name('superadmin.getbillmadeprint');
    Route::get('/superadmin/bill_made_pdf', [VendorController::class, 'getbillmadepdf'])->name('superadmin.getbillmadepdf');
    Route::get('/billMade-download-template', [VendorController::class, 'billMadetemplate'])->name('superadmin.vendortemplate');
    Route::post('/import-billmade', [VendorController::class, 'importbillMadeExcel'])->name('import.importbillMadeExcel');

    //quotation
    Route::get('/superadmin/quotation_dashboard', [VendorController::class, 'getquotation'])->name('superadmin.getquotation');
    Route::get('/superadmin/quotation_create', [VendorController::class, 'getquotationcreate'])->name('superadmin.getquotationcreate');
    Route::post('/save-quotation', [VendorController::class, 'savequotation'])->name('superadmin.savequotation');
	Route::GET('/quotation_approver', [VendorController::class, 'QuotationApprover'])->name('superadmin.QuotationApprover');
    Route::POST('/superadmin/quotation_fetch', [VendorController::class, 'getquotationfetch'])->name('superadmin.getquotationfetch');
    Route::get('/superadmin/quotation_print', [VendorController::class, 'getquotationprint'])->name('superadmin.getquotationprint');
    Route::get('/superadmin/quotation_pdf', [VendorController::class, 'getquotationpdf'])->name('superadmin.getquotationpdf');

    Route::get('/download-quotation-template', function () {
            return Excel::download(new QuotationTemplateExport, 'quotation_template.xlsx');
        });
    Route::post('/import-quotation', [VendorController::class, 'importQuotationExcel'])->name('import.quotation');

    // purchase order
    Route::get('/superadmin/purchase_dashboard', [VendorController::class, 'getpurchaseorder'])->name('superadmin.getpurchaseorder');
    Route::get('/superadmin/purchase_order_create', [VendorController::class, 'getpurchasecreate'])->name('superadmin.getpurchasecreate');
    Route::post('/save-purchase', [VendorController::class, 'savepurchaseorder'])->name('superadmin.savepurchaseorder');
    Route::POST('/superadmin/purchase_fetch', [VendorController::class, 'getpurchasefetch'])->name('superadmin.getpurchasefetch');
	Route::GET('/purchase_approver', [VendorController::class, 'PurchaseApprover'])->name('superadmin.PurchaseApprover');
    Route::get('/superadmin/po_print', [VendorController::class, 'getpurchaseprint'])->name('superadmin.getpurchaseprint');
    Route::get('/superadmin/po_pdf', [VendorController::class, 'getpurchasepdf'])->name('superadmin.getpurchasepdf');
    Route::get('/purchase-download-template', [VendorController::class, 'purchasetemplate'])->name('superadmin.purchasetemplate');
    Route::post('/import-purchase', [VendorController::class, 'importpurchaseExcel'])->name('import.importpurchaseExcel');

    // neft
    Route::get('/superadmin/neft_dashboard', [VendorController::class, 'getneftdashboard'])->name('superadmin.getneftdashboard');
    Route::get('/superadmin/neft_create', [VendorController::class, 'getneftcreate'])->name('superadmin.getneftcreate');
    Route::post('/save-neft', [VendorController::class, 'saveneft'])->name('superadmin.saveneft');
	Route::GET('/checker_approver', [VendorController::class, 'CheckerAndApprover'])->name('superadmin.CheckerAndApprover');

    //tds tax
    Route::post('/superadmin/tds_save', [VendorController::class, 'gettdssave'])->name('superadmin.gettdssave');
    Route::post('/superadmin/tds_section_save', [VendorController::class, 'gettdssectionsave'])->name('superadmin.gettdssectionsave');
    Route::post('/superadmin/tcs_save', [VendorController::class, 'gettcssave'])->name('superadmin.gettcssave');
    Route::post('/superadmin/gst_save', [VendorController::class, 'getgstsave'])->name('superadmin.getgstsave');
    Route::post('/superadmin/nature_save', [VendorController::class, 'getnaturesave'])->name('superadmin.getnaturesave');
    Route::post('/superadmin/account_save', [VendorController::class, 'getaccountsave'])->name('superadmin.getaccountsave');
    Route::post('/superadmin/po_email_save', [VendorController::class, 'getpoemailsave'])->name('superadmin.getpoemailsave');
    Route::post('/superadmin/delivery_add_save', [VendorController::class, 'getdeliverysave'])->name('superadmin.getdeliverysave');

    //xlss and csv neft
    Route::get('/export/excel', [VendorController::class, 'exportUsers'])->name('superadmin.exportExcel');

    //tax pagination
    Route::get('/ajax/tds-taxes', [VendorController::class, 'getTdsTaxes'])->name('ajax.tds');
    Route::get('/ajax/gst-taxes', [VendorController::class, 'getGstTaxes'])->name('ajax.gst');
    Route::get('/ajax/purchase-orders', [VendorController::class, 'getPurchases'])->name('ajax.purchases');
    Route::get('/ajax/quotations', [VendorController::class, 'getQuotations'])->name('ajax.quotations');
    //
    Route::get('/superadmin/tdstax_dashboard', [VendorController::class, 'gettdsdashboard'])->name('superadmin.gettdsdashboard');
    Route::get('/superadmin/gsttax_dashboard', [VendorController::class, 'getgstdashboard'])->name('superadmin.getgstdashboard');
    Route::get('/superadmin/nature_payment_dashboard', [VendorController::class, 'getnaturedashboard'])->name('superadmin.getnaturedashboard');
    //address
    Route::get('/superadmin/address_dashboard', [VendorController::class, 'getaddressdashboard'])->name('superadmin.getaddressdashboard');
    //GRN
    Route::get('/superadmin/grn_dashboard', [VendorController::class, 'getgrndashboard'])->name('superadmin.getgrndashboard');
    Route::get('/superadmin/grn_convert', [VendorController::class, 'getgrnconvert'])->name('superadmin.getgrnconvert');
    Route::get('/superadmin/grn_create', [VendorController::class, 'getgrncreate'])->name('superadmin.getgrncreate');
    Route::post('/save-grn', [VendorController::class, 'savegrn'])->name('superadmin.savegrn');

    //branch fetch
    Route::POST('/superadmin/branch_fetch', [VendorController::class, 'getbranchfetch'])->name('superadmin.getbranchfetch');

    //compare
    Route::get('/ai/compare', [AiCompareController::class, 'page'])->name('ai.compare.page');
    Route::post('/ai/compare', [AiCompareController::class, 'run'])->name('ai.compare.run');

    // bank dashboard
    Route::get('/bank_dashboard', [VendorController::class, 'index'])->name('superadmin.dashboard');
    Route::post('/dashboard/approve/{id}', [VendorController::class, 'approve'])->name('dashboard.approve');
    Route::get('/dashboard/export', [VendorController::class, 'export'])->name('dashboard.export');
    // tds summary
    Route::get('/superadmin/tds_summary', [VendorController::class, 'gettdssummary'])->name('superadmin.gettdssummary');
    Route::get('/tds-summary/download', [VendorController::class, 'downloadTdsSummary'])->name('tds.summary.download');
    Route::get('/superadmin/tds_report', [VendorController::class, 'gettdsreport'])->name('superadmin.gettdsreport');
    Route::get('/tds-report/download', [VendorController::class, 'downloadTdsReport'])->name('tds.report.download');
    Route::get('/tds-report/fy-download', [VendorController::class, 'downloadFyExcel'])->name('tds.report.fy.download');

    // gst summary
    Route::get('/superadmin/gst_summary', [VendorController::class, 'getgstsummary'])->name('superadmin.getgstsummary');
    Route::get('/gst-summary/download', [VendorController::class, 'downloadGstSummary'])->name('gst.summary.download');

    //profession summary
    Route::get('/superadmin/profession_summary', [VendorController::class, 'getprofessionalsummary'])->name('superadmin.getprofessionalsummary');
    Route::get('/professional-summary/download', [VendorController::class, 'downloadprofessionalSummary'])->name('professional.summary.download');

    //company
    Route::get('/superadmin/company', [VendorController::class, 'getcompany'])->name('superadmin.getcompany');
    Route::post('/superadmin/company_save', [VendorController::class, 'getcompanysave'])->name('superadmin.getcompanysave');
    // vendor type
    Route::get('/superadmin/vendor_type', [VendorController::class, 'getvendortype'])->name('superadmin.getvendortype');
    Route::post('/superadmin/vendor_type_save', [VendorController::class, 'getvendortypesave'])->name('superadmin.getvendortypesave');
    //income
    Route::get('/superadmin/income', [VendorController::class, 'vendorincomeReport'])->name('superadmin.vendorincomeReport');
    Route::get('/income-summary/export', [VendorController::class, 'exportIncomeSummary'])->name('incomeSummary.export');
    Route::get('/income-summary/drilldown', [VendorController::class, 'vendorIncomeDrilldown'])->name('incomeSummary.drilldown');


    //Branch financial module
    Route::get('/branch-financial', [BranchFinancialController::class, 'index'])->name('branch-financial.index');

    // Store new report
    Route::post('/branch-financial/store', [BranchFinancialController::class, 'store'])->name('branch-financial.store');

    // Get single report for editing
    Route::get('/branch-financial/show/{id}', [BranchFinancialController::class, 'show'])->name('branch-financial.show');

    // Update report
    Route::put('/branch-financial/update/{id}', [BranchFinancialController::class, 'update'])->name('branch-financial.update');

    // Delete report
    Route::delete('/branch-financial/destroy/{id}', [BranchFinancialController::class, 'destroy'])->name('branch-financial.destroy');

    // financial report branch

    Route::prefix('financial-reports')->name('financial-reports.')->group(function () {

        // Read-only routes
        Route::get('/', [FinancialReportController::class, 'index'])->name('index');

        // ⚠️  Export routes MUST come before /{id} to avoid "excel"/"csv" being treated as an ID
        Route::get('/export/excel', [FinancialReportController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/csv',   [FinancialReportController::class, 'exportCsv'])->name('export.csv');

        // Single report detail
        Route::get('/{id}', [FinancialReportController::class, 'show'])->name('show');

        // ✅ NEW: Attachments for a report
        Route::get('/{id}/attachments', [FinancialReportController::class, 'getAttachments'])->name('attachments');

        // Auditor Approval routes (access_limits = 4)
        Route::post('/{id}/approve-auditor', [FinancialReportController::class, 'approveAuditor'])->name('approve.auditor');
        Route::post('/{id}/reject-auditor',  [FinancialReportController::class, 'rejectAuditor'])->name('reject.auditor');

        // Management Approval routes (access_limits = 1)
        Route::post('/{id}/approve-management', [FinancialReportController::class, 'approveManagement'])->name('approve.management');
        Route::post('/{id}/reject-management',  [FinancialReportController::class, 'rejectManagement'])->name('reject.management');
    });
    //   Route::get('/financial-reports', [FinancialReportController::class, 'index'])->name('financial-reports.index');

    // // View single report
    // Route::get('/financial-reports/{id}', [FinancialReportController::class, 'show'])->name('financial-reports.show');

    // // Export routes
    // Route::get('/financial-reports/export/excel', [FinancialReportController::class, 'exportExcel'])->name('financial-reports.export.excel');
    // Route::get('/financial-reports/export/csv', [FinancialReportController::class, 'exportCsv'])->name('financial-reports.export.csv');

    // // Download file
    // Route::get('/financial-reports/{id}/download/{type}', [FinancialReportController::class, 'downloadFile'])->name('financial-reports.download');
    // Route::prefix('bank-reconciliation')->name('bank-reconciliation.')->middleware(['auth'])->group(function () {
    //     Route::get('/', [BankStatementController::class, 'index'])->name('index');
    //     Route::post('/upload', [BankStatementController::class, 'upload'])->name('upload');
    //     Route::get('/statements', [BankStatementController::class, 'getStatements'])->name('statements');
    //     Route::post('/search-bills', [BankStatementController::class, 'searchBills'])->name('search-bills');
    //     Route::post('/match', [BankStatementController::class, 'matchBill'])->name('match');
    //     Route::post('/unmatch/{id}', [BankStatementController::class, 'unmatch'])->name('unmatch');
    //     Route::delete('/destroy/{id}', [BankStatementController::class, 'destroy'])->name('destroy');
    // });
    Route::prefix('bank-reconciliation')->name('bank-reconciliation.')->middleware(['auth'])->group(function () {
        // Main page
        Route::get('/', [BankStatementController::class, 'index'])->name('index');
        // Upload Excel file
        Route::post('/upload', [BankStatementController::class, 'upload'])->name('upload');
        // Get bank statements with filters
        Route::get('/statements', [BankStatementController::class, 'getStatements'])->name('statements');
        Route::get('/drilldown/by-nature', [BankStatementController::class, 'drilldownStatementsByNature'])->name('drilldown.by-nature');
        Route::get('/drilldown/by-zone', [BankStatementController::class, 'drilldownStatementsByZone'])->name('drilldown.by-zone');
        Route::get('/statements-export', [BankStatementController::class, 'exportStatements'])->name('statements-export');
        Route::get('/matched-by-options', [BankStatementController::class, 'listMatchedByUsersForFilter'])->name('matched-by-options');
        Route::get('/quick-filter-options', [BankStatementController::class, 'statementQuickFilterOptions'])->name('quick-filter-options');
        Route::get('/user-history', [BankStatementController::class, 'listBankReconUserHistory'])->name('user-history');
        Route::get('/chart-accounts', [BankStatementController::class, 'listChartAccounts'])->name('chart-accounts');
        // Search bills by amount
        Route::post('/search-bills', [BankStatementController::class, 'searchBills'])->name('search-bills');
        // Filter bills with advanced criteria
        Route::post('/filter-bills', [BankStatementController::class, 'filterBills'])->name('filter-bills');
        // Match statement to bill
        Route::post('/match', [BankStatementController::class, 'matchBill'])->name('match');
        // Unmatch statement
        Route::post('/unmatch/{id}', [BankStatementController::class, 'unmatch'])->name('unmatch');
        // Delete statement
        Route::delete('/destroy/{id}', [BankStatementController::class, 'destroy'])->name('destroy');
        // Delete batch
        Route::post('/delete-batch', [BankStatementController::class, 'deleteBatch'])->name('delete-batch');
        // Income Tag - apply bank statement to income reconciliation record
        Route::post('/income-tag', [BankStatementController::class, 'applyIncomeTag'])->name('income-tag');
        // Income Tag - remove income tag from a bank statement
        Route::post('/income-unmatch/{id}', [BankStatementController::class, 'unmatchIncome'])->name('income-unmatch');
        // Fetch single bank statement by ID (for income recon ref-number click)
        Route::get('/statement/{id}', [BankStatementController::class, 'getBankStatementById'])->name('statement.show');
        // Salary UTR sheet: auto-match by UTR in description
        Route::post('/salary-utr-upload', [BankStatementController::class, 'uploadSalaryUtr'])->name('salary-utr-upload');
        Route::get('/salary-utr-uploads', [BankStatementController::class, 'listSalaryUtrUploads'])->name('salary-utr-uploads');
        Route::get('/salary-utr-uploads/{id}/rows', [BankStatementController::class, 'salaryUtrUploadRows'])->name('salary-utr-uploads.rows');
        Route::delete('/salary-utr-uploads/{id}', [BankStatementController::class, 'deleteSalaryUpload'])->name('salary-utr-uploads.delete');
        Route::get('/salary-master', [BankStatementController::class, 'salaryMasterPage'])->name('salary-master');
        Route::get('/salary-master/data', [BankStatementController::class, 'salaryMasterData'])->name('salary-master.data');
        Route::get('/salary-master/export', [BankStatementController::class, 'exportSalaryMaster'])->name('salary-master.export');
        // Income Tag supporting dropdowns
        Route::get('/income-tag/zones', [BankStatementController::class, 'incomeTagZones'])->name('income-tag.zones');
        Route::get('/income-tag/branches', [BankStatementController::class, 'incomeTagBranches'])->name('income-tag.branches');
        Route::get('/income-tag/resolve-description', [BankStatementController::class, 'incomeTagResolveDescription'])->name('income-tag.resolve-description');
        Route::get('/radiant-cash-pickups-for-date', [BankStatementController::class, 'radiantCashPickupsForTransactionDate'])->name('radiant-cash-pickups-for-date');
        Route::post('/radiant-match-against', [BankStatementController::class, 'saveRadiantMatchAgainst'])->name('radiant-match-against');
        Route::post('/radiant-unmatch/{id}', [BankStatementController::class, 'unmatchRadiant'])->name('radiant-unmatch');
        // Bank account master & batch history
        Route::get('/batch-uploads', [BankStatementController::class, 'batchUploadPage'])->name('batch-uploads');
        Route::get('/accounts', [BankStatementController::class, 'listBankAccounts'])->name('accounts');
        Route::post('/accounts', [BankStatementController::class, 'storeBankAccount'])->name('accounts.store');
        Route::put('/accounts/{id}', [BankStatementController::class, 'updateBankAccount'])->name('accounts.update');
        Route::get('/upload-batches', [BankStatementController::class, 'listUploadBatches'])->name('upload-batches');
        Route::get('/batch-file/{uploadBatchId}', [BankStatementController::class, 'downloadBatchFile'])->name('batch-file');
        Route::get('/batch-preview/{uploadBatchId}', [BankStatementController::class, 'previewBatch'])->name('batch-preview');
        // Match attachment document types (PO, Quotation, …) — master + dropdown JSON
        Route::get('/match-attachment-types', [BankStatementController::class, 'listMatchAttachmentTypes'])->name('match-attachment-types.index');
        Route::post('/match-attachment-types', [BankStatementController::class, 'storeMatchAttachmentType'])->name('match-attachment-types.store');
        Route::post('/match-attachment-types/{id}', [BankStatementController::class, 'updateMatchAttachmentType'])->name('match-attachment-types.update');
        Route::delete('/match-attachment-types/{id}', [BankStatementController::class, 'destroyMatchAttachmentType'])->name('match-attachment-types.destroy');
    });

    //income new stats
    Route::get('/billing-stats',  [BillingStatsController::class, 'index'])->name('superadmin.billingstats');
    Route::get('/billing-export/excel', [BillingStatsController::class, 'export'])->name('superadmin.billingexport');
    Route::post('/billing-stats/fetch-insert', [BillingStatsController::class, 'fetchAndInsert'])->name('superadmin.billingstats.fetchinsert');

    // Bill Category routes
    Route::get('superadmin/bill_category', [VendorController::class, 'getBillCategory'])->name('superadmin.billcategory.index');
    Route::post('superadmin/bill_category', [VendorController::class, 'storeBillCategory'])->name('superadmin.billcategory.store');

    Route::get('/notifications/unread',    [WebNotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read',[WebNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [WebNotificationController::class, 'markAllRead'])->name('notifications.readAll');

    Route::get('/radiant-cash-pickup',                   [RadiantCashPickupController::class, 'index'])->name('superadmin.radiantcash.index');
    Route::get('/radiant-cash-pickup/data',              [RadiantCashPickupController::class, 'data'])->name('superadmin.radiantcash.data');
    Route::get('/radiant-cash-pickup/filter-options',    [RadiantCashPickupController::class, 'getFilterOptions'])->name('superadmin.radiantcash.filteroptions');
    Route::get('/radiant-cash-pickup/reconcile-counts',  [RadiantCashPickupController::class, 'reconcileCounts'])->name('superadmin.radiantcash.reconcilecounts');
    Route::get('/radiant-cash-pickup/reconcile-lists',   [RadiantCashPickupController::class, 'reconcileLists'])->name('superadmin.radiantcash.reconcilists');
    Route::get('/radiant-cash-pickup/batches',          [RadiantCashPickupController::class, 'batches'])->name('superadmin.radiantcash.batches');
    Route::post('/radiant-cash-pickup/upload',           [RadiantCashPickupController::class, 'upload'])->name('superadmin.radiantcash.upload');
    Route::post('/radiant-cash-pickup/delete-batch',     [RadiantCashPickupController::class, 'deleteBatch'])->name('superadmin.radiantcash.deletebatch');
    Route::get('/radiant-cash-pickup/stats',             [RadiantCashPickupController::class, 'stats'])->name('superadmin.radiantcash.stats');
    Route::get('/radiant-cash-pickup/{id}/compare',      [RadiantCashPickupController::class, 'compare'])->name('superadmin.radiantcash.compare');
    Route::post('/radiant-cash-pickup/mismatch-alert', [RadiantMismatchAlertController::class, 'sendAlert'])->name('superadmin.radiantcash.mismatchalert');


    // Menu Master
    Route::get( 'superadmin/menu-master',           [MenuMasterController::class, 'index']  )->name('superadmin.menumaster.index');
    Route::get( 'superadmin/menu-master/list',      [MenuMasterController::class, 'list']   )->name('superadmin.menumaster.list');
    Route::post('superadmin/menu-master',           [MenuMasterController::class, 'store']  )->name('superadmin.menumaster.store');
    Route::get( 'superadmin/menu-master/{id}',      [MenuMasterController::class, 'show']   )->name('superadmin.menumaster.show');
    Route::put( 'superadmin/menu-master/{id}',      [MenuMasterController::class, 'update'] )->name('superadmin.menumaster.update');
    Route::delete('superadmin/menu-master/{id}',    [MenuMasterController::class, 'destroy'])->name('superadmin.menumaster.destroy');

    // Location Master — zones (tblzones) + branches (tbl_locations.zone_id); same data as VendorController bill/branch dropdowns
    Route::get('superadmin/location-master', [LocationMasterController::class, 'index'])->name('superadmin.locationmaster.index');
    Route::get('superadmin/location-master/locations-list', [LocationMasterController::class, 'locationsList'])->name('superadmin.locationmaster.locations.list');
    Route::post('superadmin/location-master/zones', [LocationMasterController::class, 'storeZone'])->name('superadmin.locationmaster.zones.store');
    Route::put('superadmin/location-master/zones/{id}', [LocationMasterController::class, 'updateZone'])->name('superadmin.locationmaster.zones.update');
    Route::delete('superadmin/location-master/zones/{id}', [LocationMasterController::class, 'destroyZone'])->name('superadmin.locationmaster.zones.destroy');
    Route::post('superadmin/location-master/locations', [LocationMasterController::class, 'storeLocation'])->name('superadmin.locationmaster.locations.store');
    Route::get('superadmin/location-master/locations/{id}', [LocationMasterController::class, 'showLocation'])->name('superadmin.locationmaster.locations.show');
    Route::put('superadmin/location-master/locations/{id}', [LocationMasterController::class, 'updateLocation'])->name('superadmin.locationmaster.locations.update');
    Route::delete('superadmin/location-master/locations/{id}', [LocationMasterController::class, 'destroyLocation'])->name('superadmin.locationmaster.locations.destroy');

    // Email Master
    Route::get('superadmin/email-master', [EmailMasterController::class, 'index'])->name('superadmin.emailmaster.index');
    Route::post('superadmin/email-master/store', [EmailMasterController::class, 'store'])->name('superadmin.emailmaster.store');
    Route::delete('superadmin/email-master/{id}', [EmailMasterController::class, 'destroy'])->name('superadmin.emailmaster.destroy');
    Route::patch('superadmin/email-master/{id}/toggle', [EmailMasterController::class, 'toggleStatus'])->name('superadmin.emailmaster.toggle');

    // Expense Type routes
    Route::get('superadmin/expense_type', [VendorController::class, 'getExpenseType'])->name('superadmin.expensetype.index');
    Route::post('superadmin/expense_type', [VendorController::class, 'storeExpenseType'])->name('superadmin.expensetype.store');

    // Expense Category routes
    Route::get('superadmin/expense_category', [VendorController::class, 'getExpenseCategory'])->name('superadmin.expensecategory.index');
    Route::post('superadmin/expense_category', [VendorController::class, 'storeExpenseCategory'])->name('superadmin.expensecategory.store');

    // Report Routes
    Route::get('next-report-id', [VendorController::class, 'getNextReportId']);
    Route::get('superadmin/expense-report', [VendorController::class, 'getExpenseReport'])->name('superadmin.expensereport.index');
    Route::post('superadmin/expense-report', [VendorController::class, 'storeExpenseReport'])->name('superadmin.expensereport.store');

    // Petty Cash Routes
    Route::get('/superadmin/petty-cash', [PettyCashController::class, 'getPettyCash'])->name('superadmin.getpettycash');
    Route::get('/superadmin/petty-cash-create', [PettyCashController::class, 'getPettyCashCreate'])->name('superadmin.getpettycashcreate');
    Route::post('/superadmin/save-petty-cash', [PettyCashController::class, 'savePettyCash'])->name('superadmin.savepettycash');
    Route::post('/superadmin/save-petty-cash-bulk', [PettyCashController::class, 'savePettyCashBulk'])->name('superadmin.savepettycashbulk');
    Route::get('/superadmin/pettycash-approver', [PettyCashController::class, 'pettyCashApprover'])->name('superadmin.PettyCashApprover');
    Route::get('/superadmin/petty-cash-ajax', [PettyCashController::class, 'getPettyCashAjax'])->name('superadmin.getpettycashajax');
    Route::get('/superadmin/petty-cash-detail', [PettyCashController::class, 'getPettyCashDetail'])->name('superadmin.getpettycashdetail');

    // Petty Cash Reports Routes
    Route::get('/superadmin/petty-cash-reports', [PettyCashController::class, 'getPettyCashReports'])->name('superadmin.getpettycashreports');
    Route::get('/superadmin/petty-cash-reports-ajax', [PettyCashController::class, 'getPettyCashReportsAjax'])->name('superadmin.getpettycashreportsajax');
    Route::get('/superadmin/petty-cash-reports-export', [PettyCashController::class, 'exportPettyCashReports'])->name('superadmin.exportpettycashreports');

    Route::get('/superadmin/pettycash-export', [PettyCashController::class, 'exportPettyCash'])->name('superadmin.pettycash.export');
    Route::get('/download-pettycash-template', [PettyCashController::class, 'downloadPettyCashTemplate'])->name('superadmin.pettycash.template');
    Route::post('/import-pettycash', [PettyCashController::class, 'importPettyCashExcel'])->name('superadmin.pettycash.import');

    Route::get('/superadmin/expense-report-detail', [PettyCashController::class, 'getExpenseReportDetail'])->name('superadmin.getexpensereportdetail');
    Route::post('/superadmin/expense-report-submit', [PettyCashController::class, 'submitExpenseReportForApproval'])->name('superadmin.expensereportsubmit');
    Route::post('/superadmin/expense-report-approve', [PettyCashController::class, 'approveExpenseReport'])->name('superadmin.expensereportapprove');
    Route::post('/superadmin/expense-report-reject', [PettyCashController::class, 'rejectExpenseReport'])->name('superadmin.expensereportreject');
    Route::post('/superadmin/expense-report-reimburse', [PettyCashController::class, 'markExpenseReportReimbursed'])->name('superadmin.expensereportreimburse');
    Route::get('/superadmin/expense-report-advances', [PettyCashController::class, 'getAdvancesForExpenseReport'])->name('superadmin.expensereportadvances');
    Route::post('/superadmin/expense-report-apply-advances', [PettyCashController::class, 'applyAdvancesToExpenseReport'])->name('superadmin.expensereportapplyadvances');

    // Advances Routes
    Route::get('/superadmin/advances', [PettyCashController::class, 'getAdvances'])->name('superadmin.getadvances');
    Route::get('/superadmin/advances-create', [PettyCashController::class, 'getAdvancesCreate'])->name('superadmin.getadvancescreate');
    Route::post('/superadmin/save-advance', [PettyCashController::class, 'saveAdvance'])->name('superadmin.saveadvance');
    Route::get('/superadmin/advances-ajax', [PettyCashController::class, 'getAdvancesAjax'])->name('superadmin.getadvancesajax');
    Route::get('/superadmin/advance-approver', [PettyCashController::class, 'advanceApprover'])->name('superadmin.advanceApprover');
    Route::get('/superadmin/advance-month-balance', [PettyCashController::class, 'getAdvanceMonthBalance'])->name('superadmin.getadvancemonthbalance');
    Route::post('/superadmin/advance-apply', [PettyCashController::class, 'applyAdvanceToExpenses'])->name('superadmin.applyadvance');
    Route::get('/superadmin/advance-detail', [PettyCashController::class, 'getAdvanceDetail'])->name('superadmin.advancedetail');
    Route::get('/superadmin/advance-reports', [PettyCashController::class, 'getReportsForAdvance'])->name('superadmin.getreportsforadvance');
    Route::post('/superadmin/advance-link-report', [PettyCashController::class, 'linkAdvanceReport'])->name('superadmin.linkreport');
    Route::post('/superadmin/advance-recall', [PettyCashController::class, 'recallAdvance'])->name('superadmin.recalladvance');

    // Department Routes
    Route::get('superadmin/departments', [TicketController::class, 'getDepartments'])->name('superadmin.departments.index');
    Route::post('superadmin/departments', [TicketController::class, 'storeDepartments'])->name('superadmin.departments.store');
    Route::get('superadmin/departments/users', [TicketController::class, 'departmentAssignedUsers'])->name('superadmin.departments.users');

    // Ticket Category Routes
    Route::get('superadmin/ticket-categories', [TicketController::class, 'getTicketCategories'])->name('superadmin.ticket.categories.index');
    Route::post('superadmin/ticket-categories', [TicketController::class, 'storeTicketCategories'])->name('superadmin.ticket.categories.store');

    // Issue Category (department, SLA, linked to Ticket Category)
    Route::get('superadmin/issue-categories', [TicketController::class, 'getIssueCategories'])->name('superadmin.issue.categories.index');
    Route::post('superadmin/issue-categories', [TicketController::class, 'storeIssueCategories'])->name('superadmin.issue.categories.store');
    Route::get('get-ticket-categories/{department_id}', [TicketController::class, 'getTicketCategoriesByDepartment'])->name('superadmin.ticket.categories.by-department');

    // Support tickets
    Route::get('superadmin/tickets', [TicketController::class, 'index'])->name('superadmin.tickets.index');
    Route::get('superadmin/tickets/data', [TicketController::class, 'data'])->name('superadmin.tickets.data');
    Route::get('superadmin/tickets/export', [TicketController::class, 'export'])->name('superadmin.tickets.export');
    Route::get('superadmin/tickets/categories-by-department', [TicketController::class, 'categoriesByDepartment'])->name('superadmin.tickets.categories');
    Route::get('superadmin/tickets/ticket-category-list', [TicketController::class, 'listTicketCategoryParents'])->name('superadmin.tickets.ticket-category-list');
    Route::post('superadmin/tickets', [TicketController::class, 'store'])->name('superadmin.tickets.store');
    Route::post('superadmin/tickets/update', [TicketController::class, 'update'])->name('superadmin.tickets.update');
    Route::post('superadmin/tickets/status', [TicketController::class, 'updateStatus'])->name('superadmin.tickets.status');
    Route::get('superadmin/tickets/{ticket}/timeline', [TicketController::class, 'timeline'])->name('superadmin.tickets.timeline');
    Route::get('superadmin/tickets/attachment', [TicketController::class, 'viewAttachment'])->name('superadmin.tickets.attachment');

    // Licence Documents Routes
    Route::get('superadmin/licence-documents', [LicenceDocumentController::class, 'index'])->name('superadmin.licence_documents.index');
    Route::get('superadmin/licence-documents-catalog', [LicenceDocumentCatalogController::class, 'index'])->name('superadmin.licence_documents.catalog.index');
    Route::post('superadmin/licence-documents-catalog', [LicenceDocumentCatalogController::class, 'store'])->name('superadmin.licence_documents.catalog.store');
    Route::get('superadmin/licence-documents/branch/{branch}', [LicenceDocumentController::class, 'branch'])->name('superadmin.licence_documents.branch');
    Route::post('superadmin/licence-documents/save', [LicenceDocumentController::class, 'save'])->name('superadmin.licence_documents.save');

    // Asset Category Routes
    Route::get('superadmin/asset-categories', [AssetController::class, 'getAssetCategories'])->name('superadmin.asset.categories.index');
    Route::post('superadmin/asset-categories', [AssetController::class, 'storeAssetCategories'])->name('superadmin.asset.categories.store');

    // Assets Routes
    Route::get('superadmin/assets-dashboard', [AssetController::class, 'getAssets'])->name('superadmin.assets.dashboard');
    Route::get('superadmin/assets/data', [AssetController::class, 'assetsData'])->name('superadmin.assets.data');
    Route::get('superadmin/assets/create', [AssetController::class, 'createAsset'])->name('superadmin.assets.create');
    Route::post('superadmin/assets', [AssetController::class, 'storeAsset'])->name('superadmin.assets.store');
    Route::get('superadmin/assets/{asset}/edit', [AssetController::class, 'editAsset'])->name('superadmin.assets.edit')->whereNumber('asset');
    Route::post('superadmin/assets/update', [AssetController::class, 'updateAsset'])->name('superadmin.assets.update');
    Route::get('superadmin/assets/export', [AssetController::class, 'exportAssets'])->name('superadmin.assets.export');
    Route::get('superadmin/assets/import-template', [AssetController::class, 'downloadAssetImportTemplate'])->name('superadmin.assets.import.template');
    Route::post('superadmin/assets/import', [AssetController::class, 'importAssetsExcel'])->name('superadmin.assets.import');

    // Consumable store Routes
    Route::get('/superadmin/consumable-store', [AssetController::class, 'getConsumableStoreDashboard'])->name('superadmin.consumable-store.dashboard');

    // Indents Routes
    Route::get('superadmin/indents/create', [IndentController::class, 'create'])->name('superadmin.indents.create');
    Route::get('superadmin/indents/data', [IndentController::class, 'data'])->name('superadmin.indents.data');
    Route::get('superadmin/indents/stock', [IndentController::class, 'stockOptions'])->name('superadmin.indents.stock');
    Route::post('superadmin/indents', [IndentController::class, 'store'])->name('superadmin.indents.store');
    Route::post('superadmin/indents/{indent}/status', [IndentController::class, 'updateStatus'])->name('superadmin.indents.status');
    Route::post('superadmin/indents/{indent}/issue', [IndentController::class, 'issue'])->name('superadmin.indents.issue');
    Route::get('superadmin/indents/{indent}/history', [IndentController::class, 'history'])->name('superadmin.indents.history');
    Route::get('superadmin/indents/{indent}/detail', [IndentController::class, 'show'])->name('superadmin.indents.show');
    Route::get('superadmin/indents', [IndentController::class, 'index'])->name('superadmin.indents.index');
    });

Route::middleware(['auth','role_id:2'])->group(function () {
    Route::get('/referral/referral',[MarketController::class,'referral'])->name('referral.referral');
     // doctor fitters and added route ........ //
     Route::post('/referral/doctoradded', [MarketController::class, 'doctoradded'])->name('referral.doctoradded');
     Route::post('/referral/doctoraddedimg', [MarketController::class, 'doctoraddedimgs'])->name('referral.doctoraddedimg');
     Route::get('/referral/fetch', [MarketController::class, 'fetch'])->name('referral.fetch');
     Route::get('/referral/fetchfitter', [MarketController::class, 'fetchfitter'])->name('referral.fetchfitter');
     Route::GET('/referral/fetchmorefitter', [MarketController::class, 'fetchmorefitter'])->name('referral.fetchmorefitter');
     Route::get('/referral/fetchmorefitterremove', [MarketController::class, 'fetchmorefitterremove'])->name('referral.fetchmorefitterremove');
     Route::get('/referral/fetchmorefitterdate', [MarketController::class, 'fetchmorefitterdate'])->name('referral.fetchmorefitterdate');
     Route::get('/referral/fetchmorefitterdateclr', [MarketController::class, 'fetchmorefitterdateclr'])->name('referral.fetchmorefitterdateclr');
     Route::post('/referral/doctordetailsedit', [MarketController::class, 'doctordetailsedit'])->name('referral.doctordetailsedit');
     Route::get('/referral/doctordetailsid', [MarketController::class, 'doctordetailsid'])->name('referral.doctordetailsid');
     Route::get('/referral/branchfetchviews', [MarketController::class, 'branchfetchviews'])->name('referral.branchfetchviews');
     Route::get('/referral/zonefetchviews', [MarketController::class, 'zonefetchviews'])->name('referral.zonefetchviews');
     Route::get('/referral/marketernamesurls', [MarketController::class, 'marketernamesurls'])->name('referral.marketernamesurls');

  // doctor fitters end ......
     Route::get('/referral/meetingid', [MarketController::class, 'meetingid'])->name('referral.meetingid');
     Route::post('/referral/meetinginsert', [MarketController::class, 'meetinginsert'])->name('referral.meetinginsert');
     Route::get('/referral/meetingallviews', [MarketController::class, 'meetingallviews'])->name('referral.meetingallviews');
     Route::get('/referral/meetingdatefitter', [MarketController::class, 'meetingdatefitter'])->name('referral.meetingdatefitter');
     Route::get('/referral/meetingmorefitter', [MarketController::class, 'meetingmorefitter'])->name('referral.meetingmorefitter');
     Route::get('/referral/meetingremovefitter', [MarketController::class, 'meetingremovefitter'])->name('referral.meetingremovefitter');
     Route::get('/referral/meetingclrfitter', [MarketController::class, 'meetingclrfitter'])->name('referral.meetingclrfitter');
     Route::get('/referral/meetingdateandfitter', [MarketController::class, 'meetingdateandfitter'])->name('referral.meetingdateandfitter');

    // meeting details end .................
     Route::get('/referral/patientid', [MarketController::class, 'patientid'])->name('referral.patientid');
     Route::post('/referral/patientinsert', [MarketController::class, 'patientinsert'])->name('referral.patientinsert');
     Route::get('/referral/patientallviews', [MarketController::class, 'patientallviews'])->name('referral.patientallviews');
     Route::get('/referral/patientdatefitter', [MarketController::class, 'patientdatefitter'])->name('referral.patientdatefitter');
     Route::get('/referral/patientmorefitter', [MarketController::class, 'patientmorefitter'])->name('referral.patientmorefitter');
     Route::get('/referral/patientremovefitter', [MarketController::class, 'patientremovefitter'])->name('referral.patientremovefitter');
     Route::get('/referral/patientclrfitter', [MarketController::class, 'patientclrfitter'])->name('referral.patientclrfitter');
     Route::get('/referral/patientdateandfitter', [MarketController::class, 'patientdateandfitter'])->name('referral.patientdateandfitter');
     Route::get('/referral/patientpop', [MarketController::class, 'patientpop'])->name('referral.patientpop');
     Route::get('/referral/meetingpop', [MarketController::class, 'meetingpop'])->name('referral.meetingpop');
});

//admin routes
Route::middleware(['auth','role_id:4'])->group(function () {
    Route::post('/storeAdminImage',[AdminController::class,'storeAdminImage']);
	Route::get('/admin/ticket', [AdminController::class, 'getTicket'])->name('admin.ticket');
	Route::get('/admin/ticketfetch', [AdminController::class, 'ticketFetch'])->name('admin.ticketfetch');
	Route::get('/admin/ticketActivity/{id}',[AdminController::class,'ticketActivity'])->name('admin.ticketActivity');
	Route::post('/adminReplyActivity',[AdminController::class,'adminReplyActivity'])->name('admin.adminReplyActivity');
	Route::get('/adminApproveActivity/{id}',[AdminController::class,'adminApproveActivity']);
	Route::get('/admin/ticketfillter', [AdminController::class, 'ticketFillter'])->name('admin.ticketfillter');
	Route::get('/admin/ticketdatefillter', [AdminController::class, 'ticketDateFillter'])->name('admin.ticketdatefillter');
	Route::post('/admin/getSubcategory',[AdminController::class,'subDepartmentBasedId']);
	Route::post('/admin/ticketadded', [AdminController::class, 'ticketAdded'])->name('admin.ticketadded');
	Route::get('/admin/myticketfetch', [AdminController::class, 'myTicketFetch'])->name('admin.myticketfetch');
	Route::get('/admin/myticketdatefillter', [AdminController::class, 'myticketDateFillter'])->name('admin.myticketdatefillter');
	Route::get('/admin/myticketfillter', [AdminController::class, 'myticketFillter'])->name('admin.myticketfillter');
	Route::get('/admin/fetchticketfitterremove', [AdminController::class, 'fetchticketfitterremove'])->name('admin.fetchticketfitterremove');
	Route::get('/admin/fetchmyticketfitterremove', [AdminController::class, 'fetchmyticketfitterremove'])->name('admin.fetchmyticketfitterremove');
	 Route::get('/admin/fetchticketfitter', [AdminController::class, 'fetchticketfitter'])->name('admin.fetchticketfitter');
	 Route::get('/admin/fetchmyticketfitter', [AdminController::class, 'fetchmyticketfitter'])->name('admin.fetchmyticketfitter');
});


//agent routes
Route::middleware(['auth','role_id:3'])->group(function () {
    Route::get('/staff/dashboard',[AgentController::class,'dashboard'])->name('staff.dashboard');
    Route::post('/staff/getSubcategory',[AgentController::class,'subCategoryBasedId']);
    Route::post('/staff/storeImage',[AgentController::class,'storeImage']);
    Route::post('/storeLoanImage',[AgentController::class,'storeLoanImage']);
	Route::get('/staff/ticket', [AgentController::class, 'getTicket'])->name('staff.ticket');
	Route::get('/staff/ticketfetch', [AgentController::class, 'ticketFetch'])->name('staff.ticketfetch');
	Route::post('/staff/ticketadded', [AgentController::class, 'ticketAdded'])->name('staff.ticketadded');
	Route::get('/staff/ticketActivity/{id}',[AgentController::class,'ticketActivity'])->name('staff.ticketActivity');
	Route::post('/staff/staffReplyActivity',[AgentController::class,'staffReplyActivity'])->name('staff.staffReplyActivity');
	Route::get('/staff/ticketfillter', [AgentController::class, 'ticketFillter'])->name('staff.ticketfillter');
	Route::get('/staff/ticketdatefillter', [AgentController::class, 'ticketDateFillter'])->name('staff.ticketdatefillter');
	 Route::get('/staff/fetchticketfitterremove', [AgentController::class, 'fetchticketfitterremove'])->name('staff.fetchticketfitterremove');
	 Route::get('/staff/fetchticketfitter', [AgentController::class, 'fetchticketfitter'])->name('staff.fetchticketfitter');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';
