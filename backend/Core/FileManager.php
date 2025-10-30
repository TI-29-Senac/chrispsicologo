<?php

namespace App\Psico\Core;

class FileManager {
    private string $diretorioBase;

    
    public function __construct(string $diretorioBase) {
        $realBaseDir = realpath($diretorioBase);
        if ($realBaseDir === false) {
            throw new \InvalidArgumentException("O diretório base especificado não existe ou não é acessível: " . $diretorioBase);
        }
        $this->diretorioBase = $realBaseDir; 
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
        array $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg',  'application/pdf'], 
        int $tamanhoMaximo = 2097152 
    ): string {

        
        $tipoArquivo = $this->validarArquivo($file, $tiposPermitidos, $tamanhoMaximo);

        
        $diretorioDestino = $this->diretorioBase . '/' . trim($subDiretorio, '/');
        if (!is_dir($diretorioDestino)) {
            if (!mkdir($diretorioDestino, 0755, true)) { 
                throw new \Exception("Falha ao criar o diretório de destino: " . $diretorioDestino);
            }
        }

        
        $extension = match ($tipoArquivo) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            
            default => throw new \Exception("Tipo MIME validado ('" . $tipoArquivo . "') não possui uma extensão mapeada."),
        };

        
        $nomeBaseUnico = $this->gerarNomeUnico();
        $novoNome = $nomeBaseUnico . '.' . $extension;
        $caminhoCompletoDestino = $diretorioDestino . '/' . $novoNome;

        
        if (!move_uploaded_file($file['tmp_name'], $caminhoCompletoDestino)) {
            
            $error = error_get_last();
            $errorMessage = $error ? $error['message'] : 'desconhecido';
            throw new \Exception("Falha ao mover o arquivo enviado para '" . $caminhoCompletoDestino . "'. Erro: " . $errorMessage);
        }
        @chmod($caminhoCompletoDestino, 0644); 
        
        
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
        
        if (!isset($file['error']) || is_array($file['error'])) {
             throw new \RuntimeException('Parâmetros de arquivo inválidos.');
        }
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break; 
            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException('Nenhum arquivo enviado.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException('Arquivo excede o limite de tamanho permitido.');
            default:
                throw new \RuntimeException('Erro desconhecido no upload do arquivo.');
        }

        
        if ($file['size'] > $tamanhoMaximo) {
            throw new \RuntimeException("O arquivo excede o tamanho máximo de " . ($tamanhoMaximo / 1024 / 1024) . "MB.");
        }

        
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $tipoArquivo = $finfo->file($file['tmp_name']);
         if (false === $tipoArquivo) { 
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

        return $tipoArquivo; 
    }

    /**
     * Gera uma string base única para o nome do arquivo.
     */
    private function gerarNomeUnico(): string {
        
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
            return true; 
        }

        
        $caminhoRelativoLimpo = ltrim($caminhoRelativo, '/');
        $caminhoCompleto = $this->diretorioBase . '/' . $caminhoRelativoLimpo;

        
        if (is_file($caminhoCompleto)) {
            
            if (!unlink($caminhoCompleto)) {
                 error_log("Falha ao deletar arquivo: " . $caminhoCompleto); 
                 return false; 
            }
            return true; 
        } elseif (file_exists($caminhoCompleto)) {
             
             error_log("Tentativa de deletar algo que não é um arquivo: " . $caminhoCompleto);
             return false; 
        }

        
        return true;
    }
}