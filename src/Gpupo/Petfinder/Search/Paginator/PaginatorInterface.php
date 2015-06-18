<?php

/*
 * This file is part of gpupo/petfinder
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search\Paginator;

interface PaginatorInterface
{
    /**
     * @param int $pageNumber
     */
    public function setCurrentPageNumber($pageNumber);

    /**
     * @param int $numItemsPerPage
     */
    public function setItemNumberPerPage($numItemsPerPage);
}
