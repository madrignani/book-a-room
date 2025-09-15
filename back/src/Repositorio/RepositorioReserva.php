<?php


namespace App\Repositorio;


interface RepositorioReserva {

    public function salvar(int $idUsuario, int $idEspaco, string $dataReserva, string $inicio, string $fim, ?string $justificativa, string $estado): void;
    public function buscarPorId(int $id): ?array;
    public function buscarPorPeriodo(string $inicio, string $fim, string $estado): array;
    public function buscarPorPeriodoETipo(string $inicio, string $fim, string $estado, int $idTipoEspaco): array;
    public function buscarPorPeriodoESala(string $inicio, string $fim, string $estado, string $codigoSala): array;
    public function buscarPorPeriodoEUsuario(string $inicio, string $fim, string $estado, int $idUsuario): array;
    public function existeReservaEspaco(int $idEspaco, string $inicio, string $fim): bool;
    public function atualizarEstado(int $idReserva, string $estado): void;
    
}


?>