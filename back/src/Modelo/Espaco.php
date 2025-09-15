<?php


namespace App\Modelo;


class Espaco {

    private int $id;
    private string $codigoSala;
    private string $nome;
    private TipoEspaco $tipoEspaco;

    public function __construct(
        int $id,
        string $codigoSala,
        string $nome,
        TipoEspaco $tipoEspaco
    ) {
        $this->id = $id;
        $this->codigoSala = $codigoSala;
        $this->nome = $nome;
        $this->tipoEspaco = $tipoEspaco;
    }

    public function getId(): int { return $this->id; }
    public function getCodigoSala(): string { return $this->codigoSala; }
    public function getNome(): string { return $this->nome; }
    public function getTipoEspaco(): TipoEspaco { return $this->tipoEspaco; }

}


?>