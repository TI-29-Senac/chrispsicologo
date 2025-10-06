<form action="/backend/agendamento/atualizar" method="POST">
    <input type="hidden" name="id" value="<?= $agendamento['id'] ?>">

    <label for="id_paciente">Paciente:</label>
    <input type="text" id="id_paciente" name="id_paciente" value="<?= $agendamento['id_paciente'] ?>" required>

    <label for="id_profissional">Profissional:</label>
    <input type="text" id="id_profissional" name="id_profissional" value="<?= $agendamento['id_profissional'] ?>" required>

    <label for="data_agendamento">Data do Agendamento:</label>
    <input type="datetime-local" id="data_agendamento" name="data_agendamento" value="<?= $agendamento['data_agendamento'] ?>" required>

    <button type="submit">Atualizar</button>
</form>