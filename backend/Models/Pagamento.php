<?php
namespace App\Psico\Models;
use PDO;

class Pagamento {
    private PDO $db;
    private $table = 'pagamento';

    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function inserirPagamento(int $id_agendamento, string $tipo_pagamento) {
        $mapaFormaPagamento = [
            'pix' => 1,
            'credito' => 2,
            'debito' => 3,
            'dinheiro' => 4
        ];
        $id_forma_pagamento = $mapaFormaPagamento[$tipo_pagamento] ?? 1;

        $sql = "INSERT INTO {$this->table} (id_agendamento, id_forma_pagamento)
                VALUES (:id_agendamento, :id_forma_pagamento)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->bindParam(':id_forma_pagamento', $id_forma_pagamento, PDO::PARAM_INT);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function paginacao(int $pagina = 1, int $por_pagina = 5): array {
        $offset = ($pagina - 1) * $por_pagina;

        $totalQuery = "SELECT COUNT(*) FROM {$this->table}";
        $totalStmt = $this->db->query($totalQuery);
        $total_de_registros = $totalStmt->fetchColumn();

        // Query Final e Corrigida
        $dataQuery = "
            SELECT
                p.id_pagamento,
                fp.nome_forma_pagamento as tipo_pagamento, 
                p.criado_em as data_pagamento,
                cliente.nome_usuario AS nome_cliente,
                profissional_usuario.nome_usuario AS nome_profissional,
                prof.valor_consulta
            FROM {$this->table} p
            JOIN agendamento a ON p.id_agendamento = a.id_agendamento
            JOIN usuario cliente ON a.id_usuario = cliente.id_usuario
            JOIN profissional prof ON a.id_profissional = prof.id_profissional
            JOIN usuario profissional_usuario ON prof.id_usuario = profissional_usuario.id_usuario
            JOIN formas_pagamento fp ON p.id_forma_pagamento = fp.id_forma_pagamento 
            ORDER BY p.id_pagamento ASC
            LIMIT :limit OFFSET :offset
        ";
        
        $dataStmt = $this->db->prepare($dataQuery);
        $dataStmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $dados = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $dados,
            'total' => (int) $total_de_registros,
            'por_pagina' => (int) $por_pagina,
            'pagina_atual' => (int) $pagina,
            'ultima_pagina' => (int) ceil($total_de_registros / $por_pagina)
        ];
    }

    public function buscarTodosPagamentos(): array {
        $sql = "
            SELECT 
                p.id_pagamento,
                p.criado_em as data_pagamento, /* <--- O SEGREDO ESTÁ AQUI */
                prof.valor_consulta, 
                fp.nome_forma_pagamento as tipo_pagamento 
            FROM {$this->table} p
            JOIN agendamento a ON p.id_agendamento = a.id_agendamento
            JOIN profissional prof ON a.id_profissional = prof.id_profissional
            JOIN formas_pagamento fp ON p.id_forma_pagamento = fp.id_forma_pagamento 
            ORDER BY p.criado_em DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarPagamentoPorId(int $id_pagamento): ?array {
        $sql = "
            SELECT
                p.id_pagamento,
                fp.nome_forma_pagamento as tipo_pagamento, 
                p.criado_em as data_pagamento,
                a.id_agendamento,
                prof.valor_consulta
            FROM {$this->table} p
            JOIN agendamento a ON p.id_agendamento = a.id_agendamento
            JOIN profissional prof ON a.id_profissional = prof.id_profissional
            JOIN formas_pagamento fp ON p.id_forma_pagamento = fp.id_forma_pagamento 
            WHERE p.id_pagamento = :id_pagamento
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmt->execute();
        $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);
        return $pagamento ?: null;
    }

    public function atualizarPagamento(int $id_pagamento, string $tipo_pagamento, float $valor_consulta): bool {
        $mapaFormaPagamento = ['pix' => 1, 'credito' => 2, 'debito' => 3, 'dinheiro' => 4];
        $id_forma_pagamento = $mapaFormaPagamento[$tipo_pagamento] ?? 1;

        $sqlPagamento = "UPDATE {$this->table}
                         SET id_forma_pagamento = :id_forma_pagamento,
                             atualizado_em = NOW()
                         WHERE id_pagamento = :id_pagamento";
        $stmtPagamento = $this->db->prepare($sqlPagamento);
        $stmtPagamento->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmtPagamento->bindParam(':id_forma_pagamento', $id_forma_pagamento, PDO::PARAM_INT);
        $sucessoPagamento = $stmtPagamento->execute();

        $sqlProfissional = "UPDATE profissional prof
                           JOIN agendamento a ON prof.id_profissional = a.id_profissional
                           JOIN pagamento p ON a.id_agendamento = p.id_agendamento
                           SET prof.valor_consulta = :valor_consulta
                           WHERE p.id_pagamento = :id_pagamento";
        $stmtProfissional = $this->db->prepare($sqlProfissional);
        $stmtProfissional->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmtProfissional->bindParam(':valor_consulta', $valor_consulta);
        $sucessoProfissional = $stmtProfissional->execute();

        return $sucessoPagamento && $sucessoProfissional;
    }

    public function deletarPagamento(int $id_pagamento): bool {
        $sql = "DELETE FROM {$this->table} WHERE id_pagamento = :id_pagamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getFaturamentoPorMes() {

        $sql = "
            SELECT
              DATE_FORMAT(p.criado_em, '%Y-%m-01') AS mes_ano,

              /* --- CORREÇÃO APLICADA AQUI --- */
              /* Somamos o 'valor_consulta' da tabela 'profissional' */
              SUM(COALESCE(prof.valor_consulta, 0)) AS total
              /* --- FIM DA CORREÇÃO --- */

            FROM pagamento p

            /* JOIN para encontrar o agendamento */
            JOIN agendamento a ON p.id_agendamento = a.id_agendamento

            /* JOIN para encontrar o profissional e seu valor */
            JOIN profissional prof ON a.id_profissional = prof.id_profissional

            WHERE p.criado_em >= (NOW() - INTERVAL 6 MONTH)
            GROUP BY mes_ano
            ORDER BY mes_ano;
        ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_OBJ);
            // O helper para preencher meses ausentes está correto
            return $this->preencherMesesAusentes($data);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar faturamento por mês: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Helper para preencher os últimos 6 meses com valor 0 se não houver dados.
     * @param array $data Os dados do banco (FETCH_OBJ)
     * @return array
     */
    private function preencherMesesAusentes(array $data) {
        $mesesFormatados = [];
        $dadosIndexados = [];

        foreach ($data as $item) {
            $dadosIndexados[$item->mes_ano] = $item->total;
        }

        for ($i = 5; $i >= 0; $i--) {
            $dataRef = new \DateTime(date('Y-m-01') . " -$i months");
            $mesKey = $dataRef->format('Y-m-01');
            $total = $dadosIndexados[$mesKey] ?? 0;

            $mesesFormatados[] = (object)[
                'mes_ano' => $mesKey,
                'total' => $total
            ];
        }
        
        return $mesesFormatados;
    }
}