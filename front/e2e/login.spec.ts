import { test, expect } from '@playwright/test';


const URL_LOGIN = 'http://localhost:5173/login.html';


test.describe( 'Página de Login', () => {
    
    test.beforeEach(async ({ page }) => {
        await page.goto(URL_LOGIN);
    });

    test( 'Deve exibir erro se login estiver vazio', async ({ page }) => {
        await page.fill('#senha', '11111111');
        await page.click('#botaoLogin');
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('obrigatórios');
    } );

    test( 'Deve exibir erro se senha estiver vazia', async ({ page }) => {
        await page.fill('#login', '11111111');
        await page.click('#botaoLogin');
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('obrigatórios');
    } );

    test( 'Deve exibir erro se login ou senha estiverem incorretos', async ({ page }) => {
        await page.fill('#login', 'invalido@exemplo.com');
        await page.fill('#senha', 'errada');
        await page.click('#botaoLogin');
        const alerta = await page.waitForEvent('dialog');
        expect(alerta.message()).toContain('credenciais');
    } );

} );