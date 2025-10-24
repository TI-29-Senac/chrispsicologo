<?php
// Usa a classe Flash para pegar a mensagem e já limpá-la da sessão.
use App\Psico\Core\Flash;
$flashData = Flash::get();
?>

<style>
    .modal-backdrop {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        display: flex; justify-content: center; align-items: center;
        z-index: 1050; opacity: 0; visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .modal-backdrop.is-visible { opacity: 1; visibility: visible; }
    .modal-content {
        background-color: #fff; padding: 30px; border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3); text-align: center;
        max-width: 400px; width: 90%;
        font-family: "Questrial", sans-serif; color: #5D6D68;
        transform: scale(0.9); transition: transform 0.3s ease;
    }
    .modal-backdrop.is-visible .modal-content { transform: scale(1); }
    .modal-content h3 { margin-top: 0; font-size: 1.5rem; }
    .modal-content p { font-size: 1.1rem; margin-bottom: 25px; }
    .modal-button {
        background-color: #5D6D68; color: white; border: none;
        padding: 10px 25px; border-radius: 8px; cursor: pointer;
        font-size: 1rem; transition: background-color 0.3s ease;
    }
    .modal-button:hover { background-color: #4a5b5c; }
</style>

<div id="flash-modal" class="modal-backdrop">
    <div class="modal-content">
        <h3 id="modal-title"></h3>
        <p id="modal-message"></p>
        <button id="modal-close-btn" class="modal-button">OK</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const flashData = <?php echo json_encode($flashData); ?>;

    if (flashData && flashData.mensagem) {
        const modal = document.getElementById('flash-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const closeBtn = document.getElementById('modal-close-btn');

        modalTitle.textContent = (flashData.tipo === 'success') ? 'Sucesso!' : 'Atenção!';
        modalMessage.textContent = flashData.mensagem;
        
        modal.classList.add('is-visible');

        const closeModal = () => modal.classList.remove('is-visible');

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) closeModal();
        });
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>