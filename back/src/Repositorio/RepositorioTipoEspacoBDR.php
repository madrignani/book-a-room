<?php


namespace App\Repositorio;
use App\Excecao\RepositorioException;
use PDO;
use PDOException;


class RepositorioTipoEspacoBDR implements RepositorioTipoEspaco {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function buscarPorId(int $id): ?array {
        try {
            $sql = <<<SQL
                SELECT id, nome FROM tipo_espaco WHERE id = :id
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

    public function buscarTodos(): array {
        try {
            $sql = <<<SQL
                SELECT id, nome FROM tipo_espaco;
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

}


?>