<?php


namespace App\Repositorio;


interface RepositorioUsuario {

    public function buscarPorId(int $id): ?array;
    public function buscarPorMatriculaOuEmail(string $valor): ?array;
    public function buscarPorMatricula(string $matricula): ?array;
    public function verificarHash(int $id, string $hash): bool;

}


?>