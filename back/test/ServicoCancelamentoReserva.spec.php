<?php


use App\Utils\RecomposicaoBD;
use App\Config\Conexao;
use App\Excecao\AutenticacaoException;
use App\Excecao\DominioException;
use App\Repositorio\RepositorioUsuarioBDR;
use App\Repositorio\RepositorioReservaBDR;
use App\Transacao\TransacaoPDO;
use App\Servico\ServicoCancelamentoReserva;


describe( 'ServicoCancelamentoReserva', function () {

    beforeAll( function() {
        $this->pdo = Conexao::conectar();
        $this->recomposicaoBD = new RecomposicaoBD($this->pdo);
        $this->transacao = new TransacaoPDO($this->pdo);
        $this->repositorioUsuario = new RepositorioUsuarioBDR($this->pdo);
        $this->repositorioReserva = new RepositorioReservaBDR($this->pdo);
        $this->servico = new ServicoCancelamentoReserva(
            $this->transacao,
            $this->repositorioUsuario,
            $this->repositorioReserva
        );
        $this->gestor = 1;
        $this->naoGestor = 2;
        $this->aluno = 5;
        $this->reservaGestor = 11;
        $this->reservaOutroGestor = 1;
        $this->reservaNaoGestor = 3;
        $this->reservaAlunoA = 5;
        $this->reservaAlunoB = 7;
        $this->reservaCancelada = 16;
    } );

    AfterAll( function() {
        $this->recomposicaoBD->redefinir();
    } );

    it( "Deve bloquear aluno tentando cancelar reserva de outro usuário", function () {
        $executar = function () {
            $this->servico->cancelar(
                $this->reservaNaoGestor, $this->aluno,
                'ALUNO', null, null
            );
        };
        expect($executar)->toThrow(new AutenticacaoException());
    } );

    it( "Deve bloquear funcionário sem gestão tentando cancelar reserva de outro usuário", function () {
        $executar = function () {
            $this->servico->cancelar(
                $this->reservaAlunoA, $this->naoGestor,
                'FUNCIONARIO', 'TECNICO', false
            );
        };
        expect($executar)->toThrow(new AutenticacaoException());
    } );

    it( "Deve permitir aluno cancelar própria reserva", function () {
        $executar = function () {
            $this->servico->cancelar(
                $this->reservaAlunoA, $this->aluno,
                'ALUNO', null, null
            );
        };
        expect($executar)->not->toThrow();
    } );

    it( "Deve permitir funcionário com gestão cancelar própria reserva", function () {
        $executar = function () {
            $this->servico->cancelar(
                $this->reservaGestor, $this->gestor,
                'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->not->toThrow();
    } );

    it( "Deve permitir gestor cancelar reserva de aluno", function () {
        $executar = function () {
            $this->servico->cancelar(
                $this->reservaAlunoB, $this->gestor,
                'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->not->toThrow();
    } );

    it( "Deve permitir gestor cancelar reserva de funcionário sem gestão", function () {
        $executar = function () {
            $this->servico->cancelar(
                $this->reservaNaoGestor, $this->gestor,
                'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->not->toThrow();
    } );

    it( "Deve bloquear gestor tentando cancelar reserva de outro gestor", function () {
        $executar = function () {
            $this->servico->cancelar(
                $this->reservaOutroGestor, $this->gestor,
                'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->toThrow(new AutenticacaoException());
    } );

    it( "Deve bloquear cancelamento de reserva inexistente", function () {
        $executar = function () {
            $this->servico->cancelar(
                999999, $this->gestor,
                'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->toThrow(new DominioException());
    } );

    it( "Deve bloquear cancelamento de reserva já cancelada", function () {
        $executar = function () {
            $this->servico->cancelar(
                $this->reservaCancelada, $this->gestor,
                'FUNCIONARIO', 'TECNICO', true
            );
        };
        expect($executar)->toThrow(new DominioException());
    } );

} );


?>