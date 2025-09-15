import { ErroGestor } from '../infra/erro_gestor.ts';
import type { VisaoListagemReserva } from '../visao/visao_listagem_reserva.ts';
import { GestorListagemReserva } from '../gestor/gestor_listagem_reserva.ts';


export class ControladoraListagemReserva {

    private gestor = new GestorListagemReserva();
    private visao: VisaoListagemReserva;

    constructor(visao: VisaoListagemReserva) {
        this.visao = visao;
    }

    async logout(): Promise<void> {
        try {
            await this.gestor.logout();
            this.visao.redirecionarParaLogin();
        } catch (erro: any) {
            if (erro instanceof ErroGestor) {
                this.visao.exibirMensagem( erro.getProblemas() );
            } else {
                this.visao.exibirMensagem( [`Não foi possível completar o logout: ${erro.message}`] ); 
            }
        }
    }

    async iniciarSessao(): Promise<void> {
        try {
            await this.gestor.verificarPermissao();
        } catch (erro: any) {
            this.visao.redirecionarParaLogin();
            return;
        }
        await this.carregarDadosUsuario();
        await this.carregarTiposEspaco();
        this.visao.iniciarFiltragem();
    }

    private async carregarDadosUsuario(): Promise<void> {
        try {
            const dadosUsuario = await this.gestor.obterDadosUsuario()
            this.visao.exibirDadosUsuario(dadosUsuario);
        } catch (erro: any) {
            if (erro instanceof ErroGestor) {
                this.visao.exibirMensagem( erro.getProblemas() );
            } else {
                this.visao.exibirMensagem( [`Não foi possível carregar os dados do usuário: ${erro.message}`] ); 
            }
        }
    }

    private async carregarTiposEspaco(): Promise<void> {
        try {
            const tipos = await this.gestor.obterTiposEspaco();
            this.visao.exibirTiposEspaco(tipos);
        } catch (erro: any) {
            if (erro instanceof ErroGestor) {
                this.visao.exibirMensagem( erro.getProblemas() );
            } else {
                this.visao.exibirMensagem( [`Não foi possível carregar os tipos de espaço: ${erro.message}`] ); 
            }
        }
    }

    async carregarReservas(dataInicio: string, dataFim: string, estado: string, tipoFiltro: string, valorFiltro: string): Promise<void> {
        try {
            let reservas;
            if (tipoFiltro === 'tipoEspaco' && valorFiltro) {
                reservas = await this.gestor.obterPorTipoEspaco(dataInicio, dataFim, estado, parseInt(valorFiltro));
            } else if (tipoFiltro === 'codigoSala' && valorFiltro) {
                reservas = await this.gestor.obterPorCodigoSala(dataInicio, dataFim, estado, valorFiltro);
            } else if (tipoFiltro === 'matriculaUsuario' && valorFiltro) {
                reservas = await this.gestor.obterPorMatriculaUsuario(dataInicio, dataFim, estado, valorFiltro);
            } else {
                reservas = await this.gestor.obterPorData(dataInicio, dataFim, estado);
            }
            this.visao.exibirReservas(reservas);
        } catch (erro: any) {
            this.visao.limparTabela();
            if (erro instanceof ErroGestor) {
                this.visao.exibirMensagem( erro.getProblemas() );
            } else {
                this.visao.exibirMensagem( [`Não foi possível obter as reservas: ${erro.message}`] );
            }
        }
    }

    async cancelarReserva(idReserva: string): Promise<void> {
        try {
            await this.gestor.cancelarReserva( parseInt(idReserva) );
            this.visao.exibirMensagem( ["Reserva cancelada com sucesso."] );
            this.visao.atualizarEstadoReserva( parseInt(idReserva), "CANCELADA" );
        } catch (erro: any) {
            if (erro instanceof ErroGestor) {
                this.visao.exibirMensagem( erro.getProblemas() );
            } else {
                this.visao.exibirMensagem( [`Não foi possível cancelar a reserva: ${erro.message}`] );
            }
        }
    }

}