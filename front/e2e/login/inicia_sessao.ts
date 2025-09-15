import { BrowserContext } from '@playwright/test';
import dotenv from 'dotenv';


dotenv.config( );


const LOGIN_URL = 'http://localhost:5173/login.html';


export async function iniciaSessaoGestor(contexto: BrowserContext) {
    const pagina = await contexto.newPage();
    await pagina.goto(LOGIN_URL);
    const matricula = process.env.MATRICULA_GESTOR;
    const senha = process.env.SENHA_GESTOR;
    await pagina.fill('#login', matricula!);
    await pagina.fill('#senha', senha!);
    await pagina.click('#botaoLogin');
    await pagina.waitForURL('**/index.html');
    await contexto.storageState({ path: 'e2e/.auth/session_gestor.json' });
    await pagina.close();
}


export async function iniciaSessaoAluno(contexto: BrowserContext) {
    const pagina = await contexto.newPage();
    await pagina.goto(LOGIN_URL);
    const matricula = process.env.MATRICULA_ALUNO;
    const senha = process.env.SENHA_ALUNO;
    await pagina.fill('#login', matricula!);
    await pagina.fill('#senha', senha!);
    await pagina.click('#botaoLogin');
    await pagina.waitForURL('**/index.html');
    await contexto.storageState({ path: 'e2e/.auth/session_aluno.json' });
    await pagina.close();
}