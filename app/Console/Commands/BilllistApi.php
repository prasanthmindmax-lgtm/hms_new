<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\BillingListModel;
use Illuminate\Support\Facades\Log;

class BilllistApi extends Command
{
    protected $signature = 'fetch:api-data';
    protected $description = 'Fetch API data for all locations and save to DB';

    public function handle()
    {
        $this->info("Starting API fetch...");

        $locations = $this->cityArray();

        // Start from first day of current month
        // $startDate = Carbon::yesterday()->startOfDay();
        // $endDate   = Carbon::yesterday()->endOfDay();
        // $endDate   = Carbon::now()->startOfMonth()->day(20)->startOfDay();

        $startDate = Carbon::createFromDate(2026, 4, 24)->startOfDay();
        $endDate   = Carbon::yesterday()->endOfDay();

        while ($startDate <= $endDate) {

            $date = $startDate->format('Ymd');
            Log::info("first date : $date ");
            foreach ($locations as $locId => $locName) {

                $response = $this->postCurlApi(
                    'https://mocdoc.in/api/get/billlist/draravinds-ivf',
                    $date,
                    $locId,
                    3
                );

                Log::info("API response for $locId on $date: " . json_encode($response));
                Log::info("API response for $locId on $locName: ");

                if (!empty($response["billinglist"])) {
                    $this->saveBillingData($locId, $locName, $date, $response["billinglist"]);
                } else {
                    Log::warning("No billinglist found for $locId on $date");
                }
            }

            $startDate->addDay(); // Move to next day
        }

        $this->info("API Fetch Completed Successfully.");
        return 0;
    }

    private function postCurlApi($url, $curr_date, $location_id, $max_retries = 3)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

          $post_fields = "date={$curr_date}&entitylocation={$location_id}";
          $head_fields = [
                  'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
                  'Date: Fri, 07 Mar 2025 10:07:52 GMT',
                  'Content-Type: application/x-www-form-urlencoded',
                  'Cookie: SRV=s1'
              ];

        $retry = 0;
        $backoff = 1;

        do {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post_fields,
                CURLOPT_HTTPHEADER => $head_fields,
                CURLOPT_FOLLOWLOCATION => true,      // FOLLOW REDIRECTS
                CURLOPT_MAXREDIRS => 10,             // max redirects
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($response === false) {
                $error = curl_error($curl);
                Log::error("CURL error for $location_id on $curr_date: $error");
                curl_close($curl);
                $retry++;
                sleep($backoff);
                $backoff *= 2;
                continue;
            }

            curl_close($curl);

            Log::info("API called for $location_id on $curr_date, HTTP code: $httpCode, Response: $response");

            if ($httpCode == 429) {
                Log::warning("HTTP 429 rate limit hit for $location_id on $curr_date. Retrying in $backoff sec.");
                sleep($backoff);
                $backoff *= 2;
                $retry++;
            } elseif ($httpCode != 200) {
                Log::warning("API returned HTTP $httpCode for $location_id on $curr_date. Response: $response");
                return null;
            } else {
                $decoded = json_decode($response, true);
                if ($decoded === null) {
                    Log::warning("Failed to decode JSON for $location_id on $curr_date. Raw response: $response");
                }
                return $decoded;
            }

        } while ($retry < $max_retries);

