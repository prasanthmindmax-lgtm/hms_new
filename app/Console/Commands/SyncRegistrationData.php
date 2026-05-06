<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\MocdocRegistrationReport;

class SyncRegistrationData extends Command
{
    protected $signature   = 'mocdoc:sync-registration
                              {--days=1 : Past calendar days ending yesterday (1 = yesterday only; ignored if --from/--to or --date)}
                              {--date= : Single day Y-m-d}
                              {--from= : Start Y-m-d (inclusive; must use with --to)}
                              {--to= : End Y-m-d (inclusive; must use with --from)}';

    protected $description = 'Sync MOC Doc registration into mocdoc_registration_reports. Default: yesterday only. Range: --from and --to together. One day: --date.';

    private function cityCodeMap(): array
    {
        return [
            'AFHARU'  => 'Harur',
            'AFMDU'   => 'Madurai',
            'AFVPAL'  => 'Vepanapalli',
            'AFCHENG' => 'Chengalpattu',
            'AFURP'   => 'Chennai - Urapakkam',
            'AFERD'   => 'Erode',
            'AFKAL'   => 'Kallakurichi',
            'AFNKL'   => 'Nagapattinam',
            'AFSLM'   => 'Salem',
            'AFHZR'   => 'Hosur',
            'AFTRY'   => 'Trichy',
            'AFTHR'   => 'Thiruporur',
            'AFSPM'   => 'Sivagangai',
            'AFVEL'   => 'Vellore',
            'AFOMR'   => 'Old Mahabalipuram Road',
            'AFCPK'   => 'Coimbatore - Ganapathy',
            'AFKONA'  => 'Bengaluru - Konanakunte',
            'AFCBR'   => 'Chidambaram',
            'AFTPR'   => 'Tiruppur',
            'AFTPT'   => 'Thirupathur',
            'AFTAM'   => 'Thiruvannamalai',
            'AFSTY'   => 'Sathyamangalam',
            'AFDAS'   => 'Dindigul',
            'AFHBL'   => 'Bengaluru - Hebbal',
            'AFTAN'   => 'Tirunelveli',
            'AFTHI'   => 'Tiruvannamalai',
            'AFATR'   => 'Aathur',
            'AFPOL'   => 'Pollachi',
            'AFMDP'   => 'Mettupalayam',
            'AFKAN'   => 'Bangalore',
            'AFECT'   => 'Echanari',
            'Coimbatore - Sundarapuram' => 'Coimbatore - Sundarapuram',
            'Coimbatore - Thudiyalur'   => 'Coimbatore - Thudiyalur',
            'Kerala - Kozhikode'        => 'Kerala - Kozhikode',
            'Karur'                     => 'Karur',
            'Tiruppur'                  => 'Tiruppur',
            'Kerala - Palakkad'         => 'Kerala - Palakkad',
            'Tanjore'                   => 'Tanjore',
            'Kanchipuram'               => 'Kanchipuram',
            'Villupuram'                => 'Villupuram',
            'Thiruvallur'               => 'Thiruvallur',
            'Corporate Office - Guindy' => 'Corporate Office - Guindy',
            'Chennai - Madipakkam'      => 'Chennai - Madipakkam',
            'Chennai - Sholinganallur'  => 'Chennai - Sholinganallur',
            'Chennai - Tambaram'        => 'Chennai - Tambaram',
            'Chennai - Vadapalani'      => 'Chennai - Vadapalani',
        ];
    }

    public function handle(): int
    {
        $this->info('[SyncRegistrationData] Starting…');
        set_time_limit(0);

        $dates = $this->resolveDatesToProcess();
        if ($dates === null) {
            return 1;
        }

        $areaMap       = $this->cityCodeMap();
        $syncedAt      = now();
        $totalInserted = 0;
        $totalUpdated  = 0;

        foreach ($dates as $date) {
            $dateKey = $date->format('Ymd');
            $this->line("  → {$date->toDateString()}");

            $apiData = $this->callApi($dateKey);

            if (empty($apiData['ptlist'])) {
                $this->line('     No records.');
                continue;
            }

            foreach ($apiData['ptlist'] as $item) {
                if (empty($item['phid'])) continue;

                $phid   = $item['phid'];
                $prefix = explode('-', $phid)[0];
                $area   = $areaMap[$prefix] ?? ($areaMap[$phid] ?? 'Unknown');

                $regDate = null;
                if (!empty($item['created_at'])) {
                    try {
                        $regDate = Carbon::parse($item['created_at'])->toDateString();
                    } catch (\Exception $e) {
                        $regDate = $date->toDateString();
                    }
                } else {
                    $regDate = $date->toDateString();
                }

                $payload = [
                    'phid'      => $phid,
                    'prefix'    => $prefix,
                    'name'      => $item['name'] ?? null,
                    'mobile'    => $item['mobile'] ?? null,
                    'gender'    => $item['gender'] ?? null,
                    'age'       => $item['age'] ?? null,
                    'area'      => $area,
                    'reg_date'  => $regDate,
                    'synced_at' => $syncedAt,
                ];

                $updated = MocdocRegistrationReport::where('phid', $phid)->first();
                if ($updated) {
                    $updated->update($payload);
                    $totalUpdated++;
                } else {
                    MocdocRegistrationReport::create($payload);
                    $totalInserted++;
                }
            }

            usleep(400000); // 400ms between date requests
        }

        $this->info("[SyncRegistrationData] Done — Inserted: {$totalInserted}, Updated: {$totalUpdated}");
        return 0;
    }

