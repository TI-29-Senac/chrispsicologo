<?php
// router.php

// 1. Obter a URI da requisição
$requestUri = $_SERVER['REQUEST_URI'];

// 2. Verificar se o arquivo solicitado existe fisicamente
$filePath = __DIR__ . $requestUri;

// Se for um diretório, tenta procurar por index.html ou index.php
if (is_dir($filePath)) {
    if (file_exists($filePath . '/index.html')) {
        return false; // Serve o index.html diretamente
    }
    if (file_exists($filePath . '/index.php')) {
        // Se for backend/, deixa o backend/index.php rodar
        if (strpos($requestUri, '/backend') === 0) {
             $_SERVER['SCRIPT_NAME'] = '/backend/index.php';
             require __DIR__ . '/backend/index.php';
             return;
        }
        return false; // Serve o index.php padrão (se houver)
    }
}

// Se o arquivo existe e é um arquivo (não diretório), serve-o diretamente
if (file_exists($filePath) && !is_dir($filePath)) {
    return false; // Retorna false para que o servidor embutido sirva o arquivo
}

// 3. Lógica de reescrita (Rewrite Rules do .htaccess)

// Regra: Tudo que começar com /backend e NÃO for arquivo real, vai para backend/index.php
if (strpos($requestUri, '/backend') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/backend/index.php';
    require __DIR__ . '/backend/index.php';
    return;
}

// Se não for backend, e não existir o arquivo, geralmente retorna 404 ou index.html (SPA)
// No seu caso, parece que a raiz serve arquivos estáticos .html.
// Se a rota não for encontrada, o servidor padrão retornará 404.
// Mas se você tiver uma lógica de SPA no frontend, pode precisar redirecionar para index.html aqui.

// Por enquanto, vamos assumir comportamento padrão para arquivos não encontrados na raiz (404)
return false;
