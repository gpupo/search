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

/**
 * Componente de paginacao de resultados da Busca.
 *
 * Não gerencia os itens de resultados, mas apenas os números de limite e offset
 * de uma query Sphinx Search
 *
 * Usado como referencia o Knp Pager component.
 */
abstract class PaginatorAbstract
{
    protected $range = 5;
    protected $currentPageNumber;
    protected $numItemsPerPage;
    protected $totalCount;

    public function setCurrentPageNumber($pageNumber)
    {
        $this->currentPageNumber = $pageNumber;
    }

    /**
     * Get currently used page number.
     *
     * @return int
     */
    public function getCurrentPageNumber()
    {
        return $this->currentPageNumber;
    }

    public function setItemNumberPerPage($numItemsPerPage)
    {
        $this->numItemsPerPage = $numItemsPerPage;
    }

    /**
     * Get number of items per page.
     *
     * @return int
     */
    public function getItemNumberPerPage()
    {
        return $this->numItemsPerPage;
    }

    public function setTotalItemCount($numTotal)
    {
        $this->totalCount = $numTotal;
    }

    /**
     * Get total item number available.
     *
     * @return int
     */
    public function getTotalItemCount()
    {
        return $this->totalCount;
    }

    /**
     * Offsets the result list by the number of places set by the count;.
     *
     * This would be used for pagination through results, where if you have 20
     * results per 'page', the second page would begin at offset 20, the third
     * page at offset 40, etc.
     *
     * @return int
     */
    public function getOffset()
    {
        return abs($this->getCurrentPageNumber() - 1) * $this->getItemNumberPerPage();
    }

    /**
     * Processa os valors de resultado.
     *
     * @param int $numTotal
     * @param int $page
     * @param int $limit
     */
    public function paginate($numTotal, $page, $limit = 10)
    {
        $this->setTotalItemCount($numTotal);
        $this->setCurrentPageNumber($page);
        $this->setItemNumberPerPage($limit);
    }

    public function getPagesCount()
    {
        return intval(ceil($this->getTotalItemCount() / $this->getItemNumberPerPage()));
    }

    /**
     * Pagination page range.
     *
     * @param int $range
     */
    public function setPageRange($range)
    {
        $this->range = intval(abs($range));
    }

    /**
     * Pagination page range.
     *
     * @return int
     */
    public function getPageRange()
    {
        return $this->range;
    }
}
