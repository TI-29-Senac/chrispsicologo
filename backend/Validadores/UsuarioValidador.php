<?php
namespace App\Psico\Validadores;

class UsuarioValidador {
    public static function ValidarEntradas($dados, $isUpdate = false){
        $erros = [];
        // Nome
        if (empty($dados['nome_usuario'])){
            $erros['nome_usuario'] = "O campo nome é obrigatório.";
        }
        // Email
        if (empty($dados['email_usuario'])) {
            $erros['email_usuario'] = "O campo email é obrigatório.";
        } elseif(!filter_var($dados['email_usuario'], FILTER_VALIDATE_EMAIL)) {
            $erros['email_usuario'] = "O campo de email deve ser um endereço de email válido.";
        }
        // Senha - não validar se for uma atualização e a senha estiver vazia
        if (!$isUpdate || !empty($dados['senha_usuario'])) {
            if (empty($dados['senha_usuario'])){
                $erros['senha_usuario'] = "O campo senha é obrigatório.";
            }elseif (strlen($dados['senha_usuario']) < 6) {
                $erros['senha_usuario'] = "A senha deve ter pelo menos 6 caracteres.";
            }
        }
        return $erros;
    }
}