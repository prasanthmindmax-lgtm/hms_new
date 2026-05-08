<?php

namespace App\Http\Controllers;

use App\Services\HrmsEmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HrmsEmployeeController extends Controller
{
    public function __construct(private readonly HrmsEmployeeService $employees)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $rows = $this->employees->all($request->boolean('refresh'));

        return response()->json([
            'ok'      => count($rows) > 0,
            'data'    => $rows,
            'message' => $rows ? '' : 'Unable to reach the employee API right now.',
        ]);
    }
}
