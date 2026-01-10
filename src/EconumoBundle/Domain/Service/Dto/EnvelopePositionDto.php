<?php

declare(strict_types=1);

namespace App\EconumoBundle\Domain\Service\Dto;

class EnvelopePositionDto
{
    public string $id;

    public ?string $folderId = null;

    public int $position;
}
