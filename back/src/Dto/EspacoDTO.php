<?php


namespace App\DTO;


class EspacoDTO {

    private int $id;
    private string $codigoSala;
    private string $nome;
    private int $idTipoEspaco;

    public function __construct(
        int $id,
        string $codigoSala,
        string $nome,
        int $idTipoEspaco
    ) {
        $this->id = $id;
        $this->codigoSala = $codigoSala;
        $this->nome = $nome;
        $this->idTipoEspaco = $idTipoEspaco;
    }

    public function arrayDados(): array {
        return [
            'id' => $this->id,
            'codigoSala' => $this->codigoSala,
            'nome' => $this->nome,
            'idTipoEspaco' => $this->idTipoEspaco,
        ];
    }

}


?>