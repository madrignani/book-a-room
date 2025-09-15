<?php


namespace App\DTO;


class UsuarioDTO {

    private int $id;
    private string $matricula;
    private string $nome;
    private string $tipoUsuario;
    private ?string $tipoFuncionario;
    private ?bool $cargoGestao;

    public function __construct(
        int $id,
        string $matricula,
        string $nome,
        string $tipoUsuario,
        ?string $tipoFuncionario = null,
        ?bool $cargoGestao = null
    ) {
        $this->id = $id;
        $this->matricula = $matricula;
        $this->nome = $nome;
        $this->tipoUsuario = $tipoUsuario;
        $this->tipoFuncionario = $tipoFuncionario;
        $this->cargoGestao = $cargoGestao;
    }

    public function arrayDados(): array {
        return [
            'id' => $this->id,
            'matricula' => $this->matricula,
            'nome' => $this->nome,
            'tipoUsuario' => $this->tipoUsuario,
            'tipoFuncionario' => $this->tipoFuncionario,
            'cargoGestao' => $this->cargoGestao,
        ];
    }
    
}


?>