<?php


namespace App\DTO;


class ReservaDTO {

    private int $id;
    private int $idUsuario;
    private string $nomeUsuario;
    private int $idTipoEspaco;
    private string $nomeTipoEspaco;
    private int $idEspaco;
    private string $nomeEspaco;
    private string $inicio;
    private string $fim;
    private string $justificativa;
    private string $estado;

    public function __construct(
        int $id,
        int $idUsuario,
        string $nomeUsuario,
        int $idTipoEspaco,
        string $nomeTipoEspaco,
        int $idEspaco,
        string $nomeEspaco,
        string $inicio,
        string $fim,
        string $justificativa,
        string $estado
    ) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->nomeUsuario = $nomeUsuario;
        $this->idTipoEspaco = $idTipoEspaco;
        $this->nomeTipoEspaco = $nomeTipoEspaco;
        $this->idEspaco = $idEspaco;
        $this->nomeEspaco = $nomeEspaco;
        $this->inicio = $inicio;
        $this->fim = $fim;
        $this->justificativa = $justificativa;
        $this->estado = $estado;
    }

    public function arrayDados(): array {
        return [
            'id' => $this->id,
            'idUsuario' => $this->idUsuario,
            'nomeUsuario' => $this->nomeUsuario,
            'idTipoEspaco' => $this->idTipoEspaco,
            'nomeTipoEspaco' => $this->nomeTipoEspaco,
            'idEspaco' => $this->idEspaco,
            'nomeEspaco' => $this->nomeEspaco,
            'inicio' => $this->inicio,
            'fim' => $this->fim,
            'justificativa' => $this->justificativa,
            'estado' => $this->estado,
        ];
    }

}


?>