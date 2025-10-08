<?php
namespace App\Psico\Models;
use PDO;


class Avaliacao {
    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    /**
     * Inserir nova avaliação
     */
    public function inserirAvaliacao(int $id_cliente, int $id_profissional, string $descricao_avaliacao, int $nota_avaliacao) {
        $sql = "INSERT INTO avaliacao (id_cliente, id_profissional, descricao_avaliacao, nota_avaliacao)
                VALUES (:id_cliente, :id_profissional, :descricao_avaliacao, :nota_avaliacao)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':descricao_avaliacao', $descricao_avaliacao);
        $stmt->bindParam(':nota_avaliacao', $nota_avaliacao, PDO::PARAM_INT);

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Buscar avaliação por ID
     */
    public function buscarAvaliacaoPorId(int $id): ?array {
        $sql = "SELECT * FROM avaliacao WHERE id_avaliacao = :id AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar todas as avaliações ativas
     */
    public function buscarAvaliacoes(): array {
        $sql = "SELECT * FROM avaliacao WHERE excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Deletar avaliação
     */
    public function deletarAvaliacao(int $id_avaliacao): int {
        $sql = "DELETE FROM avaliacao WHERE id_avaliacao = :id_avaliacao";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Atualizar avaliação
     */
    public function atualizarAvaliacao(int $id_avaliacao, string $descricao_avaliacao, int $nota_avaliacao): bool {
        $dataAtual = date('Y-m-d H:i:s');
        $sql = "UPDATE avaliacao
                SET descricao_avaliacao = :descricao_avaliacao,
                    nota_avaliacao = :nota_avaliacao,
                    atualizado_em = :atual
                WHERE id_avaliacao = :id_avaliacao";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
        $stmt->bindParam(':descricao_avaliacao', $descricao_avaliacao);
        $stmt->bindParam(':nota_avaliacao', $nota_avaliacao, PDO::PARAM_INT);
        $stmt->bindParam(':atual', $dataAtual);

        return $stmt->execute();
    }

    /**
     * Buscar avaliações por ID do Profissional, incluindo nome do cliente
     */
    public function buscarAvaliacoesPorProfissional(int $id_profissional): array {
        $sql = "
            SELECT 
                a.descricao_avaliacao AS comentario,
                a.nota_avaliacao AS nota,
                u.nome_usuario AS cliente
            FROM 
                avaliacao a
            INNER JOIN 
                usuario u ON a.id_cliente = u.id_usuario
            WHERE 
                a.id_profissional = :id_profissional AND a.excluido_em IS NULL
            ORDER BY 
                a.criado_em DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}