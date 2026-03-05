<?php

namespace App\Http\Controllers\Leader;

use App\Http\Controllers\Controller;
use App\Models\SopMaterial;

class SopMaterialController extends Controller
{
    public function index()
    {
        $materials = SopMaterial::with('uploader')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('leader.sop-materials.index', compact('materials'));
    }
}
