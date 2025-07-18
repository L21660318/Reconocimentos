<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'tipo' => 'nullable|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'institution_id' => 'nullable|exists:institutions,id',
            'imagen' => 'nullable|image|max:2048',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ];
    }

}
