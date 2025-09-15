<?php


namespace App\Repositorio;
use App\Excecao\RepositorioException;
use PDO;
use PDOException;


class RepositorioReservaBDR implements RepositorioReserva {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function salvar(int $idUsuario, int $idEspaco, string $dataReserva, string $inicio, string $fim, ?string $justificativa, string $estado): void {
        try {
            $sql = <<<SQL
                INSERT INTO reserva ( id_usuario, id_espaco, data_reserva, inicio, fim, justificativa, estado) 
                VALUES ( :idUsuario, :idEspaco, :dataReserva, :inicio, :fim, :justificativa, :estado )
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( [
                'idUsuario' => $idUsuario,
                'idEspaco' => $idEspaco,
                'dataReserva' => $dataReserva,
                'inicio' => $inicio,
                'fim' => $fim,
                'justificativa' => $justificativa,
                'estado' => $estado
            ] );
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

    public function buscarPorId(int $id): ?array {
        try {
            $sql = <<<SQL
                SELECT * FROM reserva WHERE id = :id
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

    public function buscarPorPeriodo(string $inicio, string $fim, string $estado): array {
        try {
            $sql = <<<SQL
                SELECT r.id, r.id_usuario, r.id_espaco, r.inicio, r.fim, r.justificativa, r.estado
                FROM reserva r JOIN espaco e ON r.id_espaco = e.id
                WHERE r.estado = :estado
                AND r.inicio >= :inicio
                AND r.fim <= :fim
                ORDER BY e.nome ASC, r.inicio ASC
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( [
                'inicio' => $inicio,
                'fim' => $fim,
                'estado' => $estado
            ] );
            $dados = $stmt->fetchAll();
            return $dados;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

    public function buscarPorPeriodoETipo(string $inicio, string $fim, string $estado, int $idTipoEspaco): array {
        try {
            $sql = <<<SQL
                SELECT r.id, r.id_usuario, r.id_espaco, r.inicio, r.fim, r.justificativa, r.estado
                FROM reserva r INNER JOIN espaco e ON r.id_espaco = e.id
                WHERE r.estado = :estado
                AND r.inicio >= :inicio
                AND r.fim <= :fim
                AND e.id_tipo = :idTipoEspaco
                ORDER BY e.nome ASC, r.inicio ASC
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( [
                'inicio' => $inicio,
                'fim' => $fim,
                'estado' => $estado,
                'idTipoEspaco' => $idTipoEspaco
            ] );
            $dados = $stmt->fetchAll();
            return $dados;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

    public function buscarPorPeriodoESala(string $inicio, string $fim, string $estado, string $codigoSala): array {
        try {
            $sql = <<<SQL
                SELECT r.id, r.id_usuario, r.id_espaco, r.inicio, r.fim, r.justificativa, r.estado
                FROM reserva r INNER JOIN espaco e ON r.id_espaco = e.id
                WHERE r.estado = :estado
                AND r.inicio >= :inicio
                AND r.fim <= :fim
                AND e.codigo_sala  = :codigoSala
                ORDER BY e.nome ASC, r.inicio ASC
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( [
                'inicio' => $inicio,
                'fim' => $fim,
                'estado' => $estado,
                'codigoSala' => $codigoSala
            ] );
            $dados = $stmt->fetchAll();
            return $dados;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

    public function buscarPorPeriodoEUsuario(string $inicio, string $fim, string $estado, int $idUsuario): array {
        try {
            $sql = <<<SQL
                SELECT r.id, r.id_usuario, r.id_espaco, r.inicio, r.fim, r.justificativa, r.estado
                FROM reserva r JOIN espaco e ON r.id_espaco = e.id
                WHERE r.estado = :estado
                AND r.inicio >= :inicio
                AND r.fim <= :fim
                AND r.id_usuario = :idUsuario
                ORDER BY e.nome ASC, r.inicio ASC
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( [
                'inicio' => $inicio,
                'fim' => $fim,
                'estado' => $estado,
                'idUsuario' => $idUsuario
            ] );
            $dados = $stmt->fetchAll();
            return $dados;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

    public function existeReservaEspaco(int $idEspaco, string $inicio, string $fim): bool {
        try{
            $sql = <<<SQL
                SELECT COUNT(*) AS quantidade FROM reserva
                WHERE id_espaco = :idEspaco
                AND estado = 'MARCADA'
                AND inicio < :fim
                AND fim > :inicio
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( [
                'idEspaco' => $idEspaco,
                'inicio' => $inicio,
                'fim' => $fim
            ] );
            $existe = ( $stmt->fetchColumn()>0 );
            return $existe;
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }    
    }

    public function atualizarEstado(int $idReserva, string $estado): void {
        try {
            $sql = <<<SQL
                UPDATE reserva SET estado = :estado WHERE id = :id
            SQL;
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $idReserva,
                'estado' => $estado
            ]);
        } catch (PDOException $erro) {
            throw new RepositorioException( $erro->getMessage() );
        }
    }

}


?>