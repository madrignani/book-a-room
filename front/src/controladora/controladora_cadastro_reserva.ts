import { ErroGestor } from '../infra/erro_gestor.ts';
import type { VisaoCadastroReserva } from '../visao/visao_cadastro_reserva.ts';
import { GestorCadastroReserva } from '../gestor/gestor_cadastro_reserva.ts';


export class ControladoraCadastroReserva {

    private gestor = new GestorCadastroReserva();
    private visao: VisaoCadastroReserva;
    private idEspacoSelecionado: number | null = null;

    constructor(visao: VisaoCadastroReserva) {
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
        this.visao.iniciarListagemEspacos();
        this.visao.iniciarFormulario();
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

    async carregarEspacosPorTipo(idTipoEspaco: string): Promise<void> {
        try {
            const espacos = await this.gestor.obterEspacosPorTipo( parseInt(idTipoEspaco) );
            this.visao.exibirEspacos(espacos);
        } catch (erro: any) {
            this.visao.limparTabelaEspacos();
            if (erro instanceof ErroGestor) {
                this.visao.exibirMensagem( erro.getProblemas() );
            } else {
                this.visao.exibirMensagem( [`Não foi possível carregar os espaços: ${erro.message}`] ); 
            }
        }
    }

    espacoSelecionado(idEspaco: string): void {
        this.idEspacoSelecionado = ( parseInt(idEspaco) );
    }

    async enviarReserva(inicio: string, fim: string, justificativa: string): Promise<void> {
        if (!this.idEspacoSelecionado) {
            this.visao.exibirMensagem( ["Selecione um espaço para cadastrar Reserva."] );
            return;
        }
        try {
            await this.gestor.cadastrarReserva(
                this.idEspacoSelecionado,
                inicio,
                fim,
                justificativa
            );
            this.visao.exibirMensagem( ["Reserva cadastrada com sucesso."] );
        } catch (erro: any) {
            if (erro instanceof ErroGestor) {
                this.visao.exibirMensagem( erro.getProblemas() );
            } else {
                this.visao.exibirMensagem( [`Erro ao cadastrar reserva: ${erro.message}`] );
            }
        }
    }

}