<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Responses\RespuestaAPI;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CrearEncuestaRequest extends FormRequest
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

            'anonimo' => 'required|boolean',
            'publico' => 'required|boolean',
            'votacion' => 'required|boolean',
            'nombre' => 'required|string|min:3|unique:encuestas,nombre',
            'descripcion' => 'nullable|string|max:512',
            'certificacion' => 'nullable|string|max:128',
            'url_foto' => 'nullable|url',
            /*'id_usuario' => [
                'required',
                Rule::regex('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i'), // UUID v4 regex
                function ($attribute, $value, $fail) {
                    if (!DB::table('users')->where('id', $value)->exists()) {
                        $fail("{$attribute} is not associated with any user.");
                    }
                },
            ],*/

            'id' => 'prohibited',
            'fecha_creacion' => 'prohibited',
            'fecha_inicio' => 'prohibited',
            'fecha_fin' => 'prohibited',
            'estado' => 'prohibited',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespuestaAPI::fallo(422, 'Error en la validación de los campos', $validator->errors()->all())
        );
    }
}