    private function callApi(string $dateKey): array
    {
        $url        = 'https://mocdoc.com/api/get/ptlist/draravinds-ivf';
        $dte        = substr($dateKey, 0, 8);
        $postFields = "registrationdate={$dte}";
        $headers    = [
            'md-authorization: MD 7b40af0edaf0ad75:zzJIrJPzgSOMhucj/1bXawbz+GI=',
            'Date: Fri, 11 Apr 2025 06:18:59 GMT',
            'Content-Type: application/x-www-form-urlencoded',
            'Cookie: SRV=s1; vid3=CvAABmf4wWdOP+VJBV+AAg==',
        ];

        $maxRetries = 5;
        $delay      = 2;
        for ($i = 0; $i < $maxRetries; $i++) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 60,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $postFields,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($curl);
            $status   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($status === 200) return json_decode($response, true) ?? [];
            if ($status === 429) { sleep($delay); $delay *= 2; } else break;
        }
        return [];
    }

    /**
     * Build list of calendar days to sync (Asia/Kolkata).
     * Priority: --from + --to (both required)  >  --date  >  --days (default 1 = yesterday only).
     *
     * @return list<Carbon>|null null on validation error
     */
    private function resolveDatesToProcess(): ?array
    {
        $tz = 'Asia/Kolkata';

        $fromOpt = Carbon::createFromDate(2025, 4, 01)->startOfDay();
        $toOpt   = Carbon::yesterday()->endOfDay();
        $fromSet = $fromOpt !== null && $fromOpt !== '';
        $toSet   = $toOpt   !== null && $toOpt   !== '';

        if ($fromSet || $toSet) {
            if ($fromSet xor $toSet) {
                $this->error('For a date range pass both --from=Y-m-d and --to=Y-m-d (inclusive). For one day use --date=Y-m-d. Default with no flags: yesterday (--days=1).');
                return null;
            }
            try {
                $start = Carbon::parse($fromOpt, $tz)->startOfDay();
                $end   = Carbon::parse($toOpt,   $tz)->startOfDay();
            } catch (\Exception $e) {
                $this->error('Invalid --from or --to (use Y-m-d).');
                return null;
            }
            if ($end->lt($start)) {
                $this->error('--to must be on or after --from.');
                return null;
            }
            $dates = [];
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dates[] = $d->copy();
            }
            $this->line("Date range: {$start->toDateString()} → {$end->toDateString()} ({$tz}, ".count($dates)." day(s)).");
            return $dates;
        }

        if ($specificDate = $this->option('date')) {
            if ($specificDate === '') {
                $this->error('Pass a value for --date=Y-m-d, or use both --from and --to for a range.');
                return null;
            }
            try {
                $d = Carbon::parse($specificDate, $tz)->startOfDay();
                $this->line("Date: {$d->toDateString()} ({$tz}, single day).");
                return [$d];
            } catch (\Exception $e) {
                $this->error("Invalid --date: {$specificDate}");
                return null;
            }
        }

        $days  = max(1, (int) $this->option('days'));
        $dates = [];
        $today = Carbon::today($tz);
        for ($i = 1; $i <= $days; $i++) {
            $dates[] = $today->copy()->subDays($i);
        }
        if ($days === 1) {
            $this->line('Date: '.$dates[0]->toDateString()." ({$tz}, yesterday only — use --from/--to for a range).");
        } else {
            $this->line("Dates: last {$days} days ending ".$dates[0]->toDateString()." ({$tz}).");
        }
        return $dates;
    }
}
