<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TipoPregunta;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Responses\RespuestaAPI;

class EstablecerPreguntasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /*
{
	"destructivo": true,
	"preguntas": [
		{
			"titulo": "di cosas",
			"opcional": true,
			"tipo": 0,
			"subtitulo": "aquello",
			"placeholder": "aaaaaa",
			"correcta": "bbbb",
			"descripcion": "alkdfhaslidfhasodfh"
		},
		{
			"titulo": "selecciona varias",
			"opcional": true,
			"contenido": ["aaa", "bbb", "ccc"],
			"tipo": 1,
			"subtitulo": "aquello",
			"placeholder": [1,2],
			"correcta": [0,1]
		},
		{
			"titulo": "selecciona una",
			"opcional": false,
			"contenido": ["aaa", "bbb", "ccc"],
			"tipo": 2,
			"subtitulo": "esto",
			"placeholder": 2,
			"correcta": 0,
			"descripcion": "alkdfhaslidfhasodfh"
		},
		{
			"titulo": "cuanto?",
			"opcional": true,
			"tipo": 3,
			"subtitulo": "aquello",
			"placeholder": 20.5,
			"correcta": -10
		}
	]
}
    */

    public static $separadorPreguntas = "¬";

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'preguntas' => 'required|array|min:0|max:127',
            'preguntas.*.id' => 'prohibited',
            'preguntas.*.titulo' => 'required|string|distinct|min:1|max:127',
            'preguntas.*.opcional' => 'required|boolean',
            'preguntas.*.tipo' => ['required', Rule::enum(TipoPregunta::class)],
            'preguntas.*.subtitulo' => 'nullable|string|max:255',
            'preguntas.*.contenido' => 'sometimes',
            'preguntas.*.placeholder' => 'sometimes|nullable',
            'preguntas.*.correcta' => 'sometimes|nullable',
            'preguntas.*.descripcion' => 'nullable|string|max:255',
            'destructivo' => 'required|boolean'
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                foreach ($this->input('preguntas', []) as $index => $pregunta) {
                    $tipo = (int) ($pregunta['tipo'] ?? -1);
                    $contenido = $pregunta['contenido'] ?? null;
                    $placeholder = $pregunta['placeholder'] ?? null;
                    $correcta = $pregunta['correcta'] ?? null;

                    switch ($tipo) {
                        case TipoPregunta::Desarrollar->value: // Desarrollar
                            $this->validateNoContenido($validator, $index, $pregunta, 'Desarrollar');
                            $this->validateScalar($validator, $index, 'string', $placeholder, 'placeholder');
                            $this->validateScalar($validator, $index, 'string', $correcta, 'correcta');
                            break;

                        case TipoPregunta::Check->value: // Check (Múltiple)
                            $count = $this->validateContenidoArray($validator, $index, $contenido);
                            $this->validateIndexCollection($validator, $index, $count, $placeholder, 'placeholder');
                            $this->validateIndexCollection($validator, $index, $count, $correcta, 'correcta');
                            break;

                        case TipoPregunta::Radio->value: // Radio (Única)
                            $count = $this->validateContenidoArray($validator, $index, $contenido);
                            $this->validateSingleIndex($validator, $index, $count, $placeholder, 'placeholder');
                            $this->validateSingleIndex($validator, $index, $count, $correcta, 'correcta');
                            break;

                        case TipoPregunta::Numero->value: // Numero
                            $this->validateNoContenido($validator, $index, $pregunta, 'Numero');
                            $this->validateScalar($validator, $index, 'numeric', $placeholder, 'placeholder');
                            $this->validateScalar($validator, $index, 'numeric', $correcta, 'correcta');
                            break;
                    }
                }
            }
        ];
    }


    public static function validateNoContenido($validator, $index, $pregunta, $tipoNombre)
    {
        if (array_key_exists('contenido', $pregunta))
            $validator->errors()->add("preguntas.$index.contenido", "El campo contenido no debe estar presente en preguntas de tipo $tipoNombre.");
    }

    private function validateContenidoArray($validator, $index, $contenido)
    {
        if (!is_array($contenido) || count($contenido) < 2) {
            $validator->errors()->add("preguntas.$index.contenido", "Debe ser un array con al menos 2 elementos.");
            return 0;
        }
        if (count($contenido) !== count(array_unique($contenido)))
            $validator->errors()->add("preguntas.$index.contenido", "Las opciones deben ser únicas.");
        foreach ($contenido as $i => $value) {
            if (!is_string($value) || strlen($value) < 1 || strlen($value) > 127) {
                $validator->errors()->add("preguntas.$index.contenido.$i", "Cada opción debe ser un texto de 1 a 127 caracteres.");
                continue;
            }
            if (str_contains($value, self::$separadorPreguntas))
                $validator->errors()->add("preguntas.$index.contenido.$i", "La opción no puede contener el carácter especial '" . self::$separadorPreguntas . "'.");
        }
        return count($contenido);
    }

    public static function validateScalar($validator, $index, $type, $value, $field)
    {
        if (is_null($value))
            return;
        if ($type === 'string' && !is_string($value))
            $validator->errors()->add("preguntas.$index.$field", "Debe ser una cadena de texto.");
        if ($type === 'numeric' && !is_numeric($value))
            $validator->errors()->add("preguntas.$index.$field", "Debe ser un número real.");
    }

    public static function validateSingleIndex($validator, $index, $max, $value, $field)
    {
        if (is_null($value))
            return;
        if (!is_int($value) || $value < 0 || $value >= $max)
            $validator->errors()->add("preguntas.$index.$field", "Debe ser un entero que represente un índice válido (0 a " . ($max - 1) . ").");
    }

    public static function validateIndexCollection($validator, $index, $max, $value, $field)
    {
        if (is_null($value))
            return;
        if (!is_array($value)) {
            $validator->errors()->add("preguntas.$index.$field", "Debe ser un array de índices.");
            return;
        }
        if (count($value) !== count(array_unique($value)))
            $validator->errors()->add("preguntas.$index.$field", "Los índices no pueden estar repetidos.");
        foreach ($value as $val)
            if (!is_int($val) || $val < 0 || $val >= $max)
                $validator->errors()->add("preguntas.$index.$field", "Contiene un índice inválido ($val).");
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(RespuestaAPI::fallo(422, 'Error en la validación de los campos, todas las preguntas deben seguir unas reglas.', $validator->errors()->all()));
    }
}
