<?php


namespace App\Modelo;


class Funcionario extends Usuario {

    private TipoFuncionario $tipoFuncionario;
    private bool $cargoGestao;

    public function __construct(
        int $id,
        string $matricula,
        string $nome,
        string $email,
        TipoFuncionario $tipoFuncionario,
        bool $cargoGestao
    ) {
        parent::__construct( $id, $matricula, $nome, $email );
        $this->tipoFuncionario = $tipoFuncionario;
        $this->cargoGestao = $cargoGestao;
    }

    public function getTipoUsuario(): string { return 'FUNCIONARIO'; }
    public function getTipoFuncionario(): TipoFuncionario { return $this->tipoFuncionario; }
    public function getCargoGestao(): bool { return $this->cargoGestao; }

    public function setCargoGestao( bool $valor ): void { $this->cargoGestao = $valor; }

    public function validar(): array {
        $problemas = parent::validar();
        return $problemas;
    }

}


?>