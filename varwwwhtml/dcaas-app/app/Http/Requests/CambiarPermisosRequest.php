<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Responses\RespuestaAPI;
use Illuminate\Validation\Rule;
use App\Enums\PermisosUsuario;

class CambiarPermisosRequest extends FormRequest
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
        return [
            'id' => 'required|string|min:36|max:36',
            'permisos' => ['required', Rule::enum(PermisosUsuario::class)]
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
            RespuestaAPI::fallo(422, 'Error en la validación de los campos.', $validator->errors()->all())
        );
    }
}
