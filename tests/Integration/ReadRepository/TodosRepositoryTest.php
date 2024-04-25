<?php
declare(strict_types=1);

namespace App\Tests\Integration\ReadRepository;

use App\Application\ReadModel\OpenedTodo;
use App\Domain\Todo;
use App\Domain\TodoId;
use App\Domain\TodoRepository;
use App\Infrastructure\Repository\InMemoryTodoRepository;
use PHPUnit\Framework\TestCase;
use function App\Tests\Fixtures\aTodo;

final class TodosRepositoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideConcretions
     */
    public function it_finds_existing_todo(TodoRepository $repository): void
    {
        $aTodo = aTodo()->savedIn($repository);

        $persistedTodo = $repository->get($aTodo->id());

        $this->assertEquals($aTodo, $persistedTodo);
    }

    /**
     * @test
     * @dataProvider provideConcretions
     */
    public function it_does_not_find_non_existing_todo(TodoRepository $repository): void
    {
        aTodo()->savedIn($repository);
        $nonExistingTodoId = TodoId::generate();

        $existingTodos = $repository->get($nonExistingTodoId);

        $this->assertNull($existingTodos);
    }

    /**
     * @test
     * @dataProvider provideConcretions
     */
    public function it_finds_opened_todos(TodoRepository $repository): void
    {
        $anOpenedTodo = aTodo()->thatIsOpened()->savedIn($repository);

        $openedTodos = $repository->opened();

        $this->assertThatArrayContainsTodo($openedTodos, $anOpenedTodo);
    }

    /**
     * @test
     * @dataProvider provideConcretions
     */
    public function it_does_not_finds_closed_todos(TodoRepository $repository): void
    {
        $aClosedTodo = aTodo()->thatIsClosed()->savedIn($repository);

        $openedTodos = $repository->opened();

        $this->assertThatArrayDoesNotContainsTodo($openedTodos, $aClosedTodo);
    }

    public function provideConcretions(): \Generator
    {
        yield InMemoryTodoRepository::class => [new InMemoryTodoRepository()];
        // TODO: add other persistence type repositories here!
    }

    private function assertThatArrayContainsTodo(array $actualTodos, Todo $expectedTodo): void
    {
        /** @var OpenedTodo $actualTodo */
        foreach ($actualTodos as $actualTodo) {
            $this->assertInstanceOf(OpenedTodo::class, $actualTodo);

            ['id' => $id, 'description' => $description] = $expectedTodo->toData();

            $consistency = $actualTodo->id === $id;
            $consistency &= $actualTodo->description === $description;

            if ($consistency) {
                $this->assertTrue(true);

                return;
            }
        }

        $this->fail('Fail asserting that given todo is in array.');
    }

    private function assertThatArrayDoesNotContainsTodo(array $actualTodos, Todo $expectedTodo): void
    {
        /** @var OpenedTodo $actualTodo */
        foreach ($actualTodos as $actualTodo) {
            $this->assertInstanceOf(OpenedTodo::class, $actualTodo);

            ['id' => $id, 'description' => $description] = $expectedTodo->toData();

            $consistency = $actualTodo->id === $id;
            $consistency &= $actualTodo->description === $description;

            if ($consistency) {
                $this->fail('Fail asserting that given todo is not in array.');

                return;
            }
        }

        $this->assertTrue(true);
    }

}
