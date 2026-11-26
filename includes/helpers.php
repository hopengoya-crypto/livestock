<?php
function escape_html($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function role_profile_id(mysqli $conn, string $role, int $userId): ?int
{
    if ($role === 'farmer') {
        $sql = 'SELECT farmer_id FROM farmer WHERE user_id = ? LIMIT 1';
    } elseif ($role === 'veterinarian') {
        $sql = 'SELECT vet_id FROM veterinarian WHERE user_id = ? LIMIT 1';
    } else {
        return null;
    }

    $statement = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($statement, 'i', $userId);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $profileId);
    $found = mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);

    return $found ? (int) $profileId : null;
}

function valid_iso_date(string $value): bool
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    return $date instanceof DateTimeImmutable && $date->format('Y-m-d') === $value;
}

function render_flash_message(): void
{
    $flash = consume_flash();
    if ($flash === null) {
        return;
    }

    echo '<p class="form-message ' . escape_html($flash['type']) . '">'
        . escape_html($flash['message']) . '</p>';
}
