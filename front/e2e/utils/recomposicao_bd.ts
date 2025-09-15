import mysql from 'mysql2/promise';


export async function redefinir(): Promise<void> {

    const conexao = await mysql.createConnection( {
        host: 'localhost',
        user: 'root',
        password: '', 
        database: 'acme',
    } );

        await conexao.execute(` SET FOREIGN_KEY_CHECKS = 0 `);
        await conexao.execute(` TRUNCATE TABLE reserva `);
        await conexao.execute(` TRUNCATE TABLE espaco `);
        await conexao.execute(` TRUNCATE TABLE tipo_espaco `);
        await conexao.execute(` TRUNCATE TABLE aluno`);
        await conexao.execute(` TRUNCATE TABLE funcionario `);
        await conexao.execute(` TRUNCATE TABLE usuario `);
        await conexao.execute(` SET FOREIGN_KEY_CHECKS = 1 `);
        await conexao.execute(`
            INSERT INTO usuario (matricula, nome, email, senha_hash, sal, tipo_usuario) VALUES
                ( '0001', 'João Silva', 'joao@acme.br', '0a316f4b6d58f099823e47f6dfe3433f7c7d0d9c0416b4a6ec15ef4098870faf09f3d79411947570803fe72c20cf59034358235ec4ab6691f6abeb451517ec79', '1ce4a434402877a90a68dc4a784b9249', 'FUNCIONARIO' ),
                ( '0002', 'Carlos Pinho', 'carlos@acme.br', '31240c519eebe86b2b79526e37f803e3a216971332e0fbaa19061419316c15145d01331353047b9fde9d0870d33bab790b9d86a86380e1ad05f03e9e3a5c75c5', 'ab7ea96815f5172fb81c40eebc911682', 'FUNCIONARIO' ),
                ( '0003', 'Maria Souza', 'maria@acme.br', '9ecc8a13b4fb68a918dcc82d438a9a087ac5e911b24e605a52733db3af3667912c5ba9b5b76d7de57494f1a79ad8d35ef252714b2294f0528f49bb8fefcb701c', '815c88c469b6edce01f9c9b97610d9d5', 'FUNCIONARIO' ),
                ( '0004', 'José Cardoso', 'jose@acme.br', '2feacd0fe04bf4a9663ff8a47e1bebf3f0a291db740377c00c518221c99f00b2ca6b90e56d84897c7ceac920b6bddaf782061b47227fee2c51bb526586ef3159', 'a874f5e5b7dd62c8ade34e11e24fd036', 'FUNCIONARIO' ),
                ( '0005', 'Raquel Oliveira', 'raquel@acme.br', 'f17f9bf31f7444481de1bb854327027cc49d01c7f777282244c047917cd83415b9ad9bf9e85dd1e851eed1e889314efe83aec2c805cbf737e5d31195629e1cfc', '61f2f3964d05b3171517e0fff97f1a53', 'ALUNO' );
        `);
        await conexao.execute(`
            INSERT INTO funcionario (id_usuario, tipo_funcionario, cargo_gestao) VALUES
                ( 1, 'TECNICO', TRUE ),
                ( 2, 'TECNICO', FALSE ),
                ( 3, 'PROFESSOR', TRUE ),
                ( 4, 'PROFESSOR', FALSE );
        `);
        await conexao.execute(`
            INSERT INTO aluno (id_usuario) VALUES
                ( 5 );
        `);
        await conexao.execute(`
            INSERT INTO tipo_espaco (nome) VALUES
                ( 'Sala de Aula' ),
                ( 'Sala de Reunião' ),
                ( 'Sala de Vídeo' ),
                ( 'Laboratório' ),
                ( 'Auditório' );
        `);
        await conexao.execute(`
            INSERT INTO espaco (codigo_sala, nome, id_tipo) VALUES
                ( 'SDA1', 'Sala de Aula 01', 1 ),
                ( 'SDA2', 'Sala de Aula 02', 1 ),
                ( 'SDR1', 'Sala de Reunião 01', 2 ),
                ( 'SDR2', 'Sala de Reunião 02', 2 ),
                ( 'SDV1', 'Sala de Vídeo 01', 3 ),
                ( 'LDF1', 'Lab. de Física 01', 4 ),
                ( 'LDI1', 'Lab. de Informática 01', 4 ),
                ( 'AUD1', 'Auditório 01', 5 );
        `);
        await conexao.execute(`
            INSERT INTO reserva (id_usuario, id_espaco, data_reserva, inicio, fim, justificativa, estado) VALUES
                ( 3, 1, '2025-07-21 17:00:00', '2025-08-29 08:00:00', '2025-08-29 19:00:00', 'Aula', 'MARCADA' ),
                ( 4, 2, '2025-07-21 17:00:00', '2025-11-25 09:00:00', '2025-11-25 12:00:00', 'Aula', 'MARCADA' ),
                ( 2, 3, '2025-07-21 17:00:00', '2025-10-02 11:00:00', '2025-10-02 12:00:00', 'Reunião', 'MARCADA' ),
                ( 2, 3, '2025-07-21 17:00:00', '2025-12-09 08:00:00', '2025-12-09 09:00:00', 'Reunião', 'MARCADA' ),
                ( 5, 3, '2025-07-21 17:00:00', '2025-10-18 16:00:00', '2025-10-18 17:00:00', 'Reunião', 'MARCADA' ),
                ( 3, 4, '2025-07-21 17:00:00', '2025-10-05 12:00:00', '2025-10-05 13:00:00', 'Reunião', 'MARCADA' ),
                ( 5, 4, '2025-07-21 17:00:00', '2025-09-29 09:00:00', '2025-09-29 10:00:00', 'Reunião', 'MARCADA' ),
                ( 5, 4, '2025-07-21 17:00:00', '2025-09-15 14:00:00', '2025-09-15 15:00:00', 'Reunião', 'MARCADA' ),
                ( 3, 5, '2025-07-21 17:00:00', '2025-09-10 13:00:00', '2025-09-10 15:00:00', 'Documentário', 'MARCADA' ),
                ( 3, 5, '2025-07-21 17:00:00', '2025-07-22 13:00:00', '2025-07-22 14:00:00', 'Documentário', 'MARCADA' ),
                ( 4, 6, '2025-07-21 17:00:00', '2025-10-21 10:00:00', '2025-10-21 14:00:00', 'Demonstração', 'MARCADA' ),
                ( 1, 6, '2025-07-21 17:00:00', '2025-12-01 10:00:00', '2025-12-01 12:00:00', 'Demonstração', 'MARCADA' ),
                ( 3, 7, '2025-07-21 17:00:00', '2025-08-30 09:00:00', '2025-08-30 12:00:00', 'Demonstração', 'MARCADA' ),
                ( 4, 8, '2025-07-21 17:00:00', '2025-11-05 15:00:00', '2025-11-05 17:00:00', 'Palestra', 'MARCADA' ),
                ( 1, 8, '2025-07-21 17:00:00', '2025-11-13 13:00:00', '2025-11-13 21:00:00', 'Palestra', 'MARCADA' ),
                ( 1, 8, '2025-07-21 17:00:00', '2025-11-19 13:00:00', '2025-11-19 13:00:00', 'Palestra', 'CANCELADA' );
        `);

}