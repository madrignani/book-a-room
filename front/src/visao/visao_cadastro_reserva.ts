export interface VisaoCadastroReserva {

    exibirMensagem( mensagens: string[] ): void;
    redirecionarParaLogin(): void;
    exibirDadosUsuario( dados: any ): void;
    exibirTiposEspaco( tipos: any[] ): void;
    iniciarListagemEspacos(): void;
    exibirEspacos( espacos: any[] ): void;
    limparTabelaEspacos(): void;
    iniciarFormulario(): void;
    
}