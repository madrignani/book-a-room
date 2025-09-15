<?php


namespace App\DTO;


class TipoEspacoDTO {

    private int $id;
    private string $nome;

    public function __construct(int $id, string $nome) {
        $this->id = $id;
        $this->nome = $nome;
    }

    public function arrayDados(): array {
        return [
            'id' => $this->id,
            'nome' => $this->nome
        ];
    }
    
}


?>