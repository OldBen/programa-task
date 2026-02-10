<?php

namespace App\Application\UseCases\Users\Commands;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
class FetchUserListCommand
{
    public function __construct(
        private int $count = 10
    ) {
    }

    public function getCount(): int
    {
        return $this->count;
    }
}