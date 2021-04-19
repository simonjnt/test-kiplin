<?php
declare(strict_types=1);

namespace App\Domain;

final class TodoWasClosed implements DomainEvent
{
    public function __construct(
        public TodoId $id
    ) {
    }

    /**
     * @param array{'id': string} $payload
     */
    protected function setPayload(array $payload): void
    {
        $this->id = TodoId::fromString($payload['id']);
    }

    /**
     * @return array{'id': string}
     */
    public function payload(): array
    {
        return [
            'id' => $this->id->asString(),
        ];
    }
}
