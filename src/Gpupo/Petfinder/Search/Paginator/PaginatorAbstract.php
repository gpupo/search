<?php
/*
 * This file is part of the sfs package.
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search\Paginator;

/**
 * Componente de paginacao de resultados da Busca
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
     * Get currently used page number
     *
     * @return integer
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
     * Get number of items per page
     *
     * @return integer
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
     * Get total item number available
     *
     * @return integer
     */
    public function getTotalItemCount()
    {
        return $this->totalCount;
    }

    /**
     * Offsets the result list by the number of places set by the count;
     *
     * This would be used for pagination through results, where if you have 20
     * results per 'page', the second page would begin at offset 20, the third
     * page at offset 40, etc.
     *
     * @return integer
     */
    public function getOffset()
    {
        return abs($this->getCurrentPageNumber() - 1) * $this->getItemNumberPerPage();
    }

    /**
     * Processa os valors de resultado
     *
     * @param integer $numTotal
     * @param integer $page
     * @param integer $limit
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
     * Pagination page range
     *
     * @param integer $range
     */
    public function setPageRange($range)
    {
        $this->range = intval(abs($range));
    }

    /**
     * Pagination page range
     *
     * @return integer
     */
    public function getPageRange()
    {
        return $this->range;
    }

}
