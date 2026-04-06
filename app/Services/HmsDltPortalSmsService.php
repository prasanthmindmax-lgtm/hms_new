<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * DLT template (single variable):
 * Dear {#var#} You have received a notification in Dr. Aravind's IVF HMS portal. Please log in and view it at draravinds.com/hms
 */
class HmsDltPortalSmsService
{
    /**
     * @return array{success: bool, response: string|null, curl_error: string|null, greeting_var: string, template_msg: string}
     */
    public function send(string $mobile, string $recipientName, string $detailLine, string $logContext = 'hms'): array
    {
        $greetingVar = $this->buildGreetingVar($recipientName, $detailLine);
        $templateMsg = "Dear {$greetingVar} You have received a notification in Dr. Aravind's IVF HMS portal. Please log in and view it at draravinds.com/hms";

        $baseUrl    = (string) config('hms_sms.pay4sms.base_url');
        $token      = (string) config('hms_sms.pay4sms.token');
        $credit     = (int) config('hms_sms.pay4sms.credit', 3);
        $sender     = (string) config('hms_sms.pay4sms.sender');
        $templateId = (string) config('hms_sms.pay4sms.template_id');

        $finalUrl = $baseUrl . '?'
            . 'token=' . $token
            . '&credit=' . $credit
            . '&sender=' . rawurlencode($sender)
            . '&message=' . rawurlencode($templateMsg)
            . '&number=' . rawurlencode($mobile)
            . '&templateid=' . rawurlencode($templateId);

        Log::info("[HmsDltPortalSms][{$logContext}] Sending", [
            'mobile' => $mobile,
            'dlt_var' => $greetingVar,
            'template_msg' => $templateMsg,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $finalUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        $responseStr = $response !== false ? (string) $response : '';
        $curlOk      = $response !== false && $curlErr === '';
        $gatewayOk   = $curlOk
            && $responseStr !== ''
            && ! preg_match('/^\d+\s*:\s*\S/u', trim($responseStr))
            && (bool) preg_match('/"Sent"|\bSent\b/u', $responseStr);

        if ($gatewayOk) {
            Log::info("[HmsDltPortalSms][{$logContext}] OK", ['mobile' => $mobile, 'response' => $responseStr]);
        } else {
            Log::error("[HmsDltPortalSms][{$logContext}] FAILED", [
                'mobile' => $mobile,
                'curl_error' => $curlErr,
                'response' => $responseStr,
            ]);
        }

        return [
            'success' => $gatewayOk,
            'response' => $response !== false ? $responseStr : null,
            'curl_error' => $curlErr !== '' ? $curlErr : null,
            'greeting_var' => $greetingVar,
            'template_msg' => $templateMsg,
        ];
    }

    public function buildGreetingVar(string $recipientName, string $smsDetailLine): string
    {
        $name   = preg_replace('/\s+/u', ' ', trim($recipientName)) ?: 'Team';
        $detail = preg_replace('/\s+/u', ' ', trim($smsDetailLine)) ?: 'notification';
        $var    = trim($name . ' ' . $detail);

        $suffix   = " You have received a notification in Dr. Aravind's IVF HMS portal. Please log in and view it at draravinds.com/hms";
        $maxTotal = (int) config('hms_sms.max_total_chars', 160);

        for ($i = 0; $i < 200; $i++) {
            if (strlen('Dear ' . $var . $suffix) <= $maxTotal) {
                return $var;
            }
            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                if (mb_strlen($var) <= 12) {
                    return $var;
                }
                $var = mb_substr($var, 0, mb_strlen($var) - 1) . '…';
            } else {
                if (strlen($var) <= 12) {
                    return $var;
                }
                $var = substr($var, 0, strlen($var) - 2) . '…';
            }
        }

        return $var;
    }
}
