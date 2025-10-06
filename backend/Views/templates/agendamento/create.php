<div>Sou o Create</div>
<form action="/backend/agendamento/salvar" method="POST">
    <label for="id_paciente">ID do Paciente:</label>
    <input type="text" id="id_paciente" name="id_paciente" required><br><br>
    <label for="id_profissional">ID do Profissional:</label>
    <input type="text" id="id_profissional" name="id_profissional" required><br><br>
    <label for="data_agendamento">Data do Agendamento:</label>
    <button type="submit" value="Salvar">Salvar</button>
</form>