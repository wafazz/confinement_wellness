<?php

namespace App\Http\Controllers\HQ;

use App\Http\Controllers\Controller;
use App\Models\SopMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SopMaterialController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(SopMaterial::with('uploader'))
                ->addColumn('uploader_name', fn($row) => $row->uploader->name ?? '-')
                ->addColumn('file_link', function ($row) {
                    $ext = pathinfo($row->file_path, PATHINFO_EXTENSION);
                    $icon = match (strtolower($ext)) {
                        'pdf' => 'fa-file-pdf text-danger',
                        'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-info',
                        'mp4', 'mov', 'avi' => 'fa-file-video text-warning',
                        'doc', 'docx' => 'fa-file-word text-primary',
                        default => 'fa-file text-secondary',
                    };
                    return '<a href="' . Storage::url($row->file_path) . '" target="_blank"><i class="fas ' . $icon . ' me-1"></i>' . strtoupper($ext) . '</a>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('hq.sop-materials.edit', $row) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="' . route('hq.sop-materials.destroy', $row) . '" class="d-inline" onsubmit="return confirm(\'Delete this material?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>';
                })
                ->rawColumns(['file_link', 'action'])
                ->make(true);
        }

        return view('hq.sop-materials.index');
    }

    public function create()
    {
        return view('hq.sop-materials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:20480', // 20MB
        ]);

        $path = $request->file('file')->store('sop-materials', 'public');

        SopMaterial::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('hq.sop-materials.index')->with('success', 'Material uploaded.');
    }

    public function edit(SopMaterial $sop_material)
    {
        return view('hq.sop-materials.edit', ['material' => $sop_material]);
    }

    public function update(Request $request, SopMaterial $sop_material)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($sop_material->file_path);
            $data['file_path'] = $request->file('file')->store('sop-materials', 'public');
        }

        $sop_material->update($data);

        return redirect()->route('hq.sop-materials.index')->with('success', 'Material updated.');
    }

    public function destroy(SopMaterial $sop_material)
    {
        Storage::disk('public')->delete($sop_material->file_path);
        $sop_material->delete();

        return back()->with('success', 'Material deleted.');
    }
}
