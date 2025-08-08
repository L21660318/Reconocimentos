<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'tipo' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'institution_id' => 'nullable|exists:institutions,id',
            'imagen' => 'sometimes|image|mimes:jpeg,png,jpg|max:1024',
            'archivo_pdf' => 'sometimes|file|mimes:pdf|max:10240',
            
        ];
    }
}
