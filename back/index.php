<?php


require_once __DIR__ . '/vendor/autoload.php';


use App\Autenticacao\Autenticacao;
use App\Autenticacao\Sessao;
use App\Config\Conexao;
use App\Excecao\AutenticacaoException;
use App\Excecao\DominioException;
use App\Excecao\RepositorioException;
use App\Transacao\TransacaoPDO;
use App\Servico\ServicoAutenticacao;
use App\Servico\ServicoCadastroReserva;
use App\Servico\ServicoListagemReserva;
use App\Servico\ServicoCancelamentoReserva;
use App\Repositorio\RepositorioUsuarioBDR;
use App\Repositorio\RepositorioTipoEspacoBDR;
use App\Repositorio\RepositorioEspacoBDR;
use App\Repositorio\RepositorioReservaBDR;
use App\Dto\UsuarioDTO;


use \phputil\router\Router;
use function \phputil\cors\cors;


date_default_timezone_set('America/Sao_Paulo');


$app = new Router();


$app->use( cors([
    'origin' => ['http://localhost:5173', 'http://localhost:8080'],
    'allowedHeaders' => ['Host', 'Origin', 'Accept', 'Content-Type'],
    'exposeHeaders' => ['Content-Type'],
    'allowMethods' => ['GET','POST','PATCH','DELETE','OPTIONS'],
    'allowCredentials' => true
]) );



////////////////////////////////////////////////////////////////////////



