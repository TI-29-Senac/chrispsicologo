<?php
use App\Psico\Core\Flash;

if ($message = Flash::get('success')) {
    echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

// Verifica se existe uma mensagem de erro e a exibe
if ($message = Flash::get('error')) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($message) . '</div>';
}
Flash::get('validation_errors');
Flash::get('old_input');
?>

<script src="/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>