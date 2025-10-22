<?php

namespace App\Psico\Core;

class FileManager {
    private string $diretorioBase;

    // Construtor ligeiramente melhorado para garantir caminho absoluto
    public function __construct(string $diretorioBase) {
        $realBaseDir = realpath($diretorioBase);
        if ($realBaseDir === false) {
            throw new \InvalidArgumentException("O diretório base especificado não existe ou não é acessível: " . $diretorioBase);
        }
        $this->diretorioBase = $realBaseDir; // Usa o caminho absoluto resolvido
    }

    /**
     * Salva um arquivo enviado, determinando a extensão correta pelo tipo MIME.
     *
     * @param array $file Array $_FILES['input_name']
     * @param string $subDiretorio Subdiretório relativo à base onde salvar (ex: 'img/profissionais')
     * @param array $tiposPermitidos Array de tipos MIME permitidos (ex: ['image/jpeg', 'image/png'])
     * @param int $tamanhoMaximo Tamanho máximo em bytes
     * @return string Caminho relativo do arquivo salvo (ex: 'img/profissionais/nomeunico.jpg')
     * @throws \Exception Em caso de erro de upload, tipo inválido, tamanho excedido ou falha ao salvar.
     */
    public function salvarArquivo(
        array $file,
        string $subDiretorio,
        array $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'], // Adicionado webp para garantir
        int $tamanhoMaximo = 2097152 // 2MB
    ): string {

        // Valida e obtém o tipo MIME real
        $tipoArquivo = $this->validarArquivo($file, $tiposPermitidos, $tamanhoMaximo);

        // Cria o diretório de destino se não existir
        $diretorioDestino = $this->diretorioBase . '/' . trim($subDiretorio, '/');
        if (!is_dir($diretorioDestino)) {
            if (!mkdir($diretorioDestino, 0755, true)) { // O terceiro parâmetro 'true' permite criar diretórios aninhados
                throw new \Exception("Falha ao criar o diretório de destino: " . $diretorioDestino);
            }
        }

        // Determina a extensão CORRETA baseada no tipo MIME detectado
        $extension = match ($tipoArquivo) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            // Adicione outros mapeamentos se necessário
            default => throw new \Exception("Tipo MIME validado ('" . $tipoArquivo . "') não possui uma extensão mapeada."),
        };

        // Gera nome único e adiciona a extensão correta
        $nomeBaseUnico = $this->gerarNomeUnico();
        $novoNome = $nomeBaseUnico . '.' . $extension;
        $caminhoCompletoDestino = $diretorioDestino . '/' . $novoNome;

        // Move o arquivo enviado para o destino final
        if (!move_uploaded_file($file['tmp_name'], $caminhoCompletoDestino)) {
            // Adiciona mais detalhes ao erro, se possível
            $error = error_get_last();
            $errorMessage = $error ? $error['message'] : 'desconhecido';
            throw new \Exception("Falha ao mover o arquivo enviado para '" . $caminhoCompletoDestino . "'. Erro: " . $errorMessage);
        }
        @chmod($caminhoCompletoDestino, 0644); // Tenta definir permissão 644 (leitura para todos)
        
        // Retorna o caminho RELATIVO para ser salvo no banco de dados
        return trim($subDiretorio, '/') . '/' . $novoNome;
    }

    /**
     * Valida o arquivo enviado.
     *
     * @param array $file Array $_FILES
     * @param array $tiposPermitidos Tipos MIME permitidos
     * @param int $tamanhoMaximo Tamanho máximo em bytes
     * @return string O tipo MIME validado do arquivo
     * @throws \Exception Se a validação falhar
     */
    private function validarArquivo(array $file, array $tiposPermitidos, int $tamanhoMaximo): string {
        // Verifica erros de upload do PHP
        if (!isset($file['error']) || is_array($file['error'])) {
             throw new \RuntimeException('Parâmetros de arquivo inválidos.');
        }
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break; // Sem erro, continua
            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException('Nenhum arquivo enviado.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException('Arquivo excede o limite de tamanho permitido.');
            default:
                throw new \RuntimeException('Erro desconhecido no upload do arquivo.');
        }

        // Verifica tamanho do arquivo
        if ($file['size'] > $tamanhoMaximo) {
            throw new \RuntimeException("O arquivo excede o tamanho máximo de " . ($tamanhoMaximo / 1024 / 1024) . "MB.");
        }

        // Verifica o tipo MIME real do arquivo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $tipoArquivo = $finfo->file($file['tmp_name']);
         if (false === $tipoArquivo) { // Verifica se finfo falhou
            throw new \RuntimeException('Falha ao verificar o tipo MIME do arquivo.');
        }


        if (!in_array($tipoArquivo, $tiposPermitidos)) {
            $originalExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            throw new \RuntimeException(
                "Tipo de arquivo inválido ('" . htmlspecialchars($tipoArquivo) .
                "' detectado para arquivo com nome original '" . htmlspecialchars($file['name']) .
                "'). Permitidos: " . implode(', ', $tiposPermitidos)
            );
        }

        return $tipoArquivo; // Retorna o tipo MIME validado
    }

    /**
     * Gera uma string base única para o nome do arquivo.
     */
    private function gerarNomeUnico(): string {
        // Gera um ID único mais seguro e com mais entropia
        return bin2hex(random_bytes(8)) . time();
    }

    /**
     * Deleta um arquivo dado seu caminho relativo à base.
     *
     * @param string|null $caminhoRelativo Caminho relativo (ex: 'img/profissionais/nome.jpg') ou null/vazio.
     * @return bool True se deletado com sucesso ou se não havia nada para deletar, False em caso de falha.
     */
    public function delete(?string $caminhoRelativo): bool {
        if (empty($caminhoRelativo)) {
            return true; // Considera sucesso se não há caminho
        }

        // Garante que não haja barras extras no início
        $caminhoRelativoLimpo = ltrim($caminhoRelativo, '/');
        $caminhoCompleto = $this->diretorioBase . '/' . $caminhoRelativoLimpo;

        // Verifica se o arquivo existe e é realmente um arquivo (não um diretório)
        if (is_file($caminhoCompleto)) {
            // Tenta deletar e retorna o sucesso/falha
            if (!unlink($caminhoCompleto)) {
                 error_log("Falha ao deletar arquivo: " . $caminhoCompleto); // Loga o erro para debug
                 return false; // Indica falha
            }
            return true; // Deletado com sucesso
        } elseif (file_exists($caminhoCompleto)) {
             // Existe mas não é um arquivo (pode ser diretório, link simbólico, etc.)
             error_log("Tentativa de deletar algo que não é um arquivo: " . $caminhoCompleto);
             return false; // Falha pois não era um arquivo esperado
        }

        // Se o arquivo nem existia, considera como 'já deletado'
        return true;
    }
}