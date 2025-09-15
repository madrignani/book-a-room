<?php


namespace App\Modelo;
use DateTime;


class Reserva {

    private int $id;
    private Usuario $usuario;
    private Espaco $espaco;
    private DateTime $dataReserva;
    private DateTime $inicio;
    private DateTime $fim;
    private ?string $justificativa;
    private EstadoReserva $estado;

    public function __construct(
        int $id,
        Usuario $usuario,
        Espaco $espaco,
        DateTime $dataReserva,
        DateTime $inicio,
        DateTime $fim,
        ?string $justificativa,
        EstadoReserva $estado
    ) {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->espaco = $espaco;
        $this->dataReserva = $dataReserva;
        $this->inicio = $inicio;
        $this->fim = $fim;
        $this->justificativa = $justificativa;
        $this->estado = $estado;
    }

    public function getId(): int { return $this->id; }
    public function getUsuario(): Usuario { return $this->usuario; }
    public function getEspaco(): Espaco { return $this->espaco; }
    public function getDataReserva(): DateTime { return $this->dataReserva; }
    public function getInicio(): DateTime { return $this->inicio; }
    public function getFim(): DateTime { return $this->fim; }
    public function getJustificativa(): ?string { return $this->justificativa; }
    public function getEstado(): EstadoReserva { return $this->estado; }

    public function setEstado( EstadoReserva $estado ): void { $this->estado = $estado; }

    public function validar(): array {
        $problemas = [];
        if ( $this->inicio >= $this->fim ) {
            $problemas[] = "A data e hora de início da Reserva deve ser anterior à data e hora de fim.";
        }
        if ( $this->inicio < new DateTime() ) {
            $problemas[] = "A Reserva não pode se iniciar no passado.";
        }
        if ( ($this->justificativa!==null) && (mb_strlen($this->justificativa)>200) ) {
            $problemas[] = "A justificativa para Reserva deve ter no máximo 200 caracteres.";
        }
        return $problemas;
    }

}


?>