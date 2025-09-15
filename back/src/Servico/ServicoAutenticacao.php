<?php


namespace App\Servico;
use App\Excecao\DominioException;
use App\Modelo\Usuario;
use App\Modelo\Funcionario;
use App\Modelo\Aluno;
use App\Modelo\TipoFuncionario;
use App\Repositorio\RepositorioUsuario;


class ServicoAutenticacao {
    
    private RepositorioUsuario $repositorioUsuario;

    public function __construct(RepositorioUsuario $repositorioUsuario) {
        $this->repositorioUsuario = $repositorioUsuario;
    }

    public function buscarUsuarioPorMatriculaOuEmail(string $valor): ?array {
        $dados = $this->repositorioUsuario->buscarPorMatriculaOuEmail($valor);
        return $dados;
    }

    public function verificarHashUsuario(int $idUsuario, string $hash): bool {
        $existe = $this->repositorioUsuario->verificarHash($idUsuario, $hash);
        return $existe;
    }

    public function mapearUsuario(array $dados): Usuario {
        if ( $dados['tipo_usuario'] === 'FUNCIONARIO' ) {
            $usuario = new Funcionario(
                ( (int) $dados['id'] ),
                $dados['matricula'],
                $dados['nome'],
                $dados['email'],
                ( TipoFuncionario::from($dados['tipo_funcionario']) ),
                ( (bool) $dados['cargo_gestao'] )
            );
        } else {
            $usuario = new Aluno(
                ( (int) $dados['id'] ),
                $dados['matricula'],
                $dados['nome'],
                $dados['email'],
            );
        }
        $problemas = $usuario->validar();
        if ( !empty($problemas) ) {
            throw DominioException::comProblemas($problemas);
        }
        return $usuario;
    }

}


?>