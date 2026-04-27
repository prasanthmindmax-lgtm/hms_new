<?php

return [

    /**
     * Log full-page GET navigations under /superadmin/ (browsed sections) as "read" rows, excluding
     * ajax/data-table/fetch-style URLs. Disable with USER_UAP_LOG_NAV_GET=0 if too noisy.
     */
    'log_superadmin_navigation_gets' => (bool) env('USER_UAP_LOG_NAV_GET', true),

    /**
     * POST visibility/page events from the browser (tab / window). Set USER_UAP_CLIENT_TAB=1 to enable.
     * When enabled, events are still hidden from report UIs; use only for separate analytics.
     */
    'client_tab_events' => (bool) env('USER_UAP_CLIENT_TAB', false),

    /**
     * Route name used for client tab/window events; rows are excluded from dashboard/user report queries.
     */
    'client_event_route' => 'user_activity.client_event',

    /**
     * When true, a sendBeacon/keepalive POST runs on tab/window close so {@see UserActivityService::onLogout}
     * is invoked (same logout type in reports as clicking Logout). Disable with USER_UAP_BEACON_LOGOUT=0.
     */
    'beacon_logout_on_unload' => (bool) env('USER_UAP_BEACON_LOGOUT', true),

    /**
     * Work sessions list on the user activity detail page (sign-in / sign-out rows only).
     */
    'work_sessions_per_page' => min(100, max(10, (int) env('USER_UAP_WORK_SESSIONS_PER_PAGE', 25))),

    'log_raw_get_page_views' => (bool) env('USER_UAP_LOG_RAW_GET', false),
    'treat_ajax_get_as_background' => true,
    'noise_route_name_substrings' => [
        'getquotation', 'getpurchase', 'getbill', 'getpetty', 'getvendor', 'getadvance',
        'tickets.data', 'ticket.data', 'ticketsdata', 'fetch', '_ajax', '.ajax',
    ],
    'noise_path_substrings' => [
        '/tickets/data', '/ticketsdata', 'datatable', 'data-table', 'typeahead',
        'select2', 'autocomplete', 'getquotation', 'getpurchase', 'getbill', 'getpetty',
    ],
    'noise_path_prefixes' => [
        'livewire/',
        'broadcasting/',
        'horizon/',
    ],

    'session_key' => 'user_activity_session_id',

    'exclude_path_prefixes' => [
        'telescope',
        'horizon',
        '_ignition',
        'livewire',
    ],

    'exclude_paths_containing' => [
        'chrome-extension://',
    ],

    'read_extensions' => ['.map', '.woff', '.woff2', '.ttf', '.ico', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.css', '.js'],

    'do_not_log_route_names' => [
        'user_activity.dashboard',
        'user_activity.data',
        'user_activity.client_event',
        'user_activity.beacon_session_end',
        'superadmin.logs_store',
    ],

    'excluded_from_activity_report_route_names' => [
        'superadmin.logs_store',
    ],

    'module_path_map' => [
        'superadmin/purchase-flow' => 'Purchase flow (PO / GRN / Invoice)',
        'purchase-flow' => 'Purchase flow (PO / GRN / Invoice)',

        'superadmin/tickets' => 'Tickets & support',
        'superadmin.tickets' => 'Tickets & support',
        'tickets/data' => 'Tickets & support',

        'superadmin/indents' => 'Indent',
        'indents' => 'Indent',
        'superadmin.indents' => 'Indent',

        'superadmin/departments' => 'Tickets — departments',
        'issue-categories' => 'Issue categories',
        'ticket-categories' => 'Ticket categories',

        'superadmin/bill_made' => 'Bill made',
        'save-bill_made' => 'Bill made',
        'bill_made' => 'Bill made',

        'quotation' => 'Quotation',
        'import-quotation' => 'Quotation',

        'save-bill' => 'Bill (vendor)',
        'superadmin/bill_dashboard' => 'Bill (vendor)',
        'superadmin/bill_create' => 'Bill (vendor)',
        'superadmin/bill_print' => 'Bill (vendor)',
        'superadmin/bill_pdf' => 'Bill (vendor)',

        'superadmin/purchase_order' => 'Purchase order (PO)',
        'superadmin/purchase_dashboard' => 'Purchase order (PO)',
        'purchase_approver' => 'Purchase order (PO)',
        'superadmin/purchase_maker' => 'Purchase order (PO)',
        'superadmin/purchase_checker' => 'Purchase order (PO)',
        'superadmin/purchase_approver' => 'Purchase order (PO)',

        'transcationvendor' => 'Accounts book (vendor & transactions)',
        'vendorchart' => 'Accounts book (vendor & transactions)',
        'account_save' => 'Accounts book (ledger)',
        'vendor/statement' => 'Accounts book (vendor statement)',

        'bank-reconciliation' => 'Bank / books (reconciliation)',
        'chart-accounts' => 'Chart of accounts',
        'income_reconciliation' => 'Income (accounts / reconciliation)',

        'getvendor' => 'Vendor / accounts (master)',
        'superadmin/vendor' => 'Vendor / accounts (master)',

        'income' => 'Income (reports)',

        'getbillmade' => 'Bill made',
        'getquotation' => 'Quotation',
        'getpurchaseorder' => 'Purchase order (PO)',
        'getpurchase' => 'Purchase order (PO)',
        'getbill' => 'Bill (vendor)',

        'superadmin/purchase_request' => 'Purchase requests',
        'purchase_request' => 'Purchase requests',
        'superadmin.purchase' => 'Purchase (requests/flow)',

        'superadmin/user-activity' => 'User activity & performance',
        'user-activity' => 'User activity & performance',
        'user_activity' => 'User activity & performance',
        'superadmin/user-productivity' => 'User activity & performance',
        'user-productivity' => 'User activity & performance',
        'user_productivity' => 'User activity & performance',

        'superadmin/assets' => 'Asset management',
        'assets.dashboard' => 'Asset management',
        'assets' => 'Asset management',
        'assets.' => 'Asset management',

        'menuaccess' => 'Menu & access URLs',
        'menuaccessurl' => 'Menu & access URLs',
        'menu' => 'Menu & access',
        'superadmin/menu' => 'Menu & access',

        'logs' => 'LOGS',
        'logs_store' => 'LOGS',
        'superadmin/logs' => 'LOGS',

        'petty' => 'Petty cash',
        'petty_cash' => 'Petty cash',
        'superadmin/petty' => 'Petty cash',

        'licence' => 'Licence documents',
        'licence_documents' => 'Licence documents',
        'superadmin/licence' => 'Licence documents',

        'consume' => 'IT consumable store',
        'consumable' => 'IT consumable store',
        'income_table' => 'Income',

        'superadmin/location' => 'Location master',
        'location' => 'Location master',
        'branch' => 'Branch / location',

        'reconciliation' => 'Bank / reconciliation',
        'superadmin/branch' => 'Branch financial',
        'branch_financial' => 'Branch financial',
    ],

    'create_flow_post_to_get_route' => [
        'superadmin.savecustomer' => 'superadmin.getcustomercreate',
        'superadmin.savepettycash' => 'superadmin.getpettycashcreate',
        'superadmin.savebill' => 'superadmin.getbillcreate',
        'superadmin.savebillmade' => 'superadmin.getbillmadecreate',
        'superadmin.saveadvance' => 'superadmin.getadvancescreate',
        'superadmin.savequotation' => 'superadmin.getquotationcreate',
        'superadmin.savevendor' => 'superadmin.getvendorcreate',
        'superadmin.savepurchaseorder' => 'superadmin.getpurchasecreate',
        'superadmin.saveneft' => 'superadmin.getneftcreate',
        'superadmin.savegrn' => 'superadmin.getgrncreate',
    ],

    'create_flow_max_form_seconds' => 172800,

    /**
     * Whitelisted request input keys copied into activity_logs.request_snapshot (POST/GET) for linking.
     */
    'activity_log_request_snapshot_keys' => [
        'id', 'bill_id', 'quotation_id', 'purchase_id', 'pr_id', 'report_id', 'grn_id', 'petty_cash_id', 'ticket_id',
        'bill_number', 'order_number', 'quotation_number', 'q_no', 'quotation_no', 'po_number', 'ticket_no', 'ticket_code',
    ],

    'path_segment_to_module' => [
        'assets' => 'Asset management',
        'tickets' => 'Tickets & support',
        'purchase-flow' => 'Purchase flow (PO / GRN / Invoice)',
        'purchase_request' => 'Purchase requests',
        'purchase' => 'Purchase (requests/flow)',
        'user-activity' => 'User activity & performance',
        'user-productivity' => 'User activity & performance',
        'indents' => 'Indent',
        'bill_made' => 'Bill made',
        'vendor' => 'Vendor / accounts (master)',
        'income' => 'Income (reports)',
        'pettycash' => 'Petty cash',
        'petty' => 'Petty cash',
        'menu' => 'Menu & access',
        'logs' => 'LOGS',
        'licence' => 'Licence documents',
        'location' => 'Location master',
        'consumable' => 'IT consumable store',
        'accounts' => 'Accounts book',
        'bank-reconciliation' => 'Bank / books (reconciliation)',
    ],
];
