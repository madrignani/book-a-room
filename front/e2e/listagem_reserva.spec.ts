import { test, expect } from '@playwright/test';
import { iniciaSessaoGestor, iniciaSessaoAluno } from './login/inicia_sessao';
import { redefinir } from './utils/recomposicao_bd';


const URL_LISTAGEM = 'http://localhost:5173/index.html';
const DATA_INICIO = '2025-01-01';
const DATA_FIM = '2025-12-31';


test.describe( 'Listagem de Reservas com Gestor', () => {
    
    test.beforeEach( async ({ context }) => {
        await iniciaSessaoGestor(context);
    } );

    test.afterAll( async () => {
        await redefinir();
    } );

    test( 'Deve exibir os dados de usuário', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.waitForTimeout(1000);
        const conteudoPagina = await page.textContent('body');
        expect(conteudoPagina).toContain('FUNCIONARIO');
        expect(conteudoPagina).toContain('GESTÃO');
    } );

    test( 'Deve exibir reservas no intervalo 2025', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.fill('#dataInicio', DATA_INICIO);
        await page.fill('#dataFim', DATA_FIM);
        await page.waitForSelector('tbody tr');
        const linhas = await page.$$('tbody tr');
        expect(linhas.length).toBeGreaterThan(10);
    } );

    test( 'Deve exibir reservas com intervalo semanal (sem inserção de data)', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.waitForSelector('tbody tr');
        const linhas = await page.$$('tbody tr');
        expect(linhas.length).toBeGreaterThan(0);
        expect(linhas.length).toBeLessThan(5);
    } );

    test( 'Deve exibir 2 reservas ao filtrar por tipo Auditório', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.fill('#dataInicio', DATA_INICIO);
        await page.fill('#dataFim', DATA_FIM);
        await page.selectOption('#filtroExtra', { label: 'Tipo de Espaço' });
        await page.selectOption('#tipoEspaco', { label: 'Auditório' });
        await page.waitForTimeout(1000);
        await page.waitForSelector('tbody tr');
        const linhas = await page.$$('tbody tr');
        expect(linhas.length).toBe(2);
    } );

    test( 'Deve exibir reservas com filtro por código de sala', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.fill('#dataInicio', DATA_INICIO);
        await page.fill('#dataFim', DATA_FIM);
        await page.selectOption('#filtroExtra', { label: 'Código de Sala' });
        await page.fill('#codigoSala', 'AUD1');
        await page.keyboard.press('Enter');
        await page.waitForTimeout(1000);
        await page.waitForSelector('tbody tr');
        const linhas = await page.$$('tbody tr');
        expect(linhas.length).toBeGreaterThan(0);
        expect(linhas.length).toBeLessThan(5);
    } );

    test( 'Deve exibir reservas com filtro por matrícula', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.fill('#dataInicio', DATA_INICIO);
        await page.fill('#dataFim', DATA_FIM);
        await page.selectOption('#filtroExtra', { label: 'Matrícula de Usuário' });
        await page.fill('#matriculaUsuario', '0001');
        await page.keyboard.press('Enter');
        await page.waitForTimeout(1000);
        await page.waitForSelector('tbody tr');
        const linhas = await page.$$('tbody tr');
        expect(linhas.length).toBeGreaterThan(0);
        expect(linhas.length).toBeLessThan(5);
    } );

    test( 'Deve permitir Gestor de cancelar reserva de funcionário sem gestão', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.fill('#dataInicio', DATA_INICIO);
        await page.fill('#dataFim', DATA_FIM);
        await page.waitForSelector('tbody tr');
        const linha = page.locator('tbody tr', { hasText: 'José Cardoso' }).first();
        await linha.locator('.botaoCancelar').click();
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('sucesso');
    } );

    test( 'Deve esconder botões de cancelar para rádio de reservas canceladas', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.fill('#dataInicio', DATA_INICIO);
        await page.fill('#dataFim', DATA_FIM);
        await page.check('input[type="radio"][value="CANCELADA"]');
        await page.waitForTimeout(1000);
        const botoes = await page.$$('.botaoCancelar');
        expect(botoes.length).toBe(0);
    } );

    test( 'Deve exibir erro ao tentar filtrar fim antes de início', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.fill('#dataFim', '2026-05-25');
        await page.fill('#dataInicio', '2027-05-25');
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('anterior');
    } );

} );


test.describe( 'Listagem de Reservas com Aluno', () => {
    
    test.beforeEach( async ({ context }) => {
        await iniciaSessaoAluno(context);
    } );

    test.afterAll( async () => {
        await redefinir();
    } );

    test( 'Deve exibir os dados de usuário', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.waitForTimeout(1000);
        const conteudoPagina = await page.textContent('body');
        expect(conteudoPagina).toContain('ALUNO');
    } );

    test( 'Deve cancelar reserva própria ', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.fill('#dataInicio', DATA_INICIO);
        await page.fill('#dataFim', DATA_FIM);
        await page.waitForSelector('tbody tr');
        const linha = page.locator('tbody tr', { hasText: 'Raquel Oliveira' }).first();
        await linha.locator('.botaoCancelar').click();
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('sucesso');
    } );

    test( 'Deve exibir erro ao tentar cancelar reserva de outro', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.fill('#dataInicio', DATA_INICIO);
        await page.fill('#dataFim', DATA_FIM);
        await page.waitForSelector('tbody tr');
        const linha = page.locator('tbody tr', { hasText: 'Maria Souza' }).first();
        await linha.locator('.botaoCancelar').click();
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('Permissão');
    } );

    test( 'Deve redirecionar para login após logout', async ({ page }) => {
        await page.goto(URL_LISTAGEM);
        await page.click('#botaoLogout');
        await page.waitForURL('**/login.html');
        expect(page.url()).toContain('login.html');
    } );

} );