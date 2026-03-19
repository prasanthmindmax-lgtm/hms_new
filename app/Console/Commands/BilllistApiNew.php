<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\BillingListModelNew;
use Illuminate\Support\Facades\Log;

class BilllistApiNew extends Command
{
    protected $signature = 'fetch:api-data-new';
    protected $description = 'Fetch API data for all locations and save to DB';

    public function handle()
    {
        $this->info("Starting API fetch...");

        $locations = $this->cityArray();

        // $startDate = Carbon::yesterday()->startOfDay();
        // $endDate   = Carbon::yesterday()->endOfDay();
        $startDate = Carbon::now()->startOfMonth()->startOfDay();
        $endDate   = Carbon::now()->startOfMonth()->endOfDay();
        Log::info("startDate date: $startDate");
        Log::info("endDate date: $endDate");

        while ($startDate <= $endDate) {

            $date = $startDate->format('Ymd');
            Log::info("first date : $date ");
            foreach ($locations as $locId => $locName) {

                $response = $this->postCurlApi(
                    'https://mocdoc.com/api/get/billlist/detailed/draravinds-ivf',
                    $date,
                    $locId,
                    3
                );

                Log::info("API response for $locId on $date: " . json_encode($response));
                Log::info("API response for $locId on $locName: ");

                if (!empty($response["billinglist_detailed"])) {
                    $this->saveBillingData($locId, $locName, $date, $response["billinglist_detailed"]);
                } else {
                    Log::warning("No billinglist_detailed found for $locId on $date");
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
                    'md-authorization: MD 7b40af0edaf0ad75:2BHsbkH5tLQFe0/vFtvTyyRsBQQ=',
                    'Date: Mon, 03 Mar 2025 09:26:20 GMT',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Cookie: SRV=s1; vid3=CvAABmlBSiMJOwFWA8JqAg=='
                ];


        $retry = 0;
        $backoff = 10;

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
        return [
            "location14" => "Tiruppur",
           
        ];
    }

  private function saveBillingData($locId, $locName, $date, $billingData)
  {
      foreach ($billingData as $item) {
          try {
              BillingListModelNew::create([
                  'location_id'   => $locId,
                  'location_name' => $locName,
                  // Add date field if needed
                  'date'          => $date, // Add this if you want to store the date separately
                  'entitylocation'=> $item['entitylocation'] ?? null,
                  'receivedby'    => $item['receivedby'] ?? null,
                  'paymenttype'   => $item['paymenttype'] ?? null,
                  'amt'           => $item['amt'] ?? 0,
                  'billno'        => $item['bill_no'] ?? null,
                  'billdate'      => $item['billdate'] ?? null,
                  'phid'          => $item['phid'] ?? null,
                  'extphid'       => $item['extphid'] ?? null,
                  'gender'        => $item['gender'] ?? null,
                  'mobile'        => $item['mobile'] ?? null,
                  'ptsource'      => $item['ptsource'] ?? null,
                  'isdcode'       => $item['isdcode'] ?? null,
                  'dob'           => $item['dob'] ?? null,
                  'email'         => $item['email'] ?? null,
                  'patientname'   => $item['name'] ?? null,
                  'patientkey'    => $item['patientkey'] ?? null,
                  'consultant'    => $item['consultant'] ?? null,
                  'consultantkey' => $item['consultantkey'] ?? null,
                  'referredbykey' => $item['referredbykey'] ?? null,
                  'referredby'    => $item['referredby'] ?? null,
                  'provider'      => $item['provider'] ?? null,
                  'billkey'       => $item['billkey'] ?? null,
                  'billtype'      => $item['billtype'] ?? null,
                  'tax'           => $item['tax'] ?? 0,

                  // EXTRA FIELDS
                  'itemname'      => $item['itemname'] ?? null,
                  'dept'          => $item['dept'] ?? null,
                  'subdept'       => $item['subdept'] ?? null,
                  'hsn'           => $item['hsn'] ?? null,
                  'registrationdate' => $item['registrationdate'] ?? null,
                  'taxable_percentage'=> $item['taxable_percentage'] ?? 0,
                  'tax_amount'    => $item['tax_amount'] ?? 0,
                  'tds_percentage'=> $item['tds_percentage'] ?? 0,
                  'tds_amount'    => $item['tds_amount'] ?? 0,
                  'igst_amount'   => $item['igst_amount'] ?? 0,
                  'cgst_amount'   => $item['CGST'] ?? 0, // Note: this might be 'CGST' in the response
                  'sgst_amount'   => $item['SGST'] ?? 0, // Note: this might be 'SGST' in the response
              ]);

          } catch (\Exception $e) {
              Log::error("Insert failed: ".$e->getMessage());
              Log::error(json_encode($item));
          }
      }
  }
}
