<?php
// ti-29-senac/chrispsicologo/chrispsicologo-backend-correto2/backend/Models/Servico.php
namespace App\Psico\Models;
use PDO;

class Servico {
    private PDO $db;
    private $table = 'servicos';

    public function __construct(PDO $db){
        $this->db = $db;
    }

    /**
     * API: Busca todos os serviços ativos para o site.
     */
    public function buscarTodosAtivos(): array {
        $sql = "SELECT id, titulo, descricao, icone_path FROM {$this->table}
                WHERE ativo = 1 AND excluido_em IS NULL
                ORDER BY id ASC"; 
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * ADMIN: Insere um novo serviço.
     */
    public function inserirServico(string $titulo, string $descricao, string $icone_path, int $ativo): int|false {
        $sql = "INSERT INTO {$this->table} (titulo, descricao, icone_path, ativo, criado_em, atualizado_em)
                VALUES (:titulo, :descricao, :icone_path, :ativo, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':icone_path', $icone_path);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
        
        return $stmt->execute() ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * ADMIN: Atualiza um serviço existente.
     */
    public function atualizarServico(int $id, string $titulo, string $descricao, string $icone_path, int $ativo): bool {
        $sql = "UPDATE {$this->table}
                SET titulo = :titulo, 
                    descricao = :descricao, 
                    icone_path = :icone_path, 
                    ativo = :ativo, 
                    atualizado_em = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':icone_path', $icone_path);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * ADMIN: Marca um serviço como excluído (Soft Delete).
     */
    public function deletarServico(int $id): bool {
        $sql = "UPDATE {$this->table} 
                SET excluido_em = NOW(), ativo = 0
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * ADMIN: Busca um serviço único pelo ID (para editar/excluir).
     */
    public function buscarServicoPorId(int $id): object|false {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id AND excluido_em IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * ADMIN: Busca todos os serviços (incluindo inativos) para stats.
     */
    public function buscarTodosServicos(): array {
        $sql = "SELECT id, ativo FROM {$this->table} WHERE excluido_em IS NULL";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * ADMIN: Paginação para a lista do dashboard.
     * (Este era o método que estava faltando)
     */
    public function paginacao(int $pagina = 1, int $por_pagina = 10): array {
        $offset = ($pagina - 1) * $por_pagina;

        // Contagem total (filtrando excluídos)
        $totalQuery = "SELECT COUNT(*) FROM {$this->table} WHERE excluido_em IS NULL";
        $total_de_registros = $this->db->query($totalQuery)->fetchColumn();

        // Busca paginada (sem JOIN, filtrando excluídos)
        $dataQuery = "SELECT * FROM {$this->table} 
                      WHERE excluido_em IS NULL 
                      ORDER BY id ASC
                      LIMIT :limit OFFSET :offset";
        
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
}