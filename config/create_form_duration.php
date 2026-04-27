<?php

return [

    'input_name' => 'create_form_duration_ms',

    /** Reject & cap client-submitted values (ms). 172800000 = 48h */
    'max_ms' => 172800000,

    /** Suggested Eloquent column name (informational) */
    'suggested_column' => 'create_form_duration_ms',

    /**
     * Add hidden field + timing for all <form method="post"> in pages that load create_form_duration.js
     * (superadminfooter), except data-no-form-duration and excluded URL substrings.
     * Set to false to only track forms with data-create-form-duration.
     *
     * For jQuery modal + FormData saves, use data-cfd on the .sm-modal root and assets/js/create_form_duration_modal.js
     * (no per-blade session code).
     */
    'auto_track_post_forms' => (bool) env('CREATE_FORM_DURATION_AUTO', true),

    /**
     * If form action URL (lowercased) contains any of these, skip duration tracking.
     * JS may override via window.__CREATE_FORM_DURATION.
     */
    'excluded_path_substrings' => [
        '/login', '/logout', '/register', '/password',
        'forgot-password', 'password/reset', 'password/email',
    ],

    /**
     * Auto-apply data-cfd to all .sm-modal (see public/assets/js/create_form_duration_sm_modal_automark.js).
     * Exposed to JS as window.__CREATE_FORM_DURATION.smModalMark.
     *
     * Env (dynamic, no static lists in views):
     *   CFD_SM_MODAL_MARK_ENABLED=true|false
     *   CFD_SM_MODAL_INCLUDE_SUBSTRINGS=vendor,bill,quotation  (comma, OR: path must contain one; empty = all pages except exclusions)
     *   CFD_SM_MODAL_EXCLUDE_SUBSTRINGS=login,password            (if path contains one, do not mark modals on that page)
     *   CFD_SM_MODAL_INCLUDE_REGEXES=^/vendor/[\w/-]+$             (one PCRE per line, OR: path must match at least one if any line is set)
     */
    'sm_modal_mark' => [
        'enabled' => (bool) env('CFD_SM_MODAL_MARK_ENABLED', true),
        'include_substrings' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('CFD_SM_MODAL_INCLUDE_SUBSTRINGS', ''))
        ))),
        'exclude_substrings' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('CFD_SM_MODAL_EXCLUDE_SUBSTRINGS', ''))
        ))),
        /** One PCRE per line; if any is non-empty, path must match at least one. Empty = do not use regex. */
        'include_regexes' => array_values(array_filter(array_map(
            'trim',
            explode("\n", (string) env('CFD_SM_MODAL_INCLUDE_REGEXES', ''))
        ))),
    ],
];
