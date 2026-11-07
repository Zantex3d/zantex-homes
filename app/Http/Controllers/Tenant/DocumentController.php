<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\DocumentFile;
use Illuminate\View\View;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $documents = DocumentFile::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tenant.documents', compact('documents'));
    }
}
