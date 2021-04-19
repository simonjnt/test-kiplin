<?php
declare(strict_types=1);

namespace App\Domain;

/**
 * Provide write access to the Todo entity
 */
interface TodoRepository
{
    public function get(TodoId $id): ?Todo;

    public function save(Todo $todo): void;
}
