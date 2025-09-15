<?php


use App\Config\Conexao;
use App\Excecao\DominioException;
use App\Repositorio\RepositorioTipoEspacoBDR;
use App\Repositorio\RepositorioEspacoBDR;
use App\Repositorio\RepositorioUsuarioBDR;
use App\Repositorio\RepositorioReservaBDR;
use App\Transacao\TransacaoPDO;
use App\Servico\ServicoListagemReserva;


describe('ServicoListagemReserva', function () {

    beforeAll( function () {
        $this->pdo = Conexao::conectar();
        $this->repositorioTipoEspaco = new RepositorioTipoEspacoBDR($this->pdo);
        $this->repositorioEspaco = new RepositorioEspacoBDR($this->pdo);
        $this->repositorioUsuario = new RepositorioUsuarioBDR($this->pdo);
        $this->repositorioReserva = new RepositorioReservaBDR($this->pdo);
        $this->servico = new ServicoListagemReserva(
            $this->repositorioTipoEspaco,
            $this->repositorioEspaco,
            $this->repositorioUsuario,
            $this->repositorioReserva
        );
    } );

    it( "Deve falhar quando data final for anterior à inicial para filtragem", function () {
        $executar = function () {
            $this->servico->validarData('2026-10-10', '2026-10-09');
        };
        expect($executar)->toThrow(new DominioException());
    } );

} );


?>