<?php
namespace App\Psico\Models;

use PDO;

class ImagemSite {
    private PDO $db;
    // Nomes das tabelas conforme o DER
    private string $imagemTable = 'imagem';
    public string $secaoTable = 'secao_site';
    public string $paginaTable = 'pagina_site';
    private string $conteudoTable = 'conteudo_site'; // <<< Adicionado para JOIN

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Insere uma nova imagem associada a uma SEÇÃO específica.
     */
    public function inserirImagem(int $id_secao, string $url_imagem, int $ordem = 99): int|false {
        $sql = "INSERT INTO {$this->imagemTable} (id_secao, url_imagem, ordem, criado_em)
                VALUES (:id_secao, :url_imagem, :ordem, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->bindParam(':url_imagem', $url_imagem);
        $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);
        return $stmt->execute() ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Busca APENAS as imagens de uma SEÇÃO específica (Usada pelo Quem Somos).
     * Revertida para o estado original.
     */
    public function buscarImagensPorSecao(int $id_secao): array {
        $sql = "SELECT id_imagem, url_imagem, ordem FROM {$this->imagemTable}
                WHERE id_secao = :id_secao AND excluido_em IS NULL
                ORDER BY ordem ASC, criado_em DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * FUNÇÃO CORRIGIDA: Busca detalhes completos (imagem, título e texto)
     * das imagens e conteúdos associados a uma SEÇÃO específica (Usada pelos Serviços).
     * Faz JOIN entre 'imagem' e 'conteudo_site'.
     */
    public function buscarDetalhesServicosPorSecao(int $id_secao): array {
        $sql = "SELECT
                    i.id_imagem,
                    i.url_imagem,
                    i.ordem,
                    c.titulo_secao AS nome_servico,
                    c.texto AS descricao_servico
                FROM
                    {$this->imagemTable} i
                INNER JOIN
                    {$this->conteudoTable} c ON i.id_secao = c.id_secao
                WHERE
                    i.id_secao = :id_secao
                    AND i.excluido_em IS NULL
                    AND c.excluido_em IS NULL
                ORDER BY
                    i.ordem ASC, i.criado_em DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


     /**
      * Busca todas as imagens agrupadas por NOME DE PÁGINA para o dashboard.
      */
     public function buscarTodasAgrupadasPorPagina(): array {
         $sql = "SELECT i.id_imagem, i.url_imagem, i.ordem, s.nome_secao, p.nome_pagina
                 FROM {$this->imagemTable} i
                 JOIN {$this->secaoTable} s ON i.id_secao = s.id_secao
                 JOIN {$this->paginaTable} p ON s.id_pagina = p.id_pagina
                 WHERE i.excluido_em IS NULL
                 ORDER BY p.nome_pagina ASC, s.ordem ASC, i.ordem ASC";
         $stmt = $this->db->query($sql);
         $imagens = $stmt->fetchAll(PDO::FETCH_OBJ);

         $agrupadas = [];
         foreach ($imagens as $imagem) {
             $nomePagina = $imagem->nome_pagina ?? 'Página Desconhecida';
             if (!isset($agrupadas[$nomePagina])) {
                 $agrupadas[$nomePagina] = [];
             }
             $agrupadas[$nomePagina][] = $imagem;
         }
         return $agrupadas;
     }

    /**
     * Busca uma imagem específica pelo ID, incluindo nomes da seção e página.
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
     * Atualiza a URL da imagem e/ou a ordem de exibição.
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
     */
    public function deletarImagem(int $id_imagem): bool {
        $sql = "UPDATE {$this->imagemTable} SET excluido_em = NOW() WHERE id_imagem = :id_imagem";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_imagem', $id_imagem, PDO::PARAM_INT);
        return $stmt->execute();
    }

     /**
      * Busca todas as PÁGINAS disponíveis para o dropdown.
      */
     public function buscarPaginasDisponiveis(): array {
         $sql = "SELECT id_pagina, nome_pagina FROM {$this->paginaTable} ORDER BY ordem ASC, nome_pagina ASC";
         $stmt = $this->db->query($sql);
         return $stmt->fetchAll(PDO::FETCH_OBJ);
     }

     /**
      * Busca todas as SEÇÕES de uma PÁGINA específica para o dropdown.
      */
     public function buscarSecoesPorPagina(int $id_pagina): array {
         $sql = "SELECT id_secao, nome_secao FROM {$this->secaoTable}
                 WHERE id_pagina = :id_pagina AND excluido_em IS NULL
                 ORDER BY ordem ASC, nome_secao ASC";
         $stmt = $this->db->prepare($sql);
         $stmt->bindParam(':id_pagina', $id_pagina, PDO::PARAM_INT);
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_OBJ);
     }

     public function inserirConteudo(int $id_secao, string $titulo, ?string $subtitulo, string $texto, int $ordem): int|false {
        $sql = "INSERT INTO {$this->conteudoTable} (id_secao, titulo_secao, subtitulo, texto, ordem_secao, criado_em)
                VALUES (:id_secao, :titulo, :subtitulo, :texto, :ordem, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':subtitulo', $subtitulo); // Pode ser null
        $stmt->bindParam(':texto', $texto);
        $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);
        return $stmt->execute() ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * ATUALIZA um registro existente na tabela conteudo_site pelo ID do conteúdo.
     */
    public function atualizarConteudoPorId(int $id_conteudo, string $titulo, ?string $subtitulo, string $texto, int $ordem): bool {
        $sql = "UPDATE {$this->conteudoTable}
                SET titulo_secao = :titulo, subtitulo = :subtitulo, texto = :texto, ordem_secao = :ordem, atualizado_em = NOW()
                WHERE id_conteudo = :id_conteudo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':subtitulo', $subtitulo);
        $stmt->bindParam(':texto', $texto);
        $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);
        $stmt->bindParam(':id_conteudo', $id_conteudo, PDO::PARAM_INT);
        return $stmt->execute();
    }

     /**
      * Busca um registro da tabela conteudo_site pelo ID da seção.
      * Útil para verificar se já existe conteúdo para editar.
      */
     public function buscarConteudoPorSecaoId(int $id_secao): object|false {
        // Assume que pode haver múltiplos conteúdos por seção, pega o primeiro pela ordem
        $sql = "SELECT * FROM {$this->conteudoTable}
                WHERE id_secao = :id_secao AND excluido_em IS NULL
                ORDER BY ordem_secao ASC, criado_em DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * ATUALIZA um registro na tabela conteudo_site pelo ID da SEÇÃO.
     * Útil se você tem apenas UM conteúdo por seção (como parece ser o caso pela edit.php).
     * Se não encontrar, NÃO faz nada (poderia ser adaptado para inserir se não encontrar).
     */
    public function atualizarConteudoPorSecaoId(int $id_secao, string $titulo, ?string $subtitulo, string $texto, int $ordem): bool {
        $sql = "UPDATE {$this->conteudoTable}
                SET titulo_secao = :titulo, subtitulo = :subtitulo, texto = :texto, ordem_secao = :ordem, atualizado_em = NOW()
                WHERE id_secao = :id_secao AND excluido_em IS NULL"; // Atualiza baseado na seção
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':subtitulo', $subtitulo);
        $stmt->bindParam(':texto', $texto);
        $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);
        $stmt->bindParam(':id_secao', $id_secao, PDO::PARAM_INT);
        $stmt->execute();
        // Verifica se alguma linha foi afetada (retorna true se atualizou, false se não encontrou)
        return $stmt->rowCount() > 0;
    }
}