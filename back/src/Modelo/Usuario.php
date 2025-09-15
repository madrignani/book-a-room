<?php


namespace App\Modelo;


abstract class Usuario {

    private int $id;
    private string $matricula;
    private string $nome;
    private string $email;

    public function __construct(
        int $id,
        string $matricula,
        string $nome,
        string $email
    ) {
        $this->id = $id;
        $this->matricula = $matricula;
        $this->nome = $nome;
        $this->email = $email;
    }

    public function getId(): int { return $this->id; }
    public function getMatricula(): string { return $this->matricula; }
    public function getNome(): string { return $this->nome; }
    public function getEmail(): string { return $this->email; }
    abstract public function getTipoUsuario(): string;

    public function validar() : array {
        $problemas = [];
        if ( mb_strlen($this->nome) < 3 || mb_strlen($this->nome) > 60 ){
            $problemas[] = "O nome do Usuário deve conter entre 3 e 60 caracteres.";
        }
        if ( mb_strlen($this->matricula) !== 4 ) {
            $problemas[] = "A matrícula do Usuário deve conter 4 caracteres.";
        }
        if ( !str_ends_with($this->email, '@acme.br') ) {
            $problemas[] = "O e-mail do Usuário deve ser institucional, '@acme.br'.";
        }
        return $problemas;
    }

}


?>