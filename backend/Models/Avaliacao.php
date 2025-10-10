<?php
namespace App\Psico\Models;
use PDO;
 
class Avaliacao {
    private PDO $db;
    private $table = 'avaliacao'; // Nome da tabela atualizado
 
    public function __construct(PDO $db){
        $this->db = $db;
    }
 
    public function inserirAvaliacao(int $id_cliente, int $id_profissional, string $descricao, int $nota) {
        $sql = "INSERT INTO {$this->table} (id_cliente, id_profissional, descricao_avaliacao, nota_avaliacao)
                VALUES (:id_cliente, :id_profissional, :descricao, :nota)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':nota', $nota, PDO::PARAM_INT);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }
 
    public function buscarAvaliacoesPorProfissional(int $id_profissional): array {
        $sql = "
            SELECT
                a.descricao_avaliacao AS comentario,
                a.nota_avaliacao AS nota,
                u.nome_usuario AS cliente
            FROM {$this->table} a
            JOIN Usuario u ON a.id_cliente = u.id_usuario
            WHERE a.id_profissional = :id_profissional AND a.excluido_em IS NULL
            ORDER BY a.criado_em DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   
    public function buscarAvaliacoes(): array {
        $sql = "SELECT * FROM {$this->table} WHERE excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletarAvaliacao(int $id_avaliacao): bool {

    $sql = "UPDATE {$this->table}

            SET excluido_em = NOW()

            WHERE id_avaliacao = :id_avaliacao AND excluido_em IS NULL";

    $stmt = $this->db->prepare($sql);

    $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);

    return $stmt->execute();

}
 
public function buscarAvaliacaoPorId(int $id_avaliacao): ?array {

    $sql = "SELECT 

                a.id_avaliacao,

                a.id_usuario,

                a.id_profissional,

                a.descricao_avaliacao AS comentario,

                a.nota_avaliacao AS nota,

                a.criado_em,

                u.nome_usuario AS cliente

            FROM {$this->table} a

            JOIN usuario u ON a.id_cliente = u.id_usuario

            WHERE a.id_avaliacao = :id_avaliacao

              AND a.excluido_em IS NULL

            LIMIT 1";
 
    $stmt = $this->db->prepare($sql);

    $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);

    $stmt->execute();

    $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

    return $avaliacao ?: null;

}
public function atualizarAvaliacao(int $id_avaliacao, string $descricao, int $nota): bool {
    $sql = "UPDATE {$this->table}
            SET 
                descricao_avaliacao = :descricao,
                nota_avaliacao = :nota,
                atualizado_em = NOW()
            WHERE id_avaliacao = :id_avaliacao
              AND excluido_em IS NULL";
 
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':nota', $nota, PDO::PARAM_INT);
    $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
    return $stmt->execute();
}
}