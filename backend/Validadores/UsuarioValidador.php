<?php
namespace App\Psico\Validadores;

class UsuarioValidador {
    public static function ValidarEntradas($dados){
        $erros = [];
        // Nome
        if (isset($dados['nome_usuario']) && empty($dados['nome_usuario'])){
            $erros[] = "O campo nome é obrigatório.";
        }
        // Email
        if (isset($dados['email_usuario']) && empty($dados['email_usuario'])) {
            $erros[] = "Email inválido.";
        } elseif(!filter_var($dados['email_usuario'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "O campo de email deve ser um endereço de email válido .";
        }
        // Senha
        if (isset($dados['senha_usuario']) && empty($dados['senha_usuario'])){
            $erros[] = "O campo senha é obrigatório.";
        }elseif (strlen($dados['senha_usuario']) < 6) {
            $erros[] = "A senha deve ter pelo menos 6 caracteres.";
        }
        return $erros;
    }
}
