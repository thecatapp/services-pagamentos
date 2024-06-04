<?php

namespace App\Http\Requests;

use App\Rules\ProibirTransferenciaParaPropriaContaRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferenciaRequest extends FormRequest
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
            "transferencias" =>["required"],
            "transferencias.*.valor" =>["required", "min:1", "numeric"],
            "transferencias.*.pessoa_id" =>["required", "exists:pessoas,id", "exists:saldos,pessoa_id", new ProibirTransferenciaParaPropriaContaRule()],
        ];
    }

    public function messages()
    {
        return [
            "transferencias.*.pessoa_id" => "Por favor, lembre-se de sempre informar a pessoa de destino e que o favorecido não pode ser você mesmo .",
            "transferencias.*.valor" => "Informe o campo valor, lembre-se o mesmo deve ser maior que zero .",
            "transferencias" => "Atributo Transferencias não enviado .",

        ];
    }
}
