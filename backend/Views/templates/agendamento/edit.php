<form action="/backend/agendamentos/atualizar/<?= $agendamento['id'] ?>" method="POST">
    
    <input type="hidden" name="id" value="<?= $agendamento['id'] ?>">

    <label for="id_usuario">Usuário:</label>
    <input type="text" id="id_usuario" name="id_usuario" value="<?= $agendamento['id_usuario'] ?>" required>

    <label for="id_profissional">Profissional:</label>
    <input type="text" id="id_profissional" name="id_profissional" value="<?= $agendamento['id_profissional'] ?>" required>

    <label for="data_agendamento">Data do Agendamento:</label>
    <input type="datetime-local" id="data_agendamento" name="data_agendamento" value="<?= date('Y-m-d\TH:i', strtotime($agendamento['data_agendamento'])) ?>" required>

    <button type="submit">Atualizar</button>
</form>