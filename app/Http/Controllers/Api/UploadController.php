<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class UploadController extends Controller
{
    public function store(Request $request) {
        if ($request->hasFile('documents')) {
            return $this->storeDocuments($request);
        }
    }

    private function storeDocuments(Request $request) {
        $file = $request->file('documents');
        $name = md5($file->getClientOriginalName() . microtime());
        Storage::disk('public')->putFileAs(
            "herramientas/documentos/",
            $file,
            "$name.pdf"
        );
        return "public/storage/herramientas/documentos/$name.pdf";
    }
}
