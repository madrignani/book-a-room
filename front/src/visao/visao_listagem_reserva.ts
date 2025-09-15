export interface VisaoListagemReserva {

    exibirMensagem( mensagens: string[] ): void;
    redirecionarParaLogin(): void;
    exibirDadosUsuario( dados: any ): void;
    exibirTiposEspaco(tipos: any[]): void;
    iniciarFiltragem(): void;
    enviarFiltros(): void;
    exibirReservas( reservas: any[] ): void;
    atualizarEstadoReserva(idReserva: number, novoEstado: string): void;
    limparTabela(): void;
    
}