<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <contact@gpupo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Paginator;

use Gpupo\Search\Result\CollectionInterface;

class Paginator extends PaginatorAbstract implements PaginatorInterface
{
    public function paginateResult(CollectionInterface $collection, $page, $limit = 10)
    {
        $limit = intval(abs($limit));
        if (!$limit) {
            throw new \LogicException('Invalid item per page number, must be a positive number');
        }

        $this->paginate($collection->getTotalFound(), $page, $limit);

        return $this;
    }

    public function getPaginationData()
    {
        $pages = $this->getPages();

        $viewData = [
            'last'              => $this->getPagesCount(),
            'current'           => $this->getCurrentPageNumber(),
            'numItemsPerPage'   => $this->getItemNumberPerPage(),
            'first'             => 1,
            'pageCount'         => $this->getPagesCount(),
            'totalCount'        => $this->getTotalItemCount(),
        ];

        if ($this->getCurrentPageNumber() - 1 > 0) {
            $viewData['previous'] = $this->getCurrentPageNumber() - 1;
        }

        if ($this->getCurrentPageNumber() + 1 <= $this->getPagesCount()) {
            $viewData['next'] = $this->getCurrentPageNumber() + 1;
        }
        $viewData['pagesInRange'] = $pages;
        $viewData['firstPageInRange'] = min($pages);
        $viewData['lastPageInRange']  = max($pages);

        return $viewData;
    }

    /**
     * Acesso ao range de paginas para navegacao.
     *
     * @return array
     */
    public function getPages()
    {
        if ($this->getPageRange() > $this->getPagesCount()) {
            $this->setPageRange($this->getPagesCount());
        }

        $delta = ceil($this->getPageRange() / 2);

        if ($this->getCurrentPageNumber() - $delta > $this->getPagesCount() - $this->getPageRange()) {
            $pages = range($this->getPagesCount() - $this->getPageRange() + 1, $this->getPagesCount());
        } else {
            if ($this->getCurrentPageNumber() - $delta < 0) {
                $delta = $this->getCurrentPageNumber();
            }

            $offset = $this->getCurrentPageNumber() - $delta;
            $pages = range($offset + 1, $offset + $this->getPageRange());
        }

        return $pages;
    }
}
