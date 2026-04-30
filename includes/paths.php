<?php

function app_url(string $path = ''): string
{
    return '/explora_mais/' . ltrim($path, '/');
}

function imagem_url(?string $path): string
{
    $path = trim((string) $path);

    if ($path === '') {
        return '';
    }

    if (preg_match('#^(https?:)?//#', $path) || str_starts_with($path, '/')) {
        return $path;
    }

    if (str_starts_with($path, 'imagens/')) {
        return app_url('uploads/' . $path);
    }

    return app_url($path);
}
