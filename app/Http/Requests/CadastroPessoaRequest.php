<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CadastroPessoaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    public function prepareForValidation(): void
    {
        $this->merge([
            'cpf_cnpj' => $this->removerCaracteresEspeciais($this->cpf_cnpj)
        ]);
    }

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
            'nm_pessoa' => ['required'],
            'cpf_cnpj' => [
                'required',
                'unique:pessoas,cpf_cnpj'
            ],
            'email' => ['required', 'unique:contatos,email'],
        ];
    }

    private function removerCaracteresEspeciais($valor)
    {
        $valor = trim($valor);
        return str_replace(['.', '-', '/'], "", $valor);
    }
}
