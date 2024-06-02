<?php

namespace App\Helpers;

class HelpersFile
{
    public static function getConteudoArquivo($caminho): bool|string
    {
        $arquivo = base_path($caminho);

        return file_get_contents($arquivo);
    }
}