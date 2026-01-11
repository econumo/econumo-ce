<?php

declare(strict_types=1);


namespace App\EconumoBundle\Infrastructure\Doctrine\Repository\Traits;

trait DeleteTrait
{
    public function delete(array $items): void
    {
        foreach ($items as $item) {
            $this->getEntityManager()->remove($item);
        }

        if ($items !== []) {
            $this->getEntityManager()->flush();
        }
    }
}
