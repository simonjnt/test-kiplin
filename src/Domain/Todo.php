<?php

declare(strict_types=1);

namespace App\Domain;

final class Todo
{
    private TodoId $id;

    private TodoDescription $description;

    private TodoStatus $status;

    /** @var DomainEvent[] */
    private array $events = [];

    private function __construct()
    {
    }

    public static function open(TodoId $id, TodoDescription $description): self
    {
        $self = new self();
        $self->record(new TodoWasOpened($id, $description));

        return $self;
    }

    public function id(): TodoId
    {
        return $this->id;
    }

    public function close(): void
    {
        if ($this->status->equals(TodoStatus::closed())) {
            throw CannotCloseTodo::becauseTodoIsAlreadyClosed($this->id());
        }

        $this->record(new TodoWasClosed($this->id()));
    }

    /**
     * @param array{'id': string, 'description': string, 'status': string} $data
     */
    public static function fromData(array $data): self
    {
        $self = new self();
        $self->id = TodoId::fromString($data['id']);
        $self->description = TodoDescription::fromString($data['description']);
        $self->status = TodoStatus::fromString($data['status']);

        return $self;
    }

    /**
     * @return array{'id': string, 'description': string, 'status': string}
     */
    public function toData(): array
    {
        return [
            'id' => $this->id->asString(),
            'description' => $this->description->asString(),
            'status' => $this->status->asString(),
        ];
    }

    /**
     * @return DomainEvent[]
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    private function record(DomainEvent $event): void
    {
        $this->apply($event);

        $this->events[] = $event;
    }

    private function apply(object $event): void
    {
        switch (get_class($event)) {
            case TodoWasOpened::class:
                $this->id = $event->id;
                $this->description = $event->description;
                $this->status = TodoStatus::opened();
                break;

            case TodoWasClosed::class:
                $this->status = TodoStatus::closed();
        }
    }
}
