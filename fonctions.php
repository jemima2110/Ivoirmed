<?php
function e($valeur): string {
    return htmlspecialchars((string)($valeur ?? ""), ENT_QUOTES, "UTF-8");
}
function rediriger(string $page): void {
    header("Location: " . $page);
    exit;
}
