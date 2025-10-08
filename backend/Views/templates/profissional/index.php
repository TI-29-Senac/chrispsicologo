<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-family: Arial, sans-serif;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        overflow: hidden;
    }
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tr:hover {
        background-color: #e0f7e9;
    }
    p.no-data {
        font-family: Arial, sans-serif;
        font-size: 16px;
        color: #555;
        margin-top: 20px;
    }
</style>

<?php if (!empty($profissionais)): ?>
<table>
    <thead>
        <tr>
            <th>ID Profissional</th>
            <th>ID Usuário</th>
            <th>Especialidade</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($profissionais as $profissional): ?>
        <tr>
            <td><?= htmlspecialchars($profissional['id_profissional']) ?></td>
            <td><?= htmlspecialchars($profissional['id_usuario']) ?></td>
            <td><?= htmlspecialchars($profissional['especialidade']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p class="no-data">Nenhum profissional encontrado.</p>
<?php endif; ?>
