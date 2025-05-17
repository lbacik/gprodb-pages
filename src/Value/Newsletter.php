<?php

declare(strict_types=1);

namespace App\Value;

use Symfony\Component\Uid\Uuid;

class Newsletter
{
    public string $email;

    public string $entityId;

    public static function create(Uuid $entityId): self
    {
        $data = new self();
        $data->entityId = $entityId->toRfc4122();

        return $data;
    }
}
