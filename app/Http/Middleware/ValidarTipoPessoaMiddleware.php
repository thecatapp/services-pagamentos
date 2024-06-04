<?php

namespace App\Http\Middleware;

use App\Enum\EnumTipoPessoa;
use App\Models\Pessoa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidarTipoPessoaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $Pessoa = Pessoa::where("id", auth()->user()->pessoa_id)->first();

        if ($Pessoa->tp_pessoa == EnumTipoPessoa::PessoaJuridica->value){
            return response()->json(
                [
                    'message' => 'Desculpe, mas pessoas jurídicas não são permitidas nesta área.'
                ], Response::HTTP_FORBIDDEN
            );

        }
        return $next($request);
    }
}
