<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProibirTransferenciaParaPropriaContaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value == auth()->user()->pessoa_id){
            $fail("Error");
        }
    }
}
