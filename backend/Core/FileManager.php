<?php

namespace App\Psico\Core;

class FileManager{
    private string $diretorioBase;
    public function __construct(string $diretorioBase){
        $this->diretorioBase = rtrim($diretorioBase, '/');
    }

    public function salvarArquivo(array $file,string $subDiretorio,array $tiposPermitidos = ['image/jpeg', 'image/png', 'application/pdf'],int $tamanhoMaximo = 2097152) {
        $this->validarArquivo($file, $tiposPermitidos, $tamanhoMaximo);

        $diretorioDestino = $this->diretorioBase . '/' . trim($subDiretorio, '/');
        if (!is_dir($diretorioDestino)) {
            if (!mkdir($diretorioDestino, 0755, true)) {
                throw new \Exception("Falha ao criar o diretório de destino.");
            }
        }

        $novoNome = $this->gerarNomeUnico($file);
        $diretorioFinal = $diretorioDestino . '/' . $novoNome;

        if (!move_uploaded_file($file['tmp_name'], $diretorioFinal)) {
            throw new \Exception("Falha ao mover o arquivo enviado.");
        }

        return trim($subDiretorio, '/') . '/' . $novoNome;
    }

    private function validarArquivo(array $file, array $tiposPermitidos, int $tamanhoMaximo){
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("Erro no upload do arquivo. Código: " . $file['error']);
        }
        if ($file['size'] > $tamanhoMaximo) {
            throw new \Exception("O arquivo excede o tamanho máximo de " . ($tamanhoMaximo / 1024 / 1024) . "MB.");
        }
        $tipoArquivo = mime_content_type($file['tmp_name']);
        if (!in_array($tipoArquivo, $tiposPermitidos)) {
            throw new \Exception("Tipo de arquivo inválido. Permitidos: " . implode(', ', $tiposPermitidos));
        }
    }

    private function gerarNomeUnico(array $file){
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        return uniqid('', true) . '.' . strtolower($extension);
    }
    
    public function delete(?string $caminhoRelativo){
        if (empty($caminhoRelativo)) {
            return true;
        }
        
        $caminhoCompleto = $this->diretorioBase . '/' . $caminhoRelativo;
        if (file_exists($caminhoCompleto) && is_file($caminhoCompleto)) {
            return unlink($caminhoCompleto);
        }
        return true;
    }
}