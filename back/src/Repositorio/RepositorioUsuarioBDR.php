<?php


namespace App\Repositorio;
use App\Excecao\RepositorioException;
use PDO;
use PDOException;


class RepositorioUsuarioBDR implements RepositorioUsuario {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function buscarPorId(int $id): ?array {
        try {
            $sql = <<<SQL
                SELECT u.id, u.matricula, u.nome, u.email, u.tipo_usuario, 
                f.tipo_funcionario, f.cargo_gestao
                FROM usuario u
                LEFT JOIN funcionario f ON f.id_usuario = u.id
                WHERE u.id = :id
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( ['id' => $id] );
            $dados = $stmt->fetch();
            if ( empty($dados) ) {
                return null;
            }
            return $dados;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

    public function buscarPorMatriculaOuEmail(string $valor): ?array {
        try {
            $sql = <<<SQL
                SELECT u.id, u.matricula, u.nome, u.email, u.sal, u.tipo_usuario, 
                f.tipo_funcionario, f.cargo_gestao
                FROM usuario u LEFT JOIN funcionario f ON f.id_usuario = u.id
                WHERE u.matricula = :valor OR u.email = :valor
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( ['valor' => $valor] );
            $dados = $stmt->fetch();
            if ( empty($dados) ) {
                return null;
            }
            return $dados;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

    public function buscarPorMatricula(string $matricula): ?array {
        try {
            $sql = <<<SQL
                SELECT u.id, u.matricula, u.nome, u.email, u.tipo_usuario, 
                f.tipo_funcionario, f.cargo_gestao
                FROM usuario u LEFT JOIN funcionario f ON f.id_usuario = u.id
                WHERE u.matricula = :matricula
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( ['matricula' => $matricula] );
            $dados = $stmt->fetch();
            if ( empty($dados) ) {
                return null;
            }
            return $dados;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

    public function verificarHash(int $id, string $hash): bool {
        try{
            $sql = <<<SQL
                SELECT COUNT(*) FROM usuario WHERE id = :id AND senha_hash = :hash
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( ['id' => $id, 'hash' => $hash] );
            $existe = ( $stmt->fetchColumn()>0 );
            return $existe;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

}


?>