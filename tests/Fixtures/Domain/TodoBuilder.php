<?php

namespace App\Tests\Fixtures\Domain;

use App\Domain\Todo;
use App\Domain\TodoRepository;
use Symfony\Component\Uid\Uuid;

final class TodoBuilder
{
    private const STATUS_OPENED = 'opened';
    private const STATUS_CLOSED = 'closed';

    private ?string $id = null;
    private ?string $description = null;
    private ?string $status = null;

    public function withId(string $id): TodoBuilder
    {
        $this->id = $id;

        return $this;
    }

    public function withDescription(string $description): TodoBuilder
    {
        $this->description = $description;

        return $this;
    }

    public function thatIsOpened(): TodoBuilder
    {
        $this->status = self::STATUS_OPENED;

        return $this;
    }

    public function thatIsClosed(): TodoBuilder
    {
        $this->status = self::STATUS_CLOSED;

        return $this;
    }

    public function build(): Todo
    {
        return Todo::fromData([
            'id' => $this->id ?? Uuid::v4()->__toString(),
            'description' => $this->description ?? (string) rand(),
            'status' => $this->status ?? self::STATUS_OPENED,
        ]);
    }

    public function savedIn(TodoRepository $repository): Todo
    {
        $todo = $this->build();

        $repository->save($todo);

        return $todo;
    }
}