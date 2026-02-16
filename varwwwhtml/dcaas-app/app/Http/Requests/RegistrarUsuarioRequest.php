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
     * De la autorizacion se encargan los middlewares
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'permisos' => 0,
        ]);
        $this->offsetUnset('fecha_creacion');
    }

    /**
     * Valida los datos de la peticion, mas informacion en la documentacion
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nickname' => [
                'required',
                'string',
                'unique:users,nickname',
                'between:4,16',
                'regex:/^[A-Za-z0-9\-\._]+$/'
            ],
            'nombre' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'permisos' => 'required|integer',
            'fecha_creacion' => 'prohibited',
            'id' => 'prohibited',
            'publicante' => 'required|boolean',
            'descripcion' => 'nullable|string|max:512',
            'url_foto' => 'nullable|url',
        ];
    }

    /**
     * Devuelve una respuesta fallida en caso de que los datos no sean validos
     * @param Validator $validator
     * @throws HttpResponseException
     * @return never
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            RespuestaAPI::fallo(422, 'Error en la validación de los campos', $validator->errors()->all())
        );
    }
}
