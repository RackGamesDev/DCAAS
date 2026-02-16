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
     * De la autorizacion se encargan los middlewares
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Valida los datos de la peticion, mas informacion en la documentacion
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

    /**
     * Mensajes personalizados para las rutas en las que se use (se muestran en la respuesta)
     * @return array{fecha_creacion.prohibited: string, id.prohibited: string, permisos.prohibited: string, publicante.prohibited: string}
     */
    public function messages(): array
    {
        return [
            'id.prohibited' => 'No tienes permisos para modificar el ID.',
            'permisos.prohibited' => 'No tienes permisos para modificar el nivel de privilegios.',
            'publicante.prohibited' => 'No tienes permisos para modificar el estado de publicante.',
            'fecha_creacion.prohibited' => 'La fecha de creación es inmutable.',
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
            RespuestaAPI::fallo(422, 'Error en la validación de los campos, es posible que la contrasegna antigua no coincida o hayas puesto un nickname/email repetidos.', $validator->errors()->all())
        );
    }
}
