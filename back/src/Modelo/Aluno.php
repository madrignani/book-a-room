<?php


namespace App\Modelo;


class Aluno extends Usuario {

    public function __construct(
        int $id,
        string $matricula,
        string $nome,
        string $email,
    ) {
        parent::__construct( $id, $matricula, $nome, $email );
    }

    public function getTipoUsuario(): string { return 'ALUNO'; }

    public function validar(): array {
        $problemas = parent::validar();
        return $problemas;
    }

}


?>