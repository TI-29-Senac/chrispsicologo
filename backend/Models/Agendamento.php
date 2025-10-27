<?php
namespace App\Psico\Models;
use PDO;
use DateTime;
use PDOException;

class Agendamento {
    private PDO $db;
    private $table = 'agendamento';
    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function inserirAgendamento(int $id_usuario, int $id_profissional, string $data_agendamento, string $status_consulta = 'pendente') {
        $sql = "INSERT INTO {$this->table} (id_usuario, id_profissional, data_agendamento, status_consulta)
                VALUES (:id_usuario, :id_profissional, :data_agendamento, :status_consulta)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':data_agendamento', $data_agendamento);
        $stmt->bindParam(':status_consulta', $status_consulta);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function buscarAgendamentos(): array {
        $sql = "
            SELECT 
                a.*,
                paciente.nome_usuario as nome_paciente,
                profissional.nome_usuario as nome_profissional
            FROM {$this->table} a
            JOIN usuario paciente ON a.id_usuario = paciente.id_usuario
            JOIN profissional p ON a.id_profissional = p.id_profissional
            JOIN usuario profissional ON p.id_usuario = profissional.id_usuario
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarHorariosOcupados(int $id_profissional, string $data): array {
        // Formata a data para buscar no banco (considera o dia inteiro)
        $dataInicio = $data . ' 00:00:00';
        $dataFim = $data . ' 23:59:59';

        $sql = "SELECT DATE_FORMAT(data_agendamento, '%H:%i') as horario
                FROM {$this->table}
                WHERE id_profissional = :id_profissional
                AND data_agendamento BETWEEN :dataInicio AND :dataFim
                AND status_consulta != 'cancelada' -- Ignora cancelados
                AND excluido_em IS NULL"; // Garante que não está soft deleted

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':dataInicio', $dataInicio);
        $stmt->bindParam(':dataFim', $dataFim);
        $stmt->execute();

        // Retorna apenas a coluna 'horario' como um array simples
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Calcula os horários disponíveis com base nos horários padrão e nos ocupados.
     * (SIMPLIFICADO: Horários padrão fixos aqui, idealmente viriam do BD)
     */
    public function calcularHorariosDisponiveis(int $id_profissional, string $data): array {
        // Horários padrão de atendimento (exemplo)
        // Idealmente, isso seria configurável por profissional no banco de dados
        $horariosPadrao = [
            '08:00', '09:00', '10:00', '11:00', '12:00',
            '14:00', '15:00', '16:00', '17:00', '18:00'
        ]; // Adicione mais ou modifique conforme necessário

        // Horários aos Sábados (exemplo)
        $horariosSabado = ['08:00', '09:00', '10:00', '11:00', '12:00'];

        try {
            $diaDaSemana = (new DateTime($data))->format('N'); // 1 (Segunda) a 7 (Domingo)
        } catch (\Exception $e) {
            return []; // Retorna vazio se a data for inválida
        }

        $horariosParaHoje = [];
        if ($diaDaSemana >= 1 && $diaDaSemana <= 5) { // Segunda a Sexta
            $horariosParaHoje = $horariosPadrao;
        } elseif ($diaDaSemana == 6) { // Sábado
            $horariosParaHoje = $horariosSabado;
        } else { // Domingo (ou outro dia sem atendimento)
            return []; // Sem horários disponíveis
        }


        $horariosOcupados = $this->buscarHorariosOcupados($id_profissional, $data);

        // Filtra os horários padrão, removendo os que estão ocupados
        $horariosDisponiveis = array_diff($horariosParaHoje, $horariosOcupados);

        // Reindexa o array para garantir chaves sequenciais (importante para JSON)
        return array_values($horariosDisponiveis);
    }

    // ... (restante dos métodos: atualizar, paginacao, buscarPorId, deletar, buscarTodos) ...
    public function atualizarAgendamento(
        int $id_agendamento,
        string $data_agendamento,
        string $status_consulta,
            ?int $id_profissional = null, // Torna opcional
            ?int $id_cliente = null      // Torna opcional
    ): bool {
        // Constrói a query dinamicamente apenas com os campos que podem mudar
        $fieldsToUpdate = [
            'data_agendamento = :data_agendamento',
            'status_consulta = :status_consulta',
            'atualizado_em = NOW()'
        ];
        $params = [
            ':data_agendamento' => $data_agendamento,
            ':status_consulta' => $status_consulta,
            ':id_agendamento' => $id_agendamento
        ];

        // Adiciona profissional e cliente apenas se forem fornecidos (permite alterá-los se necessário no futuro)
        if ($id_profissional !== null) {
            $fieldsToUpdate[] = 'id_profissional = :id_profissional';
            $params[':id_profissional'] = $id_profissional;
        }
        if ($id_cliente !== null) {
            $fieldsToUpdate[] = 'id_cliente = :id_cliente';
            $params[':id_cliente'] = $id_cliente;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fieldsToUpdate) . " WHERE id_agendamento = :id_agendamento";

        try {
            $stmt = $this->db->prepare($sql);

            // Vincula os parâmetros existentes no array $params
            foreach ($params as $key => &$value) {
                // Determina o tipo do parâmetro (assume INT para IDs)
                 $paramType = (str_contains($key, '_id')) ? PDO::PARAM_INT : PDO::PARAM_STR;
                 if ($key === ':id_agendamento') $paramType = PDO::PARAM_INT;

                 $stmt->bindParam($key, $value, $paramType);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            // Log do erro para depuração
            error_log("Erro ao atualizar agendamento {$id_agendamento}: " . $e->getMessage());
            return false;
        }
    }

    public function paginacao(int $pagina = 1, int $por_pagina = 5): array {
        $offset = ($pagina - 1) * $por_pagina;

        $totalQuery = "SELECT COUNT(*) FROM {$this->table} a WHERE a.excluido_em IS NULL"; // Considera soft delete
        $totalStmt = $this->db->query($totalQuery);
        $total_de_registros = $totalStmt->fetchColumn();

        $dataQuery = "
            SELECT a.*, paciente.nome_usuario as nome_paciente, prof_usuario.nome_usuario as nome_profissional
            FROM {$this->table} a
            JOIN usuario paciente ON a.id_usuario = paciente.id_usuario
            JOIN profissional p ON a.id_profissional = p.id_profissional
            JOIN usuario prof_usuario ON p.id_usuario = prof_usuario.id_usuario
            WHERE a.excluido_em IS NULL -- Considera soft delete
            ORDER BY a.data_agendamento ASC -- Ordena pela data do agendamento
            LIMIT :limit OFFSET :offset";

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

    public function buscarAgendamentoPorId(int $id_agendamento): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id_agendamento = :id_agendamento AND excluido_em IS NULL"; // Considera soft delete
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->execute();
        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
        return $agendamento ?: null;
    }

    public function deletarAgendamento(int $id_agendamento): bool {
        // Soft delete: marca como excluído e cancelado
        $sql = "UPDATE {$this->table} SET excluido_em = NOW(), status_consulta = 'cancelada', atualizado_em = NOW()
                WHERE id_agendamento = :id_agendamento AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function buscarTodosAgendamentos(): array {
         // Busca todos não excluídos para a lista de seleção no painel
        $sql = "SELECT
                    a.id_agendamento,
                    a.data_agendamento,
                    u.nome_usuario AS nome_paciente,
                    p.id_profissional
                FROM {$this->table} a
                JOIN usuario u ON a.id_usuario = u.id_usuario
                JOIN profissional p ON a.id_profissional = p.id_profissional
                WHERE a.excluido_em IS NULL AND a.status_consulta = 'pendente' -- Exemplo: buscar só pendentes para criar pagamento
                ORDER BY a.data_agendamento DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ... (Dentro da classe Agendamento em Agendamento.php) ...

    public function buscarAgendamentosPorUsuario(int $id_usuario): array {
        $sql = "
            SELECT
    a.id_agendamento,
    a.data_agendamento,
    a.status_consulta,
    p.id_profissional, -- Adicione esta linha
    prof_usuario.nome_usuario AS nome_profissional
FROM agendamento a -- Use alias para a tabela
JOIN profissional p ON a.id_profissional = p.id_profissional
JOIN usuario prof_usuario ON p.id_usuario = prof_usuario.id_usuario
WHERE a.id_usuario = :id_usuario
AND a.excluido_em IS NULL
ORDER BY a.data_agendamento DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAgendamentosPorMes() {
        
        $sql = "
            SELECT 
              DATE_FORMAT(data_agendamento, '%Y-%m-01') AS mes_ano, -- <<< CORRIGIDO AQUI
              COUNT(id_agendamento) AS total
            FROM agendamento
            WHERE data_agendamento >= (NOW() - INTERVAL 6 MONTH) -- <<< DESCOMENTAR E CORRIGIR AQUI
            GROUP BY mes_ano
            ORDER BY mes_ano;
        ";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_OBJ);
            return $this->preencherMesesAusentes($data); 
        } catch (\PDOException $e) {
            error_log("Erro ao buscar agendamentos por mês: " . $e->getMessage());
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

        // Indexa os dados recebidos por 'mes_ano' (ex: '2023-10-01')
        foreach ($data as $item) {
            $dadosIndexados[$item->mes_ano] = $item->total;
        }

        // Gera os últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $dataRef = new \DateTime(date('Y-m-01') . " -$i months");
            $mesKey = $dataRef->format('Y-m-01');
            
            $total = $dadosIndexados[$mesKey] ?? 0; // Pega o total se existir, senão 0

            $mesesFormatados[] = (object)[
                'mes_ano' => $mesKey,
                'total' => $total
            ];
        }
        
        return $mesesFormatados;
    }

}