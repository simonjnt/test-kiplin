<?php
declare(strict_types=1);

namespace App\Application\ReadModel;

/**
 * Provide read access to a list of currently opened todo
 */
interface TodosRepository
{
    /**
     * @return OpenedTodo[]
     */
    public function opened(): array;
}
