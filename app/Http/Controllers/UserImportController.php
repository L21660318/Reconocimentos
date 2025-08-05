<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserImportController extends Controller
{
    public function import(Request $request, $eventId)
    {
        $request->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new UsersImport($eventId), $request->file('excelFile'));
            
            return back()->with('success', 'Usuarios importados correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
    public function downloadTemplate()
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_usuarios.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['nombre', 'email', 'password (opcional)', 'area_conocimiento (opcional)', 'institucion (opcional)'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}