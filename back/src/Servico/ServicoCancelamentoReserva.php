<?php

namespace App\Servico;
use App\Transacao\Transacao;
use App\Excecao\AutenticacaoException;
use App\Excecao\DominioException;
use App\Repositorio\RepositorioUsuario;
use App\Repositorio\RepositorioReserva;
use App\Modelo\EstadoReserva;
use Throwable;

class ServicoCancelamentoReserva {
    
    private Transacao $transacao;
    private RepositorioUsuario $repositorioUsuario;
    private RepositorioReserva $repositorioReserva;

    public function __construct (
        Transacao $transacao,
        RepositorioUsuario $repositorioUsuario,
        RepositorioReserva $repositorioReserva
    ) {
        $this->transacao = $transacao;
        $this->repositorioUsuario = $repositorioUsuario;
        $this->repositorioReserva = $repositorioReserva;
    }

    public function cancelar( int $idReserva, int $idUsuario, string $tipoUsuario, ?string $tipoFuncionario, ?bool $cargoGestao ): void {
        $this->transacao->iniciar();
        try {
            $reserva = $this->repositorioReserva->buscarPorId($idReserva);
            if (!$reserva) {
                throw DominioException::comProblemas( ['Reserva não encontrada para efetuar o cancelamento.'] );
            }
            if ( $reserva['estado'] == EstadoReserva::CANCELADA->value ){
                throw DominioException::comProblemas( ['Reserva já cancelada.'] );
            }
            $this->verificarPermissaoCancelamento( $reserva['id_usuario'], $idUsuario, $tipoUsuario, $tipoFuncionario, $cargoGestao );
            $this->repositorioReserva->atualizarEstado( $idReserva, EstadoReserva::CANCELADA->value );
            $this->transacao->finalizar();
        } catch (Throwable $erro) {
            $this->transacao->desfazer();
            throw $erro;
        }
    }

    private function verificarPermissaoCancelamento( int $idUsuarioReserva, int $idUsuario, string $tipoUsuario, ?string $tipoFuncionario, ?bool $cargoGestao ): void {
        if ($idUsuarioReserva === $idUsuario) {
            return;
        }        
        if ($tipoUsuario === 'ALUNO') {
            throw throw new AutenticacaoException( 'Permissão negada para cancelar esta Reserva.' );
        }
        if (!$cargoGestao) {
            throw throw new AutenticacaoException( 'Permissão negada para cancelar esta Reserva.' );
        }
        $donoReserva = $this->repositorioUsuario->buscarPorId($idUsuarioReserva);
        if ($donoReserva['tipo_usuario'] === 'FUNCIONARIO' && $donoReserva['cargo_gestao']) {
            throw throw new AutenticacaoException( 'Permissão negada para cancelar esta Reserva.' );
        }
    }
    
}