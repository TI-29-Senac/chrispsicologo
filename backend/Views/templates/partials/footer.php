<?php
use App\Psico\Core\Flash;
$mensagem = Flash::get();
if(isset($mensagem)){
    foreach($mensagem as $key => $value){
        if($key == "type"){
            $tipo = $value == "success" ? "alert-success": "alert-danger";
            echo "<div class='alert $tipo' role'alert'>";
        }else{
            echo $value;
            echo "</div>";
        }
    };
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>