<?php


namespace App\Repositorio;


interface RepositorioEspaco {

    public function buscarPorId(int $id): ?array;
    public function buscarPorTipo(int $idTipoEspaco): array;
    public function buscarPorSala(string $codigo): ?array;

}


?>