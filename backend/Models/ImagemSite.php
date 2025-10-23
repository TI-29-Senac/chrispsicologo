<?php
namespace App\Psico\Models;
 
use PDO;
 
class ImagemSite {
    private PDO $db;
    // Nomes das tabelas conforme o DER
    private string $imagemTable = 'imagem';
    public string $secaoTable = 'secao_site';
    public string $paginaTable = 'pagina_site';
 
    public function __construct(PDO $db) {
        $this->db = $db;
    }
 
    /**
     * Insere uma nova imagem associada a uma SEÇÃO específica.
     * DER: imagem (id_secao, url_imagem, ordem)
     */
    public function inserirImagem(int $id_secao, string $url_imagem, int $ordem = 99): int|false {
        $sql = "INSERT INTO {$this->imagemTable} (id_secao, url_imagem, ordem, criado_em)
                VALUES (:id_secao, :url_imagem, :ordem, NOW())"; // Adicionado criado_em
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->bindParam(':url_imagem', $url_imagem);
        $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);
        return $stmt->execute() ? (int)$this->db->lastInsertId() : false;
    }
 
    /**
     * Busca todas as imagens de uma SEÇÃO específica, ordenadas.
     * DER: imagem WHERE id_secao = ?
     */
    public function buscarImagensPorSecao(int $id_secao): array {
        $sql = "SELECT id_imagem, url_imagem, ordem FROM {$this->imagemTable}
                WHERE id_secao = :id_secao AND excluido_em IS NULL
                ORDER BY ordem ASC, criado_em DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->execute();
        // Retorna como objetos para consistência, mas pode ser FETCH_ASSOC se preferir
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
 
     /**
      * Busca todas as imagens agrupadas por NOME DE PÁGINA para o dashboard.
      * DER: imagem JOIN secao_site JOIN pagina_site
      */
     public function buscarTodasAgrupadasPorPagina(): array {
         $sql = "SELECT i.id_imagem, i.url_imagem, i.ordem, s.nome_secao, p.nome_pagina
                 FROM {$this->imagemTable} i
                 JOIN {$this->secaoTable} s ON i.id_secao = s.id_secao
                 JOIN {$this->paginaTable} p ON s.id_pagina = p.id_pagina
                 WHERE i.excluido_em IS NULL
                 ORDER BY p.nome_pagina ASC, s.ordem ASC, i.ordem ASC"; // Adicionado s.ordem
         $stmt = $this->db->query($sql);
         $imagens = $stmt->fetchAll(PDO::FETCH_OBJ);
 
         // Agrupa as imagens pelo NOME DA PÁGINA
         $agrupadas = [];
         foreach ($imagens as $imagem) {
             $nomePagina = $imagem->nome_pagina ?? 'Página Desconhecida';
             if (!isset($agrupadas[$nomePagina])) {
                 $agrupadas[$nomePagina] = [];
             }
             $agrupadas[$nomePagina][] = $imagem; // Armazena o objeto imagem completo
         }
         return $agrupadas;
     }
 
    /**
     * Busca uma imagem específica pelo ID, incluindo nomes da seção e página.
     * DER: imagem JOIN secao_site JOIN pagina_site WHERE id_imagem = ?
     */
    public function buscarImagemPorId(int $id_imagem): object|false {
        $sql = "SELECT i.id_imagem, i.url_imagem, i.ordem, i.id_secao,
                       s.nome_secao, s.id_pagina,
                       p.nome_pagina
                FROM {$this->imagemTable} i
                JOIN {$this->secaoTable} s ON i.id_secao = s.id_secao
                JOIN {$this->paginaTable} p ON s.id_pagina = p.id_pagina
                WHERE i.id_imagem = :id_imagem AND i.excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_imagem', $id_imagem, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
 
    /**
     * Atualiza a URL da imagem e/ou a ordem de exibição. A seção não muda.
     * DER: UPDATE imagem SET ... WHERE id_imagem = ?
     */
    public function atualizarImagem(int $id_imagem, ?string $url_imagem, int $ordem): bool {
        $setParts = ["ordem = :ordem", "atualizado_em = NOW()"];
        if ($url_imagem !== null) {
            $setParts[] = "url_imagem = :url_imagem";
        }
        $setClause = implode(', ', $setParts);
        $sql = "UPDATE {$this->imagemTable} SET {$setClause} WHERE id_imagem = :id_imagem";
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
     * DER: UPDATE imagem SET excluido_em = NOW() WHERE id_imagem = ?
     */
    public function deletarImagem(int $id_imagem): bool {
        $sql = "UPDATE {$this->imagemTable} SET excluido_em = NOW() WHERE id_imagem = :id_imagem";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_imagem', $id_imagem, PDO::PARAM_INT);
        return $stmt->execute();
    }
 
     /**
      * Busca todas as PÁGINAS disponíveis para o primeiro dropdown.
      * DER: SELECT id_pagina, nome_pagina FROM pagina_site
      */
     public function buscarPaginasDisponiveis(): array {
         $sql = "SELECT id_pagina, nome_pagina FROM {$this->paginaTable} ORDER BY ordem ASC, nome_pagina ASC"; // Usa ordem da pagina
         $stmt = $this->db->query($sql);
         return $stmt->fetchAll(PDO::FETCH_OBJ);
     }
 
     /**
      * Busca todas as SEÇÕES de uma PÁGINA específica para o segundo dropdown.
      * DER: SELECT id_secao, nome_secao FROM secao_site WHERE id_pagina = ?
      */
     public function buscarSecoesPorPagina(int $id_pagina): array {
         $sql = "SELECT id_secao, nome_secao FROM {$this->secaoTable}
                 WHERE id_pagina = :id_pagina AND excluido_em IS NULL
                 ORDER BY ordem ASC, nome_secao ASC"; // Usa ordem da secao
         $stmt = $this->db->prepare($sql);
         $stmt->bindParam(':id_pagina', $id_pagina, PDO::PARAM_INT);
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_OBJ);
     }
}