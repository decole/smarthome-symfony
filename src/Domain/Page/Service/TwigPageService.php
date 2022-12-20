<?php

namespace App\Domain\Page\Service;

use App\Domain\Contract\Repository\PageRepositoryInterface;
use App\Domain\Page\Entity\Page;

final class TwigPageService
{
    public function __construct(private PageRepositoryInterface $repository)
    {
    }

    /**
     * @return list<Page>
     */
    public function map(): array
    {
        return $this->repository->findAll();
    }
}