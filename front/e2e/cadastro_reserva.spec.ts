import { test, expect } from '@playwright/test';
import { iniciaSessaoGestor, iniciaSessaoAluno } from './login/inicia_sessao';
import { redefinir } from './utils/recomposicao_bd.ts';


const URL_CADASTRO = 'http://localhost:5173/cadastro_reserva.html';


test.describe( 'Cadastro de Reservas com Gestor', () => {
        
        test.beforeEach( async ({ context }) => {
            await iniciaSessaoGestor(context);
        } );

        test.afterAll( async () => {
            await redefinir();
        } );

        test( 'Deve exibir os dados de usuário', async ({page}) => {
            await page.goto(URL_CADASTRO);
            await page.waitForTimeout(1000);
            const conteudoPagina = await page.textContent('body');
            expect(conteudoPagina).toContain('FUNCIONARIO');
            expect(conteudoPagina).toContain('GESTÃO');
        } );

        test( 'Deve exibir todos os tipos de espaço para Gestor', async ({page}) => {
            await page.goto(URL_CADASTRO);
            await page.click('#tipoEspaco');
            const conteudoPagina = await page.textContent('body');
            expect(conteudoPagina).toContain('Sala de Aula');
            expect(conteudoPagina).toContain('Laboratório');
            expect(conteudoPagina).toContain('Auditório');
            expect(conteudoPagina).toContain('Sala de Reunião');
            expect(conteudoPagina).toContain('Sala de Vídeo');
        } );

        test( 'Deve concluir o cadastro de reserva para Gestor', async ({ page }) => {
            await page.goto(URL_CADASTRO);
            await page.selectOption('#tipoEspaco', { label: 'Laboratório' });
            await page.click('#botaoSelecionar');
            await page.fill('#dia', '2027-01-01');
            await page.fill('#horaInicio', '08:00');
            await page.fill('#horaFim', '20:00');
            await page.click('#botaoReservar');
            const alerta = await page.waitForEvent('dialog');
            expect(alerta.message()).toContain('sucesso');
        } );

        test( 'Deve exibir erro para data/hora de início no passado', async ({ page }) => {
            await page.goto(URL_CADASTRO);
            await page.selectOption('#tipoEspaco', { label: 'Sala de Reunião' });
            await page.click('#botaoSelecionar');
            await page.fill('#dia', '2020-01-01');
            await page.fill('#horaInicio', '09:00');
            await page.fill('#horaFim', '10:00');
            await page.click('#botaoReservar');
            const alerta = await page.waitForEvent('dialog');
            expect(alerta.message()).toContain('passado');
        } );

        test( 'Deve exibir erro ao tentar reservar com início após fim', async ({ page }) => {
            await page.goto(URL_CADASTRO);
            await page.selectOption('#tipoEspaco', { label: 'Auditório' });
            await page.click('#botaoSelecionar');
            await page.fill('#dia', '2027-05-25');
            await page.fill('#horaInicio', '12:00');
            await page.fill('#horaFim', '10:00');
            await page.click('#botaoReservar');
            const alerta = await page.waitForEvent('dialog');
            expect(alerta.message()).toContain('anterior');
        } );

        test( 'Deve redirecionar para login após logout', async ({ page }) => {
            await page.goto(URL_CADASTRO);
            await page.click('#botaoLogout');
            await page.waitForURL('**/login.html');
            expect(page.url()).toContain('login.html');
        } );

} );


test.describe( 'Cadastro de Reservas com Aluno', () => {
    
    test.beforeEach( async ({ context }) => {
        await iniciaSessaoAluno(context);
    } );

    test.afterAll( async () => {
        await redefinir();
    } );

    test( 'Deve exibir os dados de usuário', async ({ page }) => {
        await page.goto(URL_CADASTRO);
        await page.waitForTimeout(1000);
        const conteudoPagina = await page.textContent('body');
        expect(conteudoPagina).toContain('ALUNO');
    } );

    test( 'Deve exibir somente Sala de Reunião', async ({ page, context }) => {
        await page.goto(URL_CADASTRO);
        await page.click('#tipoEspaco');
        const conteudoPagina = await page.textContent('body');
        expect(conteudoPagina).toContain('Sala de Reunião');
        expect(conteudoPagina).not.toContain('Sala de Aula');
        expect(conteudoPagina).not.toContain('Laboratório');
        expect(conteudoPagina).not.toContain('Auditório');
        expect(conteudoPagina).not.toContain('Sala de Vídeo');
    } );

    test( 'Deve concluir o cadastro de reserva válida de até 2h', async ({ page }) => {
        await page.goto(URL_CADASTRO);
        await page.selectOption('#tipoEspaco', { label: 'Sala de Reunião' });
        await page.click('#botaoSelecionar');
        await page.fill('#dia', '2027-03-25');
        await page.fill('#horaInicio', '08:00');
        await page.fill('#horaFim', '09:59');
        await page.click('#botaoReservar');
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('sucesso');
    } );

    test( 'Deve exibir erro ao exceder limite de horas', async ({ page }) => {
        await page.goto(URL_CADASTRO);
        await page.selectOption('#tipoEspaco', { label: 'Sala de Reunião' });
        await page.click('#botaoSelecionar');
        await page.fill('#dia', '2027-02-25');
        await page.fill('#horaInicio', '08:00');
        await page.fill('#horaFim', '12:00');
        await page.click('#botaoReservar');
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('excedida');
    } );

    test( 'Deve exibir erro para justificativa muito longa', async ({ page }) => {
        await page.goto(URL_CADASTRO);
        await page.selectOption('#tipoEspaco', { label: 'Sala de Reunião' });
        await page.click('#botaoSelecionar');
        await page.fill('#dia', '2027-02-25');
        await page.fill('#horaInicio', '08:00');
        await page.fill('#horaFim', '09:00');
        await page.fill('#justificativa', 'a'.repeat(201));
        await page.click('#botaoReservar');
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('200 caracteres');
    } );

} );