<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-family: Arial, sans-serif;
    }
    th, td {
        padding: 12px 15px;
        border: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: #4CAF50;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
</style>

<table>
    <thead>
        <tr>
            <th>ID Usuário</th>
            <th>ID Profissional</th>
            <th>Data do Agendamento</th>
            <th>Status da Consulta</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($agendamentos as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['id_usuario']) ?></td>
            <td><?= htmlspecialchars($item['id_profissional']) ?></td>
            <td><?= htmlspecialchars($item['data_agendamento']) ?></td>
            <td><?= htmlspecialchars($item['status_consulta']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
