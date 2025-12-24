<?php
namespace App\Psico\Controllers;

use Piggly\Pix\Exceptions\InvalidPixKeyException;
use Piggly\Pix\StaticPayload;

// Este controlador NÃO herda de AuthenticatedController,
// portanto não exige login para ser acessado.
class ApiController {

    public function gerarPix()
    {
        // Pega os dados enviados pelo aplicativo desktop (ex: valor)
        $input = json_decode(file_get_contents('php://input'), true);
        $valor = $input['valor'] ?? 1.00; // Pega o valor ou usa 1.00 como padrão

        try {
            // --- CONFIGURAÇÃO DO PIX ---
            $chavePix      = 'dollyblair18@gmail.com'; // <-- IMPORTANTE: TROQUE PELA SUA CHAVE PIX
            $nomeRecebedor = 'Chris Psicologia';
            $cidade        = 'SAO PAULO';

            $payload = (new StaticPayload())
                ->setAmount($valor)
                ->setPixKey('email', $chavePix)
                ->setDescription('Pagamento de consulta')
                ->setMerchantName($nomeRecebedor)
                ->setMerchantCity($cidade);

            $copiaECola = $payload->getPixCode();
            $qrCodeBase64 = $payload->getQRCode();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'copiaECola' => $copiaECola,
                'qrCodeBase64' => $qrCodeBase64
            ]);

        } catch (InvalidPixKeyException $e) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'message' => 'Chave PIX configurada é inválida.']);
        } catch (\Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['success' => false, 'message' => 'Erro ao gerar o PIX: ' . $e->getMessage()]);
        }
    }
}