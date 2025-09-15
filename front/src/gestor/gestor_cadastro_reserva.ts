import { API_URL } from './rota_api.ts';
import { ErroGestor } from '../infra/erro_gestor.ts';
import { GestorAutenticacao } from './gestor_autenticacao.ts';
import { GestorSessao } from '../gestor/gestor_sessao.ts';


export class GestorCadastroReserva {

    private gestorAutenticacao = new GestorAutenticacao();
    private gestorSessao = new GestorSessao();

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
        const response = await fetch( `${API_URL}/tipos-espaco`, {
            method: 'GET',
            credentials: 'include'
        } );
        if (!response.ok) {
            const dadosResposta = await response.json();
            throw ErroGestor.comProblemas(dadosResposta.mensagens);
        }
        return await response.json(); 
    }

    async obterEspacosPorTipo(idTipoEspaco: number): Promise<any> {
        const response = await fetch( `${API_URL}/espacos-por-tipo/${idTipoEspaco}`, {
            method: 'GET',
            credentials: 'include'
        } );
        if (!response.ok) {
            const dadosResposta = await response.json();
            throw ErroGestor.comProblemas(dadosResposta.mensagens);
        }
        return await response.json();    
    }

    async cadastrarReserva(idEspaco: number, inicio: string, fim: string, justificativa: string): Promise<void> {
        const dados = {
            idEspaco,
            inicio,
            fim,
            justificativa
        };
        const response = await fetch( `${API_URL}/reservas`, {
            method: "POST",
            credentials: "include",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dados)
        } );
        if (!response.ok) {
            const dadosResposta = await response.json();
            throw ErroGestor.comProblemas(dadosResposta.mensagens);
        }
    }

}