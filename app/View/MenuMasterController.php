<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MenuMasterController extends Controller
{
    /** Page view */
    public function index(Request $request)
    {
        $admin = auth()->user();

        $mainMenus  = DB::table('menus')->where('main_menu', 1)->orderBy('menu_name')->get(['id', 'menu_name']);
        return view('superadmin.menu_master', compact('admin', 'mainMenus'));
    }

    /** DataTable AJAX list */
    public function list(Request $request)
    {
        $draw   = $request->input('draw');
        $start  = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value', '');

        $query = DB::table('menus');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('menu_name', 'like', "%{$search}%")
                  ->orWhere('route',     'like', "%{$search}%");
            });
        }

        $total    = $query->count();
        $records  = (clone $query)
            ->orderBy('main_menu', 'desc')
            ->orderBy('id')
            ->skip($start)
            ->take($length)
            ->get();

        // Attach parent name for sub-menus
        $mainMenuMap = DB::table('menus')
            ->where('main_menu', 1)
            ->pluck('menu_name', 'id');

        $data = $records->map(function ($row, $i) use ($start, $mainMenuMap) {
            $parentName = ($row->sub_menus && $row->main_menu == 0)
                ? ($mainMenuMap[$row->sub_menus] ?? "ID #{$row->sub_menus}")
                : '—';

            return [
                'DT_RowIndex' => $start + $i + 1,
                'id'          => $row->id,
                'menu_name'   => htmlspecialchars($row->menu_name),
                'route'       => htmlspecialchars($row->route ?? ''),
                'icon'        => htmlspecialchars($row->icon ?? ''),
                'active_ids'  => htmlspecialchars($row->active_ids ?? ''),
                'main_menu'   => $row->main_menu,
                'dropdown'    => $row->dropdown,
                'sub_menus'   => $row->sub_menus,
                'parent_name' => $parentName,
                'action'      => '',
            ];
        });

        return response()->json([
            'draw'            => intval($draw),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
        ]);
    }

    /** Store new menu */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'menu_name' => 'required|string|max:150',
            'route'     => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:100',
            'active_ids'=> 'nullable|string|max:100',
            'main_menu' => 'required|in:0,1',
            'dropdown'  => 'required|in:0,1',
            'sub_menus' => 'nullable|integer',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 400, 'errors' => $v->errors()->all()]);
        }

        $isMain   = intval($request->main_menu);
        $subMenus = ($isMain == 0 && $request->sub_menus) ? intval($request->sub_menus) : 0;

        DB::table('menus')->insert([
            'menu_name'  => trim($request->menu_name),
            'route'      => trim($request->route ?? ''),
            'icon'       => trim($request->icon ?? 'health.png'),
            'active_ids' => trim($request->active_ids ?? 'dashboard_color'),
            'main_menu'  => $isMain,
            'dropdown'   => ($isMain == 1) ? intval($request->dropdown) : 0,
            'sub_menus'  => $subMenus,
            'parent_id'  => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 200, 'message' => 'Menu created successfully.']);
    }

    /** Fetch single record for edit */
    public function show(int $id)
    {
        $menu = DB::table('menus')->find($id);
        if (!$menu) {
            return response()->json(['status' => 404, 'message' => 'Not found.']);
        }
        return response()->json(['status' => 200, 'menu' => $menu]);
    }

    /** Update menu */
    public function update(Request $request, int $id)
    {
        $v = Validator::make($request->all(), [
            'menu_name' => 'required|string|max:150',
            'route'     => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:100',
            'active_ids'=> 'nullable|string|max:100',
            'main_menu' => 'required|in:0,1',
            'dropdown'  => 'required|in:0,1',
            'sub_menus' => 'nullable|integer',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 400, 'errors' => $v->errors()->all()]);
        }

        $isMain   = intval($request->main_menu);
        $subMenus = ($isMain == 0 && $request->sub_menus) ? intval($request->sub_menus) : 0;

        DB::table('menus')->where('id', $id)->update([
            'menu_name'  => trim($request->menu_name),
            'route'      => trim($request->route ?? ''),
            'icon'       => trim($request->icon ?? 'health.png'),
            'active_ids' => trim($request->active_ids ?? 'dashboard_color'),
            'main_menu'  => $isMain,
            'dropdown'   => ($isMain == 1) ? intval($request->dropdown) : 0,
            'sub_menus'  => $subMenus,
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 200, 'message' => 'Menu updated successfully.']);
    }

    /** Delete menu */
    public function destroy(int $id)
    {
        // Prevent deleting a parent that has children
        $childCount = DB::table('menus')->where('sub_menus', $id)->count();
        if ($childCount > 0) {
            return response()->json([
                'status'  => 400,
                'message' => "Cannot delete: this menu has {$childCount} sub-menu(s) linked to it. Remove sub-menus first.",
            ]);
        }

        DB::table('menus')->delete($id);
        return response()->json(['status' => 200, 'message' => 'Menu deleted successfully.']);
    }
}
