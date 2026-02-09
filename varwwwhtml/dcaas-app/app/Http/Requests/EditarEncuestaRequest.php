<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditarEncuestaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'anonimo' => 'sometimes|boolean',
            'publico' => 'sometimes|boolean',
            'votacion' => 'sometimes|boolean',
            'nombre' => 'sometimes|string|min:3|unique:encuestas,nombre',
            'descripcion' => 'sometimes|string|max:512',
            'url_foto' => 'sometimes|url',

            'certificacion' => 'prohibited',
            'id' => 'prohibited',
            'fecha_creacion' => 'prohibited',
            'fecha_inicio' => 'prohibited',
            'fecha_fin' => 'prohibited',
            'estado' => 'prohibited',
        ];
    }
}
