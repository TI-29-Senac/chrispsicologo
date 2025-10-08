<div>Sou o Create</div>
<form action="/backend/agendamentos/salvar" method="POST">
    <label for="id_usuario">ID do Usuario:</label>
    <input type="text" id="id_usuario" name="id_usuario" required><br><br>
    <label for="id_profissional">ID do Profissional:</label>
    <input type="text" id="id_profissional" name="id_profissional" required><br><br>
    <label for="data_agendamento">Data do Agendamento:</label>
    <input type="datetime-local" id="data_agendamento" name="data_agendamento" required><br><br>
    <button type="submit" value="Salvar">Salvar</button>
</form>