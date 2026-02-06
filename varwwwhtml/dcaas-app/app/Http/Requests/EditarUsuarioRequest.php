<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Responses\RespuestaAPI;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EditarUsuarioRequest extends FormRequest
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
        $userId = $this->user()->id;

        return [
            'nickname' => [
                'sometimes',
                'string',
                'between:4,16',
                'regex:/^[A-Za-z0-9\-\._]+$/',
                Rule::unique('users')->ignore($userId)
            ],
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($userId)
            ],
            'nombre' => 'sometimes|string|min:3|max:255',
            'descripcion' => 'sometimes|string|max:512',
            'url_foto' => 'sometimes|url',

            'password' => [
                'sometimes',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],

            'oldPassword' => 'required_with:nickname,email,password|string',

            // Forbidden
            'id' => 'prohibited',
            'permisos' => 'prohibited',
            'publicante' => 'prohibited',
            'fecha_creacion' => 'prohibited',
            'token' => 'prohibited',
        ];
    }

    public function messages(): array
    {
        return [
            'id.prohibited' => 'No tienes permisos para modificar el ID.',
            'permisos.prohibited' => 'No tienes permisos para modificar el nivel de privilegios.',
            'publicante.prohibited' => 'No tienes permisos para modificar el estado de publicante.',
            'fecha_creacion.prohibited' => 'La fecha de creación es inmutable.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespuestaAPI::fallo(422, 'Error en la validación de los campos, es posible que la contrasegna antigua no coincida o hayas puesto un nickname/email repetidos.', $validator->errors()->all())
        );
    }
}