        Log::error("API request failed for $location_id on $curr_date after $max_retries retries.");
        return null;
    }



    private function cityArray()
    {
        // return [
        //     "location20" => "Coimbatore - Ganapathy",
        // ];
        return [
            "location1" => "Kerala - Palakkad",
            "location7" => "Erode",
            "location14" => "Tiruppur",
            "location6" => "Kerala - Kozhikode",
            "location20" => "Coimbatore - Ganapathy",
            "location21" => "Hosur",
            "location22" => "Chennai - Sholinganallur",
            "location23" => "Chennai - Urapakkam",
            "location24" => "Chennai - Madipakkam",
            "location26" => "Kanchipuram",
            "location27" => "Coimbatore - Sundarapuram",
            "location28" => "Trichy",
            "location29" => "Thiruvallur",
            "location30" => "Pollachi",
            "location31" => "Bengaluru - Electronic City",
            "location32" => "Bengaluru - Konanakunte",
            "location33" => "Chennai - Tambaram",
            "location34" => "Tanjore",
            "location36" => "Harur",
            "location39" => "Coimbatore - Thudiyalur",
            "location40" => "Madurai",
            "location41" => "Bengaluru - Hebbal",
            "location42" => "Kallakurichi",
            "location43" => "Vellore",
            "location44" => "Tirupati",
            "location45" => "Aathur",
            "location46" => "Namakal",
            "location47" => "Bengaluru - Dasarahalli",
            "location48" => "Chengalpattu",
            "location49" => "Chennai - Vadapalani",
            "location50" => "Pennagaram",
            "location51" => "Thirupathur",
            "location52" => "Sivakasi",
            "location13" => "Salem"
        ];
    }

    // private function saveBillingData($locId, $locName, $date, $billingList)
    // {
    //     foreach ($billingList as $item) {

    //         BillingListModel::create([
    //             'location_id'     => $locId,
    //             'location_name'   => $locName,
    //             'type'            => $item['type'] ?? null,
    //             'paymenttype'     => $item['paymenttype'] ?? null,
    //             'amt'             => $item['amt'] ?? 0,
    //             'billno'          => $item['billno'] ?? null,
    //             'billdate'        => $item['billdate'] ?? null,
    //             'user_name'       => $item['user'] ?? null,
    //             'userid'          => $item['userid'] ?? null,
    //             'phid'            => $item['phid'] ?? null,
    //             'extphid'         => $item['extphid'] ?? null,
    //             'gender'          => $item['gender'] ?? null,
    //             'age'             => $item['age'] ?? null,
    //             'mobile'          => $item['mobile'] ?? null,
    //             'ptsource'        => $item['ptsource'] ?? null,
    //             'isdcode'         => $item['isdcode'] ?? null,
    //             'dob'             => $item['dob'] ?? null,
    //             'email'           => $item['email'] ?? null,
    //             'patientname'     => $item['patientname'] ?? null,
    //             'patientkey'      => $item['patientkey'] ?? null,
    //             'consultant'      => $item['consultant'] ?? null,
    //             'consultantkey'   => $item['consultantkey'] ?? null,
    //             'referredbykey'   => $item['referredbykey'] ?? null,
    //             'referredby'      => $item['referredby'] ?? null,
    //             'provider'        => $item['provider'] ?? null,
    //             'billkey'         => $item['billkey'] ?? null,
    //             'billtype'        => $item['billtype'] ?? null,
    //             'tax'             => $item['tax'] ?? 0,
    //             'opno'            => $item['opno'] ?? null,
    //         ]);
    //     }
    // }

    private function saveBillingData($locId, $locName, $date, $billingList)
{
    foreach ($billingList as $item) {
        try {
            // Normalize the data - map API fields to database columns
            $billingData = [
                'location_id'     => $locId,
                'location_name'   => $locName,
                'type'            => $item['type'] ?? null,
                'paymenttype'     => $item['paymenttype'] ?? null,
                'amt'             => $item['amt'] ?? 0,
                'billno'          => $item['billno'] ?? null,
                'billdate'        => $item['billdate'] ?? $item['receivedat'] ?? null,
                'user_name'       => $item['user'] ?? $item['receivedby'] ?? null,
                'userid'          => $item['userid'] ?? $item['receivedbyid'] ?? null,
                'phid'            => $item['phid'] ?? null,
                'extphid'         => $item['extphid'] ?? null,
                'gender'          => $item['gender'] ?? null,
                'age'             => $item['age'] ?? null,
                'mobile'          => $item['mobile'] ?? null,
                'ptsource'        => $item['ptsource'] ?? null,
                'isdcode'         => $item['isdcode'] ?? null,
                'dob'             => $item['dob'] ?? null,
                'email'           => $item['email'] ?? null,
                'patientname'     => $item['patientname'] ?? null,
                'patientkey'      => $item['patientkey'] ?? null,
                'consultant'      => $item['consultant'] ?? null,
                'consultantkey'   => $item['consultantkey'] ?? null,
                'referredbykey'   => $item['referredbykey'] ?? null,
                'referredby'      => $item['referredby'] ?? null,
                'provider'        => $item['provider'] ?? null,
                'billkey'         => $item['billkey'] ?? null,
                'billtype'        => $item['billtype'] ?? null,
                // Handle different tax fields
                'tax'             => $item['grandtax'] ?? $item['tax'] ?? 0,
                'opno'            => $item['ipno'] ?? $item['opno'] ?? null,
                // Additional fields for IP data
                'receiptno'       => $item['receiptno'] ?? null,
                'receivedat'      => $item['receivedat'] ?? null,
                'grandtotal'      => $item['grandtotal'] ?? 0,
                'granddiscountvalue' => $item['granddiscountvalue'] ?? 0,
                'grandprodvalue'  => $item['grandprodvalue'] ?? 0,
                'paymentinfo'     => isset($item['paymentinfo']) ? json_encode($item['paymentinfo']) : null,
            ];

            BillingListModel::create($billingData);

            $logMsg = "Saved " . ($item['type'] ?? 'Unknown') . " record";
            $logMsg .= " PHID: " . ($item['phid'] ?? 'N/A');
            if (isset($item['receiptno'])) {
                $logMsg .= " Receipt: " . $item['receiptno'];
            }
            if (isset($item['billno'])) {
                $logMsg .= " Bill: " . $item['billno'];
            }

            Log::info($logMsg);

        } catch (\Exception $e) {
            Log::error("Failed to save billing data: " . $e->getMessage());
            Log::error("Problematic data: " . json_encode($item));
        }
    }
}


}
