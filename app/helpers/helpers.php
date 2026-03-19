<?php

use Illuminate\Support\Facades\Http;

/**
 * Helper function to get bill list from API using hardcoded values.
 *
 * @return array
 */
if (!function_exists('getBillListFromApi')) {
    function getBillListFromApi()
    {
        // Hardcoded values directly in the helper function
        $url = 'https://mocdoc.in/api/get/billlist/draravinds-ivf'; // Hardcoded URL
        $curr_date = '20250315'; // Hardcoded current date
       $locations = ['location23', 'location13', 'location24']; // Hardcoded location
		$arr = [];
			foreach ($locations as $location) {
				// Initialize cURL
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS => 'date=' . $curr_date . '&entitylocation=' . $location,
					CURLOPT_HTTPHEADER => array(
						'md-authorization: MD 7b40af0edaf0ad75:0yAJg5vPzhav8JdUyBmFq8sQvy8=',
						'Date: Fri, 07 Mar 2025 10:07:52 GMT',
						'Content-Type: application/x-www-form-urlencoded',
						'Cookie: SRV=s1'
					),
				));

				// Execute the cURL request
				$response = curl_exec($curl);
				curl_close($curl);

				// Decode the response from JSON
				$data = json_decode($response, true);

				$arr[] = [
					'data' => $data,
					'location' => $location
				];
			}
			return $arr;
    }
}

if (!function_exists('formatIndianMoney')) {
    /**
     * Format number in Indian numbering style (e.g. 12,34,567.89) with optional currency symbol.
     *
     * @param  float|int  $amount
     * @param  int  $decimals
     * @param  bool  $showSymbol
     * @return string
     */
    function formatIndianMoney($amount, $decimals = 2, $showSymbol = true) {
        // Ensure numeric
        $amount = is_numeric($amount) ? (float) $amount : 0.0;

        // Handle negative
        $negative = $amount < 0;
        $amount = abs($amount);

        // Separate integer and fractional parts (fixed decimals)
        $parts = explode('.', number_format($amount, $decimals, '.', ''));
        $intPart = $parts[0];
        $fracPart = $parts[1] ?? str_repeat('0', $decimals);

        // If number length <= 3 just return it with fraction
        if (strlen($intPart) <= 3) {
            $formattedInt = $intPart;
        } else {
            // Last 3 digits
            $last3 = substr($intPart, -3);
            // Leading part before last 3 digits
            $rest = substr($intPart, 0, -3);
            // Insert commas every 2 digits in the rest
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $formattedInt = $rest . ',' . $last3;
        }

        $result = $formattedInt;
        if ($decimals > 0) {
            $result .= '.' . $fracPart;
        }

        if ($showSymbol) {
            $result = $result;
        }

        return $negative ? ('-' . $result) : $result;
    }
}
