<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HrmsEmployeeService
{
    private const HRMS_API_URL = 'https://app.draravindsivf.com/hrms/employee_details_api.php';

    private const HRMS_API_KEY = '3x@MpL3-K3Y-98fG_2025!';

    private const CACHE_KEY = 'hrms.employees.directory.v1';

    private const HRMS_CACHE_MINUTES = 10;

    public function all(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY);
        } else {
            $cached = Cache::get(self::CACHE_KEY);
            if (is_array($cached) && $cached !== []) {
                return $cached;
            }
        }

        $employees = $this->fetchFromApi();

        if ($employees !== []) {
            Cache::put(self::CACHE_KEY, $employees, now()->addMinutes(self::HRMS_CACHE_MINUTES));
        }

        return $employees;
    }

    public function find(string $empId): ?array
    {
        $empId = trim($empId);
        if ($empId === '') {
            return null;
        }
        foreach ($this->all() as $row) {
            if ((string) ($row['emp_id'] ?? '') === $empId
                || (string) ($row['id']     ?? '') === $empId) {
                return $row;
            }
        }

        return null;
    }

    private function fetchFromApi(): array
    {
        try {
            $response = Http::timeout(15)
                ->connectTimeout(8)
                ->withoutVerifying()
                ->retry(2, 250)
                ->get(self::HRMS_API_URL, ['api_key' => self::HRMS_API_KEY]);

            if (! $response->successful()) {
                Log::warning('HRMS API non-success', ['status' => $response->status()]);

                return [];
            }

            $payload = $response->json();
            $rows = is_array($payload) && isset($payload['data']) && is_array($payload['data'])
                ? $payload['data']
                : (is_array($payload) ? $payload : []);

            if ($rows && isset($rows[0]) && is_array($rows[0])) {
                $first = $rows[0];
                $looksLikeHeader = isset($first['fullname'])
                    && strtolower((string) $first['fullname']) === 'fullname';
                if ($looksLikeHeader) {
                    array_shift($rows);
                }
            }

            $out = [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $name = trim((string) (
                    $row['fullname']
                    ?? $row['full_name']
                    ?? $row['name']
                    ?? $row['employee_name']
                    ?? ''
                ));
                if ($name === '') {
                    continue;
                }
                $empId = trim((string) (
                    $row['employment_id']
                    ?? $row['emp_id']
                    ?? $row['employee_id']
                    ?? $row['id']
                    ?? ''
                ));
                $out[] = [
                    'id'     => $empId !== '' ? $empId : $name,
                    'name'   => $name,
                    'emp_id' => $empId,
                ];
            }

            return $out;
        } catch (\Throwable $e) {
            Log::warning('HRMS API failure', ['msg' => $e->getMessage()]);

            return [];
        }
    }
}
