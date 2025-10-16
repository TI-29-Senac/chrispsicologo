<?php
namespace App\Psico\Models;
use PDO;

class Profissional {
    private $db;
    private $table = 'profissional';

    public function __construct(PDO $db) {
        $this->db = $db;
    }

public function inserirProfissional(int $id_usuario, string $especialidade, float $valor, float $sinal, int $publico, ?string $sobre, int $ordem_exibicao) {
    $sql = "INSERT INTO {$this->table} (id_usuario, especialidade, sobre, valor_consulta, sinal_consulta, publico, ordem_exibicao) 
            VALUES (:id_usuario, :especialidade, :sobre, :valor, :sinal, :publico, :ordem)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->bindParam(':especialidade', $especialidade);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':sinal', $sinal);
    $stmt->bindParam(':publico', $publico, PDO::PARAM_INT);
    $stmt->bindParam(':sobre', $sobre);
    $stmt->bindParam(':ordem', $ordem_exibicao, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return $this->db->lastInsertId();
    }
    return false;
}
    
public function paginacao(int $pagina = 1, int $por_pagina = 10): array {
        $offset = ($pagina - 1) * $por_pagina;

        $totalQuery = "SELECT COUNT(*) FROM {$this->table} p"; // REMOVIDO: WHERE p.excluido_em IS NULL
        $totalStmt = $this->db->query($totalQuery);
        $total_de_registros = $totalStmt->fetchColumn();

        $dataQuery = "
            SELECT p.*, u.nome_usuario, u.email_usuario, u.status_usuario, u.tipo_usuario
            FROM {$this->table} p
            JOIN usuario u ON p.id_usuario = u.id_usuario
            ORDER BY 
            id_profissional ASC
            LIMIT :limit OFFSET :offset"; // REMOVIDO: WHERE p.excluido_em IS NULL
        
        $dataStmt = $this->db->prepare($dataQuery);
        $dataStmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        $dados = $dataStmt->fetchAll(PDO::FETCH_OBJ); 

        return [
            'data' => $dados,
            'total' => (int) $total_de_registros,
            'por_pagina' => (int) $por_pagina,
            'pagina_atual' => (int) $pagina,
            'ultima_pagina' => (int) ceil($total_de_registros / $por_pagina)
        ];
    }

public function listarProfissionais(): array {
        $sql = "
            SELECT 
                p.id_profissional,
                p.id_usuario,
                p.img_profissional,
                p.especialidade,
                p.valor_consulta,
                p.sinal_consulta,
                u.nome_usuario, 
                u.email_usuario, 
                u.status_usuario, 
                u.tipo_usuario
            FROM {$this->table} p
            JOIN usuario u ON p.id_usuario = u.id_usuario
            ORDER BY u.nome_usuario ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

 public function buscarProfissionalPorId(int $id_profissional) {
        $sql = "
            SELECT 
                p.*,
                u.nome_usuario,
                u.email_usuario,
                u.tipo_usuario,
                u.status_usuario
            FROM 
                {$this->table} p
            JOIN 
                usuario u ON p.id_usuario = u.id_usuario
            WHERE 
                p.id_profissional = :id_profissional
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

public function atualizarProfissional(int $id_profissional, string $especialidade, float $valor, float $sinal, int $publico, ?string $sobre, int $ordem_exibicao): bool {
    $sql = "UPDATE {$this->table}
            SET especialidade = :especialidade, sobre = :sobre, valor_consulta = :valor,
                sinal_consulta = :sinal, publico = :publico, ordem_exibicao = :ordem,
                atualizado_em = NOW()
            WHERE id_profissional = :id_profissional";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
    $stmt->bindParam(':especialidade', $especialidade);
    $stmt->bindParam(':sobre', $sobre);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':sinal', $sinal);
    $stmt->bindParam(':publico', $publico, PDO::PARAM_INT);
    $stmt->bindParam(':ordem', $ordem_exibicao, PDO::PARAM_INT); // Adicione esta linha

    return $stmt->execute();
}

    public function deletarProfissional(int $id_profissional): bool {
        $sql = "UPDATE {$this->table} SET excluido_em = NOW() WHERE id_profissional = :id_profissional";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        return $stmt->execute();
    }

public function listarProfissionaisPublicos(): array {
        $sql = "
            SELECT 
                p.id_profissional,
                p.img_profissional,
                p.especialidade,
                p.sobre, -- Adicione esta linha
                p.valor_consulta,
                u.nome_usuario
            FROM {$this->table} p
            JOIN usuario u ON p.id_usuario = u.id_usuario
            WHERE p.publico = 1 AND u.status_usuario = 'ativo'
            ORDER BY p.ordem_exibicao ASC, u.nome_usuario ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function buscarProfissionalPublicoPorId(int $id_profissional) {
        $sql = "
            SELECT 
                p.*,
                u.nome_usuario
            FROM 
                {$this->table} p
            JOIN 
                usuario u ON p.id_usuario = u.id_usuario
            WHERE 
                p.id_profissional = :id_profissional
                AND p.publico = 1
                AND u.status_usuario = 'ativo'
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}