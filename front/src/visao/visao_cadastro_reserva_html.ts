import type { VisaoCadastroReserva } from './visao_cadastro_reserva.ts';
import { ControladoraCadastroReserva } from '../controladora/controladora_cadastro_reserva.ts';


export class VisaoCadastroReservaHTML implements VisaoCadastroReserva {

    private controladora: ControladoraCadastroReserva;
    
    constructor() {
        this.controladora = new ControladoraCadastroReserva(this);
    }
    
    iniciar(): void {
        this.controladora.iniciarSessao();
        this.iniciarLogout();
    }

    private iniciarLogout(): void {
        const botaoLogout = document.getElementById("botaoLogout") as HTMLButtonElement;
        botaoLogout.addEventListener( 'click', () => {
            this.controladora.logout();
        } );
    }

    exibirDadosUsuario( dados: any ): void {
        const div = document.getElementById("identificacaoUsuario") as HTMLDivElement;
        let texto = `Usuário: ${dados.nome} -- ${dados.tipoUsuario}`;
        if(dados.tipoFuncionario) {
            texto += ` (${dados.tipoFuncionario})`;
        }
        if (dados.cargoGestao) {
            if(dados.cargoGestao === true){
                texto += ` GESTÃO`;
            }
        }
        div.textContent = texto;
    }

    exibirMensagem( mensagens: string[] ): void {
        alert( mensagens.join("\n") );
    }

    redirecionarParaLogin(): void {
        window.location.href = "./login.html";
    }

    exibirTiposEspaco(tipos: any[]): void {
        const select = document.getElementById("tipoEspaco") as HTMLSelectElement;
        select.innerHTML = '<option value="">Selecione</option>';
        for (const tipo of tipos) {
            const option = document.createElement("option");
            option.value = tipo.id;
            option.textContent = tipo.nome;
            select.appendChild(option);
        }
    }

    iniciarListagemEspacos(): void {
        const select = document.getElementById("tipoEspaco") as HTMLSelectElement;
        select.addEventListener( 'change', () => {
            const idTipoEspaco = select.value;
            if (idTipoEspaco) {
                this.controladora.carregarEspacosPorTipo(idTipoEspaco);
            } else {
                this.limparTabelaEspacos();
            }
        } );
    }

    exibirEspacos(espacos: any[]): void {
        const tbody = document.querySelector("tbody") as HTMLTableSectionElement;
        tbody.innerHTML = '';
        for (const espaco of espacos) {
            const linha = document.createElement("tr");
            const celulaCodigo = document.createElement("td");
            const celulaNome = document.createElement("td");
            celulaCodigo.textContent = espaco.codigoSala;
            celulaNome.textContent = espaco.nome;
            const celulaAcao = document.createElement("td");
            const botaoSelecionar = document.createElement("button");
            botaoSelecionar.id = 'botaoSelecionar';
            botaoSelecionar.textContent = "Selecionar";
            botaoSelecionar.dataset.id = espaco.id;
            botaoSelecionar.addEventListener( "click", () => {
                const idEspaco = botaoSelecionar.dataset.id;
                this.controladora.espacoSelecionado(idEspaco!);
                const todasLinhas = tbody.querySelectorAll("tr");
                for (const l of todasLinhas) {
                    l.classList.remove('espacoMarcado');
                }
                linha.classList.add('espacoMarcado');
            } );
            celulaAcao.appendChild(botaoSelecionar);   
            linha.appendChild(celulaCodigo);
            linha.appendChild(celulaNome);
            linha.appendChild(celulaAcao);
            tbody.appendChild(linha);
        }
    }

    limparTabelaEspacos(): void {
        const tbody = document.querySelector("tbody") as HTMLTableSectionElement;
        tbody.innerHTML = '';
    }

    iniciarFormulario(): void {
        const form = document.querySelector("form") as HTMLFormElement;
        form.addEventListener( "submit", (event) => {
            event.preventDefault();
            this.coletarDadosReserva();
        } );
    }

    private coletarDadosReserva(): void {
        const dia = ( document.getElementById("dia") as HTMLInputElement ).value;
        const horaInicio = ( document.getElementById("horaInicio") as HTMLInputElement ).value;
        const horaFim = ( document.getElementById("horaFim") as HTMLInputElement ).value;
        const inicioIso = `${dia}T${horaInicio}`;
        const fimIso = `${dia}T${horaFim}`;
        const justificativa = ( document.getElementById("justificativa") as HTMLTextAreaElement ).value;
        this.controladora.enviarReserva(inicioIso, fimIso, justificativa);
    }

}