<?php


namespace App\Repositorio;


interface RepositorioTipoEspaco {

    public function buscarPorId(int $id): ?array;
    public function buscarTodos(): array;

}


?>