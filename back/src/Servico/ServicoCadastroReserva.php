<?php


namespace App\Servico;
use App\Transacao\Transacao;
use App\Excecao\DominioException;
use App\Excecao\AutenticacaoException;
use App\Repositorio\RepositorioTipoEspaco;
use App\Repositorio\RepositorioEspaco;
use App\Repositorio\RepositorioUsuario;
use App\Repositorio\RepositorioReserva;
use App\Modelo\Usuario;
use App\Modelo\TipoFuncionario;
use App\Modelo\Funcionario;
use App\Modelo\Aluno;
use App\Modelo\TipoEspaco;
use App\Modelo\Espaco;
use App\Modelo\Reserva;
use App\Modelo\EstadoReserva;
use App\Dto\TipoEspacoDTO;
use App\Dto\EspacoDTO;
use Throwable;
use DateTime;


class ServicoCadastroReserva {
    
    private Transacao $transacao;
    private RepositorioTipoEspaco $repositorioTipoEspaco;
    private RepositorioEspaco $repositorioEspaco;
    private RepositorioUsuario $repositorioUsuario;
    private RepositorioReserva $repositorioReserva;

    public function __construct (
        Transacao $transacao,
        RepositorioTipoEspaco $repositorioTipoEspaco,
        RepositorioEspaco $repositorioEspaco,
        RepositorioUsuario $repositorioUsuario,
        RepositorioReserva $repositorioReserva
    ) {
        $this->transacao = $transacao;
        $this->repositorioTipoEspaco = $repositorioTipoEspaco;
        $this->repositorioEspaco = $repositorioEspaco;
        $this->repositorioUsuario = $repositorioUsuario;
        $this->repositorioReserva = $repositorioReserva;
    }

    public function buscarTiposEspaco(string $tipoUsuario, ?string $tipoFuncionario, ?bool $cargoGestao): array {
        $todos = $this->repositorioTipoEspaco->buscarTodos();
        if ( empty($todos) ) {
            return [];
        }
        $filtrados = $this->filtrarTiposEspaco($todos, $tipoUsuario, $tipoFuncionario, $cargoGestao);
        $dto = [];
        foreach ($filtrados as $tipo) {
            $i = new TipoEspacoDTO( $tipo['id'], $tipo['nome'] );
            $dto[] = $i->arrayDados();
        }
        return $dto;
    }

    private function filtrarTiposEspaco(array $todos, string $tipoUsuario, ?string $tipoFuncionario, ?bool $cargoGestao): array {
        switch ($tipoUsuario) {
            case 'ALUNO':
                $reuniao = array_filter( $todos, function($tipo) {
                    return $tipo['nome'] === 'Sala de Reunião';
                } );
                return $reuniao;
            case 'FUNCIONARIO':
                if ($tipoFuncionario === 'PROFESSOR' || $cargoGestao) {
                    return $todos;
                } else {
                    $reuniao = array_filter( $todos, function($tipo) {
                        return $tipo['nome'] === 'Sala de Reunião';
                    } );
                    return $reuniao;
                }
            default:
                return [];
        }
    }

    public function buscarEspacosPorTipo(int $idTipoEspaco): array {
        $espacos = $this->repositorioEspaco->buscarPorTipo($idTipoEspaco);
        if ( empty($espacos) ) {
            return [];
        }
        $dto = [];
        foreach ($espacos as $espaco) {
            $i = new EspacoDTO( $espaco['id'], $espaco['codigo_sala'], $espaco['nome'], $espaco['id_tipo'] );
            $dto[] = $i->arrayDados();
        }
        return $dto;
    }

    public function cadastrarReserva( int $idEspaco, string $inicio, string $fim, ?string $justificativa, int $idUsuario, string $tipoUsuario, ?string $tipoFuncionario, ?bool $cargoGestao ): void {
        $this->transacao->iniciar();
        try {
            $dadosEspaco = $this->repositorioEspaco->buscarPorId($idEspaco);
            if (!$dadosEspaco) {
                throw DominioException::comProblemas( ['Espaço não encontrado para o cadastro da Reserva.'] );
            }
            $dadosTipoEspaco = $this->repositorioTipoEspaco->buscarPorId( $dadosEspaco['id_tipo'] );
            if (!$dadosTipoEspaco) {
                throw DominioException::comProblemas( ['Tipo de Espaço não encontrado para o cadastro da Reserva.'] );
            }
            $inicioData = new DateTime($inicio);
            $fimData = new DateTime($fim);
            if ( $inicioData->format('Y-m-d') !== $fimData->format('Y-m-d') ) {
                throw DominioException::comProblemas( ['Reserva deve iniciar e terminar no mesmo dia.'] );
            }
            $this->verificarPermissaoEspaco( $dadosTipoEspaco['nome'], $tipoUsuario, $tipoFuncionario, $cargoGestao );
            $duracao = $this->calcularDuracaoEmHoras($inicioData, $fimData);
            $this->verificarPermissaoHoras( $duracao, $tipoUsuario, $tipoFuncionario, $cargoGestao ); 
            if ( $this->repositorioReserva->existeReservaEspaco($idEspaco, $inicioData->format('Y-m-d H:i:s'), $fimData->format('Y-m-d H:i:s')) ) {
                throw DominioException::comProblemas( ["Espaço já possui Reserva para o período selecionado."] );
            }
            $reserva = $this->criarObjetoReserva( $idEspaco, $inicioData, $fimData, $justificativa, $idUsuario, $dadosTipoEspaco, $dadosEspaco, $duracao );
            $this->repositorioReserva->salvar(
                $reserva->getUsuario()->getId(),
                $reserva->getEspaco()->getId(),
                ( new DateTime() )->format('Y-m-d H:i:s'),
                $inicioData->format('Y-m-d H:i:s'),
                $fimData->format('Y-m-d H:i:s'),
                $reserva->getJustificativa(),
                $reserva->getEstado()->value
            );
            $this->transacao->finalizar();
        } catch (Throwable $erro) {
            $this->transacao->desfazer();
            throw $erro;
        }
    }

    private function verificarPermissaoEspaco( string $nomeTipoEspaco, string $tipoUsuario, ?string $tipoFuncionario, ?bool $cargoGestao ): void {
        $permitido = false;
        switch ($tipoUsuario) {
            case 'ALUNO':
                $permitido = ( $nomeTipoEspaco === 'Sala de Reunião' );
                break;
            case 'FUNCIONARIO':
                if ($tipoFuncionario === 'PROFESSOR' || $cargoGestao) {
                    $permitido = true;
                } else {
                    $permitido = ( $nomeTipoEspaco === 'Sala de Reunião' );
                }
                break;
            default:
                $permitido = false;
        }
        if (!$permitido) {
            throw new AutenticacaoException( 'Tipo de Espaço não permitido para o cadastro da Reserva.' );
        }
    }

    private function calcularDuracaoEmHoras(DateTime $inicioData, DateTime $fimData): int {
        $inicioTimestamp = $inicioData->getTimestamp();
        $fimTimestamp = $fimData->getTimestamp();
        $periodoEmSeg = $fimTimestamp - $inicioTimestamp;
        $periodoEmHoras = ( (int)(ceil($periodoEmSeg / 3600)) );
        return $periodoEmHoras;
    }

    private function verificarPermissaoHoras( int $horas, string $tipoUsuario, ?string $tipoFuncionario, ?bool $cargoGestao): void {
        $limite = 0;
        switch ($tipoUsuario) {
            case 'ALUNO':
                $limite = 2;
                break;
            case 'FUNCIONARIO':
                if ($cargoGestao){
                    $limite = 24;
                } else {
                    $limite = 5;
                }
                break;
            default:
                $limite = 0;
        } 
        if ($horas > $limite) {
            throw new AutenticacaoException( 'Quantidade de horas excedida para o cadastro da Reserva.' );
        }
    }

    private function criarObjetoReserva( int $idEspaco, DateTime $inicioData, DateTime $fimData, ?string $justificativa, int $idUsuario, array $dadosTipoEspaco, array $dadosEspaco, int $duracao ): Reserva {
        $dadosUsuario = $this->repositorioUsuario->buscarPorId($idUsuario);
        if ( $dadosUsuario['tipo_usuario'] === 'FUNCIONARIO' ) {
            $usuario = new Funcionario(
                ( (int) $dadosUsuario['id'] ),
                $dadosUsuario['matricula'],
                $dadosUsuario['nome'],
                $dadosUsuario['email'],
                ( TipoFuncionario::from($dadosUsuario['tipo_funcionario']) ),
                ( (bool) $dadosUsuario['cargo_gestao'] )
            );
        } else {
            $usuario = new Aluno(
                ( (int) $dadosUsuario['id'] ),
                $dadosUsuario['matricula'],
                $dadosUsuario['nome'],
                $dadosUsuario['email'],
            );
        }
        $problemasUsuario = $usuario->validar();
        if ( !empty($problemasUsuario) ) {
            throw DominioException::comProblemas( $problemasUsuario );
        }
        $tipoEspaco = new TipoEspaco(
            ( (int) $dadosTipoEspaco['id'] ),
            $dadosTipoEspaco['nome']
        );
        $espaco = new Espaco(
            ( (int) $dadosEspaco['id'] ),
            $dadosEspaco['codigo_sala'],
            $dadosEspaco['nome'],
            $tipoEspaco,
        );
        $reserva = new Reserva(
            0,
            $usuario,
            $espaco,
            new DateTime(),
            $inicioData,
            $fimData,
            $justificativa,
            EstadoReserva::MARCADA
        );
        $problemasReserva = $reserva->validar();
        if ( !empty($problemasReserva) ) {
            throw DominioException::comProblemas( $problemasReserva );
        }
        return $reserva;
    }

}


?>