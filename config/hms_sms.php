<?php

return [

    'pay4sms' => [
        'base_url' => rtrim((string) env('HMS_SMS_PAY4SMS_URL', 'http://pay4sms.in/sendsms/'), '/') . '/',
        'token' => (string) env('HMS_SMS_TOKEN', '55cefcfaa0bad65a743bcdba1d8b8a68'),
        'credit' => (int) env('HMS_SMS_CREDIT', 3),
        'sender' => (string) env('HMS_SMS_SENDER', 'DRAlVF'),
        'template_id' => (string) env('HMS_SMS_TEMPLATE_ID', '1707177521816818030'),
    ],

    /* Dear {#var#} You have received… — keep total length reasonable for one SMS segment */
    'max_total_chars' => (int) env('HMS_SMS_MAX_TOTAL_CHARS', 160),

];
