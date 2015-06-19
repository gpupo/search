<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Paginator;

interface PaginableInterface
{
    public function getPaginator();

    public function setPaginator(PaginatorInterface $paginator);
}