<?php


use App\Config\Conexao;
use App\Excecao\DominioException;
use App\Repositorio\RepositorioUsuarioBDR;
use App\Servico\ServicoAutenticacao;


describe( 'ServicoAutenticacao', function () {

    beforeAll( function () {
        $this->pdo = Conexao::conectar();
        $this->repositorioUsuario = new RepositorioUsuarioBDR($this->pdo);
        $this->servico = new ServicoAutenticacao($this->repositorioUsuario);
    } );

    it( "Deve mapear Aluno com dados corretos", function() {
        $executar = function () {
            $dados = [
                'id' => 1,
                'matricula' => '12AB',
                'nome' => 'Aluno Teste',
                'email' => 'aluno@acme.br',
                'tipo_usuario' => 'ALUNO',
                'tipo_funcionario' => null,
                'cargo_gestao' => null
            ];
            $usuario = $this->servico->mapearUsuario($dados);
        };
        expect($executar)->not->toThrow();
    } );

    it( "Deve mapear Funcionario com dados corretos", function() {
        $executar = function () {
            $dados = [
                'id' => 1,
                'matricula' => 'AB12',
                'nome' => 'Funcionario Teste',
                'email' => 'funcionario@acme.br',
                'tipo_usuario' => 'FUNCIONARIO',
                'tipo_funcionario' => 'TECNICO',
                'cargo_gestao' => true
            ];
            $usuario = $this->servico->mapearUsuario($dados);
        };
        expect($executar)->not->toThrow();
    } );

    it( "Deve falhar para nome inválido ao mapear usuário", function() {
        $executar = function () {
            $dados = [
                'id' => 1,
                'matricula' => 'AB12',
                'nome' => 'A',
                'email' => 'teste@acme.br',
                'tipo_usuario' => 'ALUNO',
                'tipo_funcionario' => null,
                'cargo_gestao' => null
            ];
            $this->servico->mapearUsuario($dados);
        };
        expect($executar)->toThrow(new DominioException());
    } );

    it( "Deve falhar para matrícula inválida ao mapear usuário", function() {
        $executar = function () {
            $dados = [
                'id' => 1,
                'matricula' => '123',
                'nome' => 'Aluno Teste',
                'email' => 'teste@acme.br',
                'tipo_usuario' => 'ALUNO',
                'tipo_funcionario' => null,
                'cargo_gestao' => null
            ];
            $this->servico->mapearUsuario($dados);
        };
        expect($executar)->toThrow(new DominioException());
    } );

    it( "Deve falhar para email não institucional ao mapear usuário", function() {
        $executar = function () {
            $dados = [
                'id' => 1,
                'matricula' => 'AB12',
                'nome' => 'Aluno Teste',
                'email' => 'teste@gmail.com',
                'tipo_usuario' => 'ALUNO',
                'tipo_funcionario' => null,
                'cargo_gestao' => null
            ];
            $this->servico->mapearUsuario($dados);
        };
        expect($executar)->toThrow(new DominioException());
    } );

} );


?>