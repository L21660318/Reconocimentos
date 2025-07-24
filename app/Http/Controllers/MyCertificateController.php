<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Certificate;

class MyCertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('event')
            ->where('user_id', auth()->id())
            ->get();

        return Inertia::render('MyCertificates/Index', [
            'certificates' => $certificates,
            'title' => 'Mis Certificados',
        ]);
    }
}
