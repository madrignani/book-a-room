<?php


namespace App\Repositorio;
use App\Excecao\RepositorioException;
use PDO;
use PDOException;


class RepositorioEspacoBDR implements RepositorioEspaco {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function buscarPorId(int $id): ?array {
        try {
            $sql = <<<SQL
                SELECT id, codigo_sala, nome, id_tipo FROM espaco WHERE id = :id
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

    public function buscarPorTipo(int $idTipoEspaco): array {
        try {
            $sql = <<<SQL
                SELECT id, codigo_sala, nome, id_tipo FROM espaco WHERE id_tipo = :idTipoEspaco
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( ['idTipoEspaco' => $idTipoEspaco] );
            $dados = $stmt->fetchAll();
            return $dados;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

    public function buscarPorSala(string $codigo): ?array {
        try {
            $sql = <<<SQL
                SELECT id, codigo_sala, nome, id_tipo FROM espaco WHERE codigo_sala = :codigo
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( ['codigo' => $codigo] );
            $dados = $stmt->fetch();
            if ( empty($dados) ) {
                return null;
            }
            return $dados;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

}


?>