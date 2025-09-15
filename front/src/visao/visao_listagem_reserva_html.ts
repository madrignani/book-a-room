import type { VisaoListagemReserva } from './visao_listagem_reserva.ts';
import { ControladoraListagemReserva } from '../controladora/controladora_listagem_reserva.ts';


export class VisaoListagemReservaHTML implements VisaoListagemReserva {

    private controladora: ControladoraListagemReserva;
    
    constructor() {
        this.controladora = new ControladoraListagemReserva(this);
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

    iniciarFiltragem(): void {
        this.enviarFiltros();
        this.exibirFiltroExtraSelecionado();
        const filtroExtra = document.getElementById('filtroExtra') as HTMLSelectElement;
        filtroExtra.addEventListener( 'change', () => {
            this.limparCamposFiltroExtra();
            this.exibirFiltroExtraSelecionado();
            if (filtroExtra.value === '') {
                this.enviarFiltros();
            }
        } );
        const inputFiltro = ['dataInicio', 'dataFim', 'tipoEspaco', 'codigoSala', 'matriculaUsuario'];
        for (const input of inputFiltro) {
            document.getElementById(input)!.addEventListener( 'change', () => {
                this.enviarFiltros();
            });
        }
        const radios = document.querySelectorAll<HTMLInputElement>('input[type="radio"]');
        for (const radio of radios) {
            radio.addEventListener( 'change', () => {
                this.enviarFiltros();
            } );
        }
    }

    private limparCamposFiltroExtra(): void {
        const campos = ['tipoEspaco', 'codigoSala', 'matriculaUsuario'];
        for (const campo of campos) {
            const elemento = document.getElementById(campo)! as HTMLInputElement | HTMLSelectElement;
            elemento.value = '';
        }
    }

    private exibirFiltroExtraSelecionado(): void {
        const filtroExtraSelect = (document.getElementById('filtroExtra') as HTMLSelectElement).value;
        const campos = ['campoTipoEspaco', 'campoCodigoSala', 'campoMatriculaUsuario'];
        for (const campo of campos) {
            document.getElementById(campo)!.style.display = 'none';
        }
        if (filtroExtraSelect === 'tipoEspaco') {
            document.getElementById('campoTipoEspaco')!.style.display = 'block';
        }
        if (filtroExtraSelect === 'codigoSala') { 
            document.getElementById('campoCodigoSala')!.style.display = 'block';
        }
        if (filtroExtraSelect === 'matriculaUsuario') {
            document.getElementById('campoMatriculaUsuario')!.style.display = 'block';
        }
    }

    enviarFiltros(): void {
        const dataInicio = (document.getElementById('dataInicio') as HTMLInputElement).value;
        const dataFim = (document.getElementById('dataFim') as HTMLInputElement).value;
        const estado = (document.querySelector('input[type="radio"]:checked') as HTMLInputElement).value;
        const filtroExtraSelect = (document.getElementById('filtroExtra') as HTMLSelectElement).value;
        let valorFiltro = null;
        if (filtroExtraSelect === 'tipoEspaco') {
            valorFiltro = (document.getElementById('tipoEspaco') as HTMLSelectElement).value;
        } else if (filtroExtraSelect === 'codigoSala') {
            valorFiltro = (document.getElementById('codigoSala') as HTMLInputElement).value;
        } else {
            valorFiltro = (document.getElementById('matriculaUsuario') as HTMLInputElement).value;
        }
        this.controladora.carregarReservas( dataInicio, dataFim, estado, filtroExtraSelect, valorFiltro );
    }

    exibirReservas(reservas: any[]): void {
        const tbody = document.querySelector("tbody") as HTMLTableSectionElement;
        tbody.innerHTML = '';
        for (const reserva of reservas) {
            const dataReserva = new Date(reserva.inicio);
            const dias = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"];
            const diaSemana = dias[dataReserva.getDay()];
            const dataReservaBr = dataReserva.toLocaleDateString("pt-BR");
            const horaInicio = dataReserva.toLocaleTimeString("pt-BR");
            const horaFim = ( new Date(reserva.fim) ).toLocaleTimeString("pt-BR");
            const linha = document.createElement("tr");
            const dadosReserva = [
                reserva.id, reserva.nomeEspaco, (`${diaSemana} ${dataReservaBr}`),
                horaInicio, horaFim, reserva.nomeUsuario, (reserva.justificativa || "-")
            ];
            for (const dado of dadosReserva) {
                const td = document.createElement("td");
                td.textContent = dado;
                linha.appendChild(td);
            }
            const acao = document.createElement("td");
            if (reserva.estado !== "CANCELADA") {
                const botaoCancelar = document.createElement("button");
                botaoCancelar.classList.add("botaoCancelar");
                botaoCancelar.textContent = "Cancelar";
                botaoCancelar.dataset.id = reserva.id;
                acao.appendChild(botaoCancelar);
            }
            linha.appendChild(acao);
            linha.dataset.id = reserva.id;
            tbody.appendChild(linha);
        }
        this.iniciarCancelamento();
    }

    private iniciarCancelamento(): void {
        const botoes = document.querySelectorAll<HTMLButtonElement>(".botaoCancelar");
        for (const botao of botoes) {
            botao.addEventListener( "click", () => {
                const idReserva = botao.dataset.id!;
                this.controladora.cancelarReserva( idReserva );
            } );
        }
    }

    atualizarEstadoReserva(idReserva: number): void {
        const linha = document.querySelector( `tr[data-id='${idReserva}']` );
        if (linha) {
            linha.remove();
        }
    }

    limparTabela(): void {
        const tbody = document.querySelector("tbody") as HTMLTableSectionElement;
        tbody.innerHTML = '';
    }

}