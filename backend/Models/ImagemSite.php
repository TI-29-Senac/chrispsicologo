<?php
namespace App\Psico\Models;

use PDO;

class ImagemSite {
    private PDO $db;
    private string $table = 'imagem_site';
    
    private string $secaoTable = 'pagina_site'; 

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Insere uma nova imagem no banco de dados.
     */
    public function inserirImagem(int $id_secao, string $url_imagem, int $ordem = 99): int|false {
        $sql = "INSERT INTO {$this->table} (id_secao, url_imagem, ordem)
                VALUES (:id_secao, :url_imagem, :ordem)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->bindParam(':url_imagem', $url_imagem);
        $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);

        return $stmt->execute() ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Busca todas as imagens de uma seção específica, ordenadas.
     */
    public function buscarImagensPorSecao(int $id_secao): array {
        $sql = "SELECT * FROM {$this->table}
                WHERE id_secao = :id_secao AND excluido_em IS NULL
                ORDER BY ordem ASC, criado_em DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

     /**
      * Busca todas as imagens agrupadas por seção para o dashboard.
      */
     public function buscarTodasAgrupadasPorSecao(): array {
         
         $sql = "SELECT i.*, s.nome_pagina as nome_secao
                 FROM {$this->table} i
                 JOIN {$this->secaoTable} s ON i.id_secao = s.id_pagina -- Ajuste s.id_pagina se necessário
                 WHERE i.excluido_em IS NULL
                 ORDER BY s.nome_pagina ASC, i.ordem ASC";
         $stmt = $this->db->query($sql);
         $imagens = $stmt->fetchAll(PDO::FETCH_OBJ);

         
         $agrupadas = [];
         foreach ($imagens as $imagem) {
             $nomeSecao = $imagem->nome_secao ?? 'Outras'; 
             if (!isset($agrupadas[$nomeSecao])) {
                 $agrupadas[$nomeSecao] = [];
             }
             $agrupadas[$nomeSecao][] = $imagem;
         }
         return $agrupadas;
     }

    /**
     * Busca uma imagem específica pelo ID.
     */
    public function buscarImagemPorId(int $id_imagem): object|false {
        $sql = "SELECT i.*, s.nome_pagina as nome_secao -- Ajuste s.nome_pagina se necessário
                FROM {$this->table} i
                LEFT JOIN {$this->secaoTable} s ON i.id_secao = s.id_pagina -- Ajuste s.id_pagina se necessário
                WHERE i.id_imagem = :id_imagem AND i.excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_imagem', $id_imagem, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Atualiza a URL da imagem e/ou a ordem de exibição.
     */
    public function atualizarImagem(int $id_imagem, ?string $url_imagem, int $ordem): bool {
        $setParts = ["ordem = :ordem", "atualizado_em = NOW()"];
        if ($url_imagem !== null) {
            $setParts[] = "url_imagem = :url_imagem";
        }
        $setClause = implode(', ', $setParts);

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id_imagem = :id_imagem";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);
        $stmt->bindParam(':id_imagem', $id_imagem, PDO::PARAM_INT);
        if ($url_imagem !== null) {
            $stmt->bindParam(':url_imagem', $url_imagem);
        }

        return $stmt->execute();
    }

    /**
     * Marca uma imagem como excluída (soft delete).
     */
    public function deletarImagem(int $id_imagem): bool {
        $sql = "UPDATE {$this->table} SET excluido_em = NOW() WHERE id_imagem = :id_imagem";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_imagem', $id_imagem, PDO::PARAM_INT);
        return $stmt->execute();
    }

     /**
      * Busca todas as seções/páginas disponíveis para seleção.
      * Ajuste os nomes das colunas 'id_pagina' e 'nome_pagina' se forem diferentes.
      */
     public function buscarSecoesDisponiveis(): array {
         $sql = "SELECT id_pagina, nome_pagina FROM {$this->secaoTable} ORDER BY nome_pagina ASC";
         $stmt = $this->db->query($sql);
         return $stmt->fetchAll(PDO::FETCH_OBJ);
     }
}