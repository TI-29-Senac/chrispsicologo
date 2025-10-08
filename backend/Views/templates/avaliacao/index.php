<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-family: Arial, sans-serif;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 12px 15px;
        border: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: #3f51b5;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f5f5f5;
    }
    tr:hover {
        background-color: #e0e0ff;
    }
    a {
        text-decoration: none;
        color: #3f51b5;
        font-weight: bold;
        margin: 0 5px;
    }
    a:hover {
        text-decoration: underline;
    }
</style>

<?php if (!empty($avaliacoes)): ?>
    <table>
        <thead>
            <tr>
                <th>ID Avaliação</th>
                <th>ID Cliente</th>
                <th>ID Profissional</th>
                <th>Nota</th>
                <th>Comentário</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($avaliacoes as $avaliacao): ?>
            <tr>
                <td><?= htmlspecialchars($avaliacao['id_avaliacao']) ?></td>
                <td><?= htmlspecialchars($avaliacao['id_cliente']) ?></td>
                <td><?= htmlspecialchars($avaliacao['id_profissional']) ?></td>
                <td><?= htmlspecialchars($avaliacao['nota_avaliacao']) ?>/5</td>
                <td><?= htmlspecialchars($avaliacao['descricao_avaliacao']) ?></td>
                <td>
                    <a href="/backend/avaliacoes/editar/<?= $avaliacao['id_avaliacao'] ?>">Editar</a> |
                    <a href="/backend/avaliacoes/excluir/<?= $avaliacao['id_avaliacao'] ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nenhuma avaliação encontrada.</p>
<?php endif; ?>
