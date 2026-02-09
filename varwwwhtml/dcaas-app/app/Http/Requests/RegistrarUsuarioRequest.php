<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Responses\RespuestaAPI;
use Illuminate\Validation\Rules\Password;

class RegistrarUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Force permisos to 0 and remove fecha_creacion if sent in the body
        $this->merge([
            'permisos' => 0,
        ]);

        $this->offsetUnset('fecha_creacion');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // nickname: 4-16 chars, letters, numbers, and -._
            'nickname' => [
                'required',
                'string',
                'unique:users,nickname',
                'between:4,16',
                'regex:/^[A-Za-z0-9\-\._]+$/'
            ],

            // nombre: min 3 chars
            'nombre' => 'required|string|min:3',

            'email' => 'required|email|unique:users,email',

            // Strong password requirements
            'password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            // We validate it here to ensure it's present, but prepareForValidation fixed the value
            'permisos' => 'required|integer',
            //'permisos' => 'prohibited',
            'fecha_creacion' => 'prohibited',
            'id' => 'prohibited',

            'publicante' => 'required|boolean',
            'descripcion' => 'nullable|string|max:512',
            'url_foto' => 'nullable|url',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespuestaAPI::fallo(422, 'Error en la validación de los campos', $validator->errors()->all())
        );
    }
}
