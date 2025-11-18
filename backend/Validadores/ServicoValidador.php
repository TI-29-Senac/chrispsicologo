<?php
namespace App\Psico\Validadores;

/**
 * Valida os dados de entrada para o CRUD de Serviços.
 * Baseado na estrutura da tabela 'servicos'.
 */
class ServicoValidador {
    
    /**
     * Valida as entradas do formulário de criação/edição de serviço.
     *
     * @param array $dados Os dados do formulário ($_POST).
     * @param bool $isUpdate Se for 'true', não valida a obrigatoriedade do arquivo de ícone.
     * @return array Um array com as mensagens de erro, se houver.
     */
    public static function ValidarEntradas($dados, $isUpdate = false){
        $erros = [];
        
        // --- Título ---
        // Com base na coluna 'titulo' (VARCHAR 100, NN)
        if (empty($dados['titulo'])){
            $erros[] = "O campo 'Título' é obrigatório.";
        }
        
        // --- Descrição ---
        // Com base na coluna 'descricao' (TEXT, NN)
        if (empty($dados['descricao'])){
            $erros[] = "O campo 'Descrição' é obrigatório.";
        }

        // --- Ícone (Upload) ---
        // Com base na coluna 'icone_path' (VARCHAR 255, NN)
        // O ícone só é obrigatório na criação (quando $isUpdate é false).
        if (!$isUpdate) {
            if (!isset($_FILES['icone_path']) || empty($_FILES['icone_path']['name']) || $_FILES['icone_path']['error'] != UPLOAD_ERR_OK) {
                $erros[] = "O campo 'Ícone do Serviço' é obrigatório na criação.";
            }
        }
        
        // As colunas 'ativo', 'criado_em', etc., são tratadas pelo Controller/Model
        // e não precisam de validação de dados de entrada aqui.
        
        return $erros;
    }
}