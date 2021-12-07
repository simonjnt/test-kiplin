<?php
declare(strict_types=1);

namespace App\Tests\Integration\WriteRepository;

use App\Domain\CannotCloseTodo;
use App\Domain\Todo;
use App\Domain\TodoDescription;
use App\Domain\TodoId;
use App\Domain\TodoRepository;
use App\Infrastructure\Repository\InMemoryTodoRepository;
use PHPUnit\Framework\TestCase;
use function App\Tests\Fixtures\aTodo;

final class TodoRepositoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideConcretions
     */
    public function it_saves_non_existing_todo(TodoRepository $repository): void
    {
        $aNonExistingTodo = aTodo()->build();

        $repository->save($aNonExistingTodo);

        $this->assertEquals($aNonExistingTodo, $repository->get($aNonExistingTodo->id()));
    }

    /**
     * @test
     * @dataProvider provideConcretions
     */
    public function it_saves_existing_todo(TodoRepository $repository): void
    {
        $anExistingTodo = aTodo()->savedIn($repository);

        $repository->save($anExistingTodo);

        $this->assertEquals($anExistingTodo, $repository->get($anExistingTodo->id()));
    }

    public function provideConcretions(): \Generator
    {
        yield InMemoryTodoRepository::class => [new InMemoryTodoRepository()];
    }
}
