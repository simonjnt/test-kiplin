<?php

namespace App\Infrastructure\Repository;

use App\Application\ReadModel\OpenedTodo;
use App\Application\ReadModel\TodosRepository;
use App\Domain\Todo;
use App\Domain\TodoId;
use App\Domain\TodoRepository;

class DatabaseTodoRepository implements TodoRepository, TodosRepository
{
    private const  CONNECTION_STRING = 'host=host.docker.internal port=5434 dbname=test_backend user=root password=root';

    public function get(TodoId $id): ?Todo
    {
        $sql = <<<SQL
SELECT * FROM todo WHERE id=$1
SQL;

        $result = $this->executeQuery($sql, [$id->asString()]);
        $data = pg_fetch_array($result, null, PGSQL_ASSOC);

        if (!$data) {
            return null;
        }

        return Todo::fromData(json_decode($data['data'], true));
    }

    public function save(Todo $todo): void
    {
        $sql = <<<SQL
INSERT INTO todo VALUES ($1::uuid, $2::json) ON CONFLICT DO NOTHING;
SQL;

        $this->executeQuery($sql, [$todo->id()->asString(), json_encode($todo->toData())]);
    }

    public function opened(): array
    {
        $todos = [];

        // If the columns were stored in separate columns, I would add a WHERE status='opened' instead, to optimize resources used.
        $sql = <<<SQL
SELECT * FROM todo;
SQL;

        $result = $this->executeQuery($sql);
        foreach (pg_fetch_all($result) as $row) {
            $data = json_decode($row['data'], true);

            if ($data['status'] === 'opened') {
                $openedTodo = new OpenedTodo();
                $openedTodo->id = $data['id'];
                $openedTodo->description = $data['description'];

                $todos[] = $openedTodo;
            }
        }

        return $todos;
    }

    private function executeQuery(string $sql, array $params = [])
    {
        $connection = pg_connect(self::CONNECTION_STRING);
        return pg_query_params($connection, $sql, $params);
    }
}