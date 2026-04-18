<?php

namespace App\Http\Controllers;

use App\Models\LicenceDocumentCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LicenceDocumentCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $admin = auth()->user();
        $perPage = (int) $request->get('per_page', 10);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $items = LicenceDocumentCatalog::query()
            ->orderBy('level')
            ->orderBy('id')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);

        return view('superadmin.licence_documents.catalog', [
            'admin' => $admin,
            'items' => $items,
            'perPage' => $perPage,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->input('id');

        if (! empty($id)) {
            $item = LicenceDocumentCatalog::query()->find($id);
            if (! $item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'label' => 'required|string|max:500',
                'is_active' => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $item->update([
                'label' => $validator->validated()['label'],
                'is_active' => (bool) (int) $validator->validated()['is_active'],
            ]);

            $item->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Licence document slot updated successfully.',
                'item' => $item,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'level' => 'required|in:1,2',
            'label' => 'required|string|max:500',
            'is_active' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $level = (int) $data['level'];
        $documentKey = $this->uniqueDocumentKeyFromLabel($data['label'], $level);

        $item = LicenceDocumentCatalog::create([
            'document_key' => $documentKey,
            'label' => $data['label'],
            'level' => $level,
            'is_active' => (bool) (int) $data['is_active'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Licence document slot created successfully.',
            'item' => $item,
        ]);
    }

    /**
     * Stable snake_case key from the display name; ensures uniqueness per level.
     */
    private function uniqueDocumentKeyFromLabel(string $label, int $level): string
    {
        $base = Str::slug(trim($label), '_');
        if ($base === '') {
            $base = 'document';
        }
        $base = substr($base, 0, 100);

        $key = $base;
        $n = 1;
        while (LicenceDocumentCatalog::query()
            ->where('level', $level)
            ->where('document_key', $key)
            ->exists()) {
            $n++;
            $suffix = '_'.$n;
            $key = substr($base, 0, max(1, 120 - strlen($suffix))).$suffix;
        }

        return $key;
    }
}
