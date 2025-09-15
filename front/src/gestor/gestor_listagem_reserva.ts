import { API_URL } from './rota_api';
import { ErroGestor } from '../infra/erro_gestor';
import { GestorAutenticacao } from './gestor_autenticacao.ts';
import { GestorSessao } from '../gestor/gestor_sessao.ts';
import { GestorCadastroReserva } from '../gestor/gestor_cadastro_reserva.ts';


export class GestorListagemReserva {

    private gestorAutenticacao = new GestorAutenticacao();
    private gestorSessao = new GestorSessao();
    private gestorCadastro = new GestorCadastroReserva();

    async logout(): Promise<void> {
        await this.gestorAutenticacao.logout();
    }

    async verificarPermissao(): Promise<void> {
        await this.gestorSessao.verificarPermissao();
    }

    async obterDadosUsuario(): Promise<void> {
        return await this.gestorSessao.obterDadosUsuario();
    }

    async obterTiposEspaco(): Promise<any> {
        return await this.gestorCadastro.obterTiposEspaco();
    }
    
    async obterPorData(inicio: string, fim: string, estado: string): Promise<any> {
        const response = await fetch( `${API_URL}/reservas?inicio=${inicio}&fim=${fim}&estado=${estado}`, {
            method: 'GET',
            credentials: 'include'
        } );
        if (!response.ok) {
            const dadosResposta = await response.json();
            throw ErroGestor.comProblemas(dadosResposta.mensagens);
        }
        return await response.json();
    }

    async obterPorTipoEspaco(inicio: string, fim: string, estado: string, idTipo: number): Promise<any> {
        const response = await fetch( `${API_URL}/reservas-te?inicio=${inicio}&fim=${fim}&estado=${estado}&tipo=${idTipo}`, {
            method: 'GET',
            credentials: 'include'
        } );
        if (!response.ok) {
            const dadosResposta = await response.json();
            throw ErroGestor.comProblemas(dadosResposta.mensagens);
        }
        return await response.json();
    }

    async obterPorCodigoSala(inicio: string, fim: string, estado: string, codigo: string): Promise<any> {
        const response = await fetch( `${API_URL}/reservas-sala?inicio=${inicio}&fim=${fim}&estado=${estado}&sala=${codigo}`, {
            method: 'GET',
            credentials: 'include'
        } );
        if (!response.ok) {
            const dadosResposta = await response.json();
            throw ErroGestor.comProblemas(dadosResposta.mensagens);
        }
        return await response.json();
    }

    async obterPorMatriculaUsuario(inicio: string, fim: string, estado: string, matricula: string): Promise<any> {
        const response = await fetch( `${API_URL}/reservas-usuario?inicio=${inicio}&fim=${fim}&estado=${estado}&matricula=${matricula}`, {
            method: 'GET',
            credentials: 'include'
        });
        if (!response.ok) {
            const dadosResposta = await response.json();
            throw ErroGestor.comProblemas(dadosResposta.mensagens);
        }
        return await response.json();
    }

    async cancelarReserva(idReserva: number): Promise<void> {
        const response = await fetch( `${API_URL}/reservas-cancelar/${idReserva}`, {
            method: 'PATCH',
            credentials: 'include'
        } );
        if (!response.ok) {
            const dadosResposta = await response.json();
            throw ErroGestor.comProblemas(dadosResposta.mensagens);
        }
    }

}