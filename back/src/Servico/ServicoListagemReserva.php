<?php


namespace App\Servico;
use App\Excecao\DominioException;
use App\Repositorio\RepositorioTipoEspaco;
use App\Repositorio\RepositorioEspaco;
use App\Repositorio\RepositorioUsuario;
use App\Repositorio\RepositorioReserva;
use App\Modelo\EstadoReserva;
use App\Dto\ReservaDTO;
use DateTime;


class ServicoListagemReserva {
    
    private RepositorioTipoEspaco $repositorioTipoEspaco;
    private RepositorioEspaco $repositorioEspaco;
    private RepositorioUsuario $repositorioUsuario;
    private RepositorioReserva $repositorioReserva;

    public function __construct (
        RepositorioTipoEspaco $repositorioTipoEspaco,
        RepositorioEspaco $repositorioEspaco,
        RepositorioUsuario $repositorioUsuario,
        RepositorioReserva $repositorioReserva
    ) {
        $this->repositorioTipoEspaco = $repositorioTipoEspaco;
        $this->repositorioEspaco = $repositorioEspaco;
        $this->repositorioUsuario = $repositorioUsuario;
        $this->repositorioReserva = $repositorioReserva;
    }

    public function validarData( ?string $inicio, ?string $fim): array {
        if ( empty($inicio) || empty($fim) ) {
            $hoje = new DateTime();
            if( $hoje->format('w') === '0' ){ 
                $inicio = $hoje->format('Y-m-d');
                $fim = ( new DateTime() )->modify('next saturday')->format('Y-m-d');
            } else {
                $inicio = ( new DateTime() )->modify('last sunday')->format('Y-m-d');
                $fim = ( new DateTime() )->modify('next saturday')->format('Y-m-d');
            }
        }
        if ($fim < $inicio) {
            throw DominioException::comProblemas( ['A data final não pode ser anterior à data inicial.'] );
        }
        $inicio .= ' 00:00:00';
        $fim .= ' 23:59:59';
        return [
            'inicio' => $inicio,
            'fim' => $fim
        ];
    }

    public function buscarReservas(string $inicio, string $fim, string $estado): array {
        $reservas = $this->repositorioReserva->buscarPorPeriodo($inicio, $fim, $estado);
        $reservasDTO = [];
        foreach ($reservas as $reserva) {
            $dadosUsuario = $this->repositorioUsuario->buscarPorId($reserva['id_usuario']);
            $dadosEspaco = $this->repositorioEspaco->buscarPorId($reserva['id_espaco']);
            $dadosTipoEspaco = $this->repositorioTipoEspaco->buscarPorId($dadosEspaco['id_tipo']);
            $dto = new ReservaDTO(
                ( (int)$reserva['id'] ),
                ( (int)$reserva['id_usuario'] ),
                $dadosUsuario['nome'],
                ( (int)$dadosTipoEspaco['id']) ,
                $dadosTipoEspaco['nome'],
                ( (int)$dadosEspaco['id'] ),
                $dadosEspaco['nome'],
                $reserva['inicio'],
                $reserva['fim'],
                $reserva['justificativa'] ?? '',
                $reserva['estado']
            );
            $reservasDTO[] = $dto->arrayDados();
        }
        return $reservasDTO;
    }

    public function buscarReservasTe(string $inicio, string $fim, string $estado, int $idTipoEspaco): array {
        $tipoEspaco = $this->repositorioTipoEspaco->buscarPorId($idTipoEspaco);
        if ( empty($tipoEspaco) ) {
            return [];
        }
        $reservas = $this->repositorioReserva->buscarPorPeriodoETipo($inicio, $fim, $estado, $idTipoEspaco);
        $reservasDTO = [];
        foreach ($reservas as $reserva) {
            $dadosUsuario = $this->repositorioUsuario->buscarPorId($reserva['id_usuario']);
            $dadosEspaco = $this->repositorioEspaco->buscarPorId($reserva['id_espaco']);
            $dto = new ReservaDTO(
                ( (int)$reserva['id'] ),
                ( (int)$reserva['id_usuario'] ),
                $dadosUsuario['nome'],
                ( (int)$tipoEspaco['id'] ),
                $tipoEspaco['nome'],
                ( (int)$dadosEspaco['id'] ),
                $dadosEspaco['nome'],
                $reserva['inicio'],
                $reserva['fim'],
                $reserva['justificativa'] ?? '',
                $reserva['estado']
            );
            $reservasDTO[] = $dto->arrayDados();
        }
        return $reservasDTO;
    }

    public function buscarReservasSala(string $inicio, string $fim, string $estado,  string $codigoSala): array {
        $espaco = $this->repositorioEspaco->buscarPorSala($codigoSala);
        if ( empty($espaco) ) {
            return [];
        }
        $reservas = $this->repositorioReserva->buscarPorPeriodoESala($inicio, $fim, $estado, $codigoSala);
        $reservasDTO = [];
        $dadosTipoEspaco = $this->repositorioTipoEspaco->buscarPorId($espaco['id_tipo']);
        foreach ($reservas as $reserva) {
            $dadosUsuario = $this->repositorioUsuario->buscarPorId($reserva['id_usuario']);
            $dto = new ReservaDTO(
                ( (int)$reserva['id'] ),
                ( (int)$reserva['id_usuario'] ),
                $dadosUsuario['nome'],
                ( (int)$dadosTipoEspaco['id'] ),
                $dadosTipoEspaco['nome'],
                ( (int)$espaco['id'] ),
                $espaco['nome'],
                $reserva['inicio'],
                $reserva['fim'],
                $reserva['justificativa'] ?? '',
                $reserva['estado']
            );
            $reservasDTO[] = $dto->arrayDados();
        }
        return $reservasDTO;
    }

    public function buscarReservasMatricula(string $inicio, string $fim, string $estado, string $matricula): array {
        $dadosUsuario = $this->repositorioUsuario->buscarPorMatricula($matricula);
        if (!$dadosUsuario) {
            return [];
        }
        $reservas = $this->repositorioReserva->buscarPorPeriodoEUsuario($inicio, $fim, $estado, $dadosUsuario['id']);
        $reservasDTO = [];
        foreach ($reservas as $reserva) {
            $dadosEspaco = $this->repositorioEspaco->buscarPorId($reserva['id_espaco']);
            $dadosTipoEspaco = $this->repositorioTipoEspaco->buscarPorId($dadosEspaco['id_tipo']);
            $dto = new ReservaDTO(
                ( (int)$reserva['id'] ),
                ( (int)$reserva['id_usuario'] ),
                $dadosUsuario['nome'],
                ( (int)$dadosTipoEspaco['id'] ),
                $dadosTipoEspaco['nome'],
                ( (int)$dadosEspaco['id'] ),
                $dadosEspaco['nome'],
                $reserva['inicio'],
                $reserva['fim'],
                $reserva['justificativa'] ?? '',
                $reserva['estado']
            );
            $reservasDTO[] = $dto->arrayDados();
        }
        return $reservasDTO;
    }

}


?>