$app->post( '/login', function($req, $res) {
    try {
        $dados = ( (array)$req->body() );
        if ( (empty($dados['login'])) || (empty($dados['senha'])) ) {
            throw DominioException::comProblemas( ['Login e senha são obrigatórios.'] );
        }
        $login = ( (string)$dados['login'] );
        $senha = ( (string)$dados['senha'] );
        $pdo = Conexao::conectar();
        $repositorio = new RepositorioUsuarioBDR($pdo);
        $servico = new ServicoAutenticacao($repositorio);
        $autenticacao = new Autenticacao($servico);
        $usuario = $autenticacao->autenticar($login, $senha);
        $sessao = new Sessao();
        $sessao->criaSessao($usuario);
        $res->status(200)->end();
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (DominioException $erro) {
        $res->status(400)->json( ['mensagens' => $erro->getProblemas()] );
    }  catch (RepositorioException $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no repositório -> ' . $erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->post( '/logout', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $sessao->destruir();
        $res->status(200)->end();
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->get( '/me', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $res->status(200)->end();
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->get( '/dados-usuario', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $logado = $sessao->dadosUsuarioLogado();
        $usuarioDto = new UsuarioDTO (
            $logado['id_usuario'],
            $logado['matricula_usuario'],
            $logado['nome_usuario'],
            $logado['tipo_usuario'],
            $logado['tipo_funcionario'],
            $logado['cargo_gestao']
        );
        $res->status(200)->json( $usuarioDto->arrayDados() );
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->get( '/tipos-espaco', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $logado = $sessao->dadosUsuarioLogado();
        $pdo = Conexao::conectar();
        $transacao = new TransacaoPDO($pdo);
        $repositorioTp = new RepositorioTipoEspacoBDR($pdo);
        $repositorioE = new RepositorioEspacoBDR($pdo);
        $repositorioU = new RepositorioUsuarioBDR($pdo);
        $repositorioR = new RepositorioReservaBDR($pdo);
        $servico = new ServicoCadastroReserva($transacao, $repositorioTp, $repositorioE, $repositorioU, $repositorioR);
        $tipos = $servico->buscarTiposEspaco( $logado['tipo_usuario'], $logado['tipo_funcionario'], $logado['cargo_gestao'] );
        $res->status(200)->json($tipos);
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (RepositorioException $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no repositório -> ' . $erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->get( '/espacos-por-tipo/:idTipoEspaco', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $idTipoEspaco = ( (int)$req->param('idTipoEspaco') );
        $pdo = Conexao::conectar();
        $transacao = new TransacaoPDO($pdo);
        $repositorioTp = new RepositorioTipoEspacoBDR($pdo);
        $repositorioE = new RepositorioEspacoBDR($pdo);
        $repositorioU = new RepositorioUsuarioBDR($pdo);
        $repositorioR = new RepositorioReservaBDR($pdo);
        $servico = new ServicoCadastroReserva($transacao, $repositorioTp, $repositorioE, $repositorioU, $repositorioR);
        $espacos = $servico->buscarEspacosPorTipo($idTipoEspaco);
        $res->status(200)->json($espacos);
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (RepositorioException $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no repositório -> ' . $erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->post( '/reservas', function($req, $res) {
    try{
        $sessao = new Sessao();
        $sessao->estaLogado();
        $logado = $sessao->dadosUsuarioLogado();
        $dados = ( (array)$req->body() );
        if ( (empty($dados['idEspaco'])) || (empty($dados['inicio'])) || (empty($dados['fim'])) ) {
            throw new Exception( ['Dados necessários para cadastrar a Reserva não foram recebidos.'] );
        }
        $idEspaco = $dados['idEspaco'];
        $inicio = $dados['inicio'];
        $fim = $dados['fim'];
        $justificativa = null;
        if ( !empty($dados['justificativa']) ){
            $justificativa = ( (string)$dados['justificativa'] );
        }
        $pdo = Conexao::conectar();
        $transacao = new TransacaoPDO($pdo);
        $repositorioTp = new RepositorioTipoEspacoBDR($pdo);
        $repositorioE = new RepositorioEspacoBDR($pdo);
        $repositorioU = new RepositorioUsuarioBDR($pdo);
        $repositorioR = new RepositorioReservaBDR($pdo);
        $servico = new ServicoCadastroReserva($transacao, $repositorioTp, $repositorioE, $repositorioU, $repositorioR);
        $servico->cadastrarReserva(
            $idEspaco, $inicio, $fim, $justificativa, 
            $logado['id_usuario'], $logado['tipo_usuario'], $logado['tipo_funcionario'], $logado['cargo_gestao'] 
        );
        $res->status(200)->end();
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (DominioException $erro) {
        $res->status(400)->json( ['mensagens' => $erro->getProblemas()] );
    }  catch (RepositorioException $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no repositório -> ' . $erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->get( '/reservas', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $inicio = $_GET['inicio'];
        $fim = $_GET['fim'];
        $estado = $_GET['estado'];
        $pdo = Conexao::conectar();
        $repositorioTp = new RepositorioTipoEspacoBDR($pdo);
        $repositorioE = new RepositorioEspacoBDR($pdo);
        $repositorioU = new RepositorioUsuarioBDR($pdo);
        $repositorioR = new RepositorioReservaBDR($pdo);
        $servico = new ServicoListagemReserva($repositorioTp, $repositorioE, $repositorioU, $repositorioR);
        $dataValidada = $servico->validarData($inicio, $fim);
        $reservas = $servico->buscarReservas($dataValidada['inicio'], $dataValidada['fim'], $estado);
        $res->status(200)->json($reservas);
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (DominioException $erro) {
        $res->status(400)->json( ['mensagens' => $erro->getProblemas()] );
    } catch (RepositorioException $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no repositório -> ' . $erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->get( '/reservas-te', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $inicio = $_GET['inicio'];
        $fim = $_GET['fim'];
        $estado = $_GET['estado'];
        $idTipoEspaco = ( (int)$_GET['tipo'] );
        $pdo = Conexao::conectar();
        $repositorioTp = new RepositorioTipoEspacoBDR($pdo);
        $repositorioE = new RepositorioEspacoBDR($pdo);
        $repositorioU = new RepositorioUsuarioBDR($pdo);
        $repositorioR = new RepositorioReservaBDR($pdo);
        $servico = new ServicoListagemReserva($repositorioTp, $repositorioE, $repositorioU, $repositorioR);
        $dataValidada = $servico->validarData($inicio, $fim);
        $reservas = $servico->buscarReservasTe($dataValidada['inicio'], $dataValidada['fim'], $estado, $idTipoEspaco);
        $res->status(200)->json($reservas);
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (DominioException $erro) {
        $res->status(400)->json( ['mensagens' => $erro->getProblemas()] );
    } catch (RepositorioException $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no repositório -> ' . $erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->get( '/reservas-sala', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $inicio = $_GET['inicio'];
        $fim = $_GET['fim'];
        $estado = $_GET['estado'];
        $codigoSala = $_GET['sala'];
        $pdo = Conexao::conectar();
        $repositorioTp = new RepositorioTipoEspacoBDR($pdo);
        $repositorioE = new RepositorioEspacoBDR($pdo);
        $repositorioU = new RepositorioUsuarioBDR($pdo);
        $repositorioR = new RepositorioReservaBDR($pdo);
        $servico = new ServicoListagemReserva($repositorioTp, $repositorioE, $repositorioU, $repositorioR);
        $dataValidada = $servico->validarData($inicio, $fim);
        $reservas = $servico->buscarReservasSala($dataValidada['inicio'], $dataValidada['fim'], $estado, $codigoSala);
        $res->status(200)->json($reservas);
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (DominioException $erro) {
        $res->status(400)->json( ['mensagens' => $erro->getProblemas()] );
    } catch (RepositorioException $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no repositório -> ' . $erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->get( '/reservas-usuario', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $inicio = $_GET['inicio'];
        $fim = $_GET['fim'];
        $estado = $_GET['estado'];
        $matriculaUsuario = $_GET['matricula'];
        $pdo = Conexao::conectar();
        $repositorioTp = new RepositorioTipoEspacoBDR($pdo);
        $repositorioE = new RepositorioEspacoBDR($pdo);
        $repositorioU = new RepositorioUsuarioBDR($pdo);
        $repositorioR = new RepositorioReservaBDR($pdo);
        $servico = new ServicoListagemReserva($repositorioTp, $repositorioE, $repositorioU, $repositorioR);
        $dataValidada = $servico->validarData($inicio, $fim);
        $reservas = $servico->buscarReservasMatricula($dataValidada['inicio'], $dataValidada['fim'], $estado, $matriculaUsuario);
        $res->status(200)->json($reservas);
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (DominioException $erro) {
        $res->status(400)->json( ['mensagens' => $erro->getProblemas()] );
    } catch (RepositorioException $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no repositório -> ' . $erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );

$app->patch( '/reservas-cancelar/:id', function($req, $res) {
    try {
        $sessao = new Sessao();
        $sessao->estaLogado();
        $logado = $sessao->dadosUsuarioLogado();
        $idReserva = ( (int)$req->param('id') );
        $pdo = Conexao::conectar();
        $transacao = new TransacaoPDO($pdo);
        $repositorioU = new RepositorioUsuarioBDR($pdo);
        $repositorioR = new RepositorioReservaBDR($pdo);
        $servico = new ServicoCancelamentoReserva($transacao, $repositorioU, $repositorioR);
        $servico->cancelar(
            $idReserva,
            $logado['id_usuario'], $logado['tipo_usuario'], $logado['tipo_funcionario'], $logado['cargo_gestao']
        );
        $res->status(200)->end();
    } catch (AutenticacaoException $erro) {
        $res->status(401)->json( ['mensagens' => [$erro->getMessage()]] );
    } catch (DominioException $erro) {
        $res->status(400)->json( ['mensagens' => $erro->getProblemas()] );
    } catch (RepositorioException $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no repositório -> ' . $erro->getMessage()]] );
    } catch (Exception $erro) {
        $res->status(500)->json( ['mensagens' => ['Erro no servidor -> ' . $erro->getMessage()]] );
    }
} );


$app->listen();


?>