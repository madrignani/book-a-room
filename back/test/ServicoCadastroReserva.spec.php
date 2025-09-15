<?php


use App\Utils\RecomposicaoBD;
use App\Config\Conexao;
use App\Excecao\AutenticacaoException;
use App\Excecao\DominioException;
use App\Repositorio\RepositorioTipoEspacoBDR;
use App\Repositorio\RepositorioEspacoBDR;
use App\Repositorio\RepositorioUsuarioBDR;
use App\Repositorio\RepositorioReservaBDR;
use App\Transacao\TransacaoPDO;
use App\Servico\ServicoCadastroReserva;


describe( 'ServicoCadastroReserva', function () {

    beforeAll( function() {
        $this->pdo = Conexao::conectar();
        $this->recomposicaoBD = new RecomposicaoBD($this->pdo);
        $this->transacao = new TransacaoPDO($this->pdo);
        $this->repositorioTipoEspaco = new RepositorioTipoEspacoBDR($this->pdo);
        $this->repositorioEspaco = new RepositorioEspacoBDR($this->pdo);
        $this->repositorioUsuario = new RepositorioUsuarioBDR($this->pdo);
        $this->repositorioReserva = new RepositorioReservaBDR($this->pdo);
        $this->servico = new ServicoCadastroReserva (
            $this->transacao, $this->repositorioTipoEspaco, $this->repositorioEspaco,
            $this->repositorioUsuario, $this->repositorioReserva
        );
        $this->tecnicoGestor = 1;
        $this->tecnicoNaoGestor = 2;
        $this->professorNaoGestor = 4;
        $this->aluno = 5;
        $this->auditorio = 8;
        $this->reuniao = 3;
        $this->dataInicioReservaExistente = '2025-11-05 15:00:00';
        $this->dataFimReservaExistente = '2025-11-05 17:00:00';
    } );

    AfterAll( function() {
        $this->recomposicaoBD->redefinir();
    } );

    it( "Deve permitir aluno reservar sala de reunião por menos de 2 horas", function () {
        $executar = function() {
            $inicio = new DateTime('2026-10-10 10:00:00');
            $fim = new DateTime('2026-10-10 11:59:00');
            $this->servico->cadastrarReserva(
                $this->reuniao, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->aluno, 'ALUNO', null, null
            );
        };
        expect($executar)->not->toThrow();
    } );

    it( "Deve permitir professor reservar auditório por menos de 5 horas", function () {
        $executar = function () {
            $inicio = new DateTime('2026-10-12 10:00:00');
            $fim = new DateTime('2026-10-12 14:59:00');
            $this->servico->cadastrarReserva(
                $this->auditorio, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->professorNaoGestor, 'FUNCIONARIO', 'PROFESSOR', true
            );
        };
        expect($executar)->not->toThrow();
    } );

    it( "Deve permitir funcionário com gestão reservar por quanto tempo quiser no mesmo dia", function () {
        $executar = function () {
            $inicio = new DateTime('2026-10-18 00:01:00');
            $fim = new DateTime('2026-10-18 23:59:00');
            $this->servico->cadastrarReserva(
                $this->auditorio, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->tecnicoGestor, 'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->not->toThrow();
    } );

    it( "Deve bloquear aluno reservando auditório", function () {
        $executar = function() {
            $inicio = new DateTime('2026-10-11 10:00:00');
            $fim = new DateTime('2026-10-11 11:00:00');
            $this->servico->cadastrarReserva(
                $this->auditorio, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->aluno, 'ALUNO', null, null
            );
        };
        expect($executar)->toThrow(new AutenticacaoException());
    } );

    it( "Deve bloquear técnico sem gestão reservando auditório", function () {
        $executar = function() {
            $inicio = new DateTime('2026-10-13 10:00:00');
            $fim = new DateTime('2026-10-13 11:00:00');
            $this->servico->cadastrarReserva(
                $this->auditorio, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->tecnicoNaoGestor, 'FUNCIONARIO', 'TECNICO', false
            );
        };
        expect($executar)->toThrow(new AutenticacaoException());
    } );

    it( "Deve bloquear reserva com mais de 2 horas para aluno", function () {
        $executar = function() {
            $inicio = new DateTime('2026-10-14 10:00:00');
            $fim = new DateTime('2026-10-14 12:01:00');
            $this->servico->cadastrarReserva(
                $this->reuniao, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->aluno, 'ALUNO', null, null
            );
        };
        expect($executar)->toThrow(new AutenticacaoException());
    } );

    it( "Deve bloquear reserva para dias diferentes", function () {
        $executar = function() {
            $inicio = new DateTime('2026-10-15 10:00:00');
            $fim = new DateTime('2026-10-20 11:00:00');
            $this->servico->cadastrarReserva(
                $this->auditorio, $inicio->format('Y-m-d H:i:s'),$fim->format('Y-m-d H:i:s'),
                '', $this->tecnicoGestor, 'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->toThrow(new DominioException());
    } );

    it( "Deve bloquear reserva com espaço já reservado para janela de tempo", function () {
        $executar = function() {
            $inicio = new DateTime($this->dataInicioReservaExistente);
            $fim = new DateTime($this->dataFimReservaExistente);
            $this->servico->cadastrarReserva(
                $this->auditorio, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->tecnicoGestor, 'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->toThrow(new DominioException());
    } );

    it( "Deve bloquear reserva com data inicial no passado", function () {
        $executar = function () {
            $inicio = new DateTime('2025-01-01 10:00:00');
            $fim = new DateTime('2025-01-01 11:00:00');
            $this->servico->cadastrarReserva(
                $this->reuniao, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->tecnicoGestor, 'FUNCIONARIO', 'TECNICO', null
            );
        };
        expect($executar)->toThrow(new DominioException());
    } );

    it( "Deve bloquear reserva com justificativa maior que 200 caracteres", function () {
        $executar = function () {
            $inicio = new DateTime('2026-11-01 10:00:00');
            $fim = new DateTime('2026-11-01 11:00:00');
            $justificativa = str_repeat('a', 201);
            $this->servico->cadastrarReserva(
                $this->reuniao, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                $justificativa, $this->aluno, 'ALUNO', null, null
            );
        };
        expect($executar)->toThrow(new DominioException());
    } );

    it( "Deve bloquear reserva com data de início posterior à data de fim", function () {
        $executar = function () {
            $inicio = new DateTime('2026-11-03 15:00:00');
            $fim = new DateTime('2026-11-03 10:00:00');
            $this->servico->cadastrarReserva(
                $this->reuniao, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->aluno, 'ALUNO', null, null
            );
        };
        expect($executar)->toThrow(new DominioException());
    } );

    it( "Deve bloquear reserva para espaço inexistente", function () {
        $executar = function() {
        $inicio = new DateTime('2026-10-16 10:00:00');
        $fim = new DateTime('2026-10-16 11:00:00');
            $this->servico->cadastrarReserva(
                1000000, $inicio->format('Y-m-d H:i:s'), $fim->format('Y-m-d H:i:s'),
                '', $this->tecnicoGestor, 'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->toThrow(new DominioException());
    } );

} );


?>