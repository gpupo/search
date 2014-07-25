<?php
/*
 * This file is part of the sfs package.
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search;

use Gpupo\Petfinder\Sphinx\SphinxService;
use Gpupo\Petfinder\Search\Result\CollectionInterface;
use Gpupo\Petfinder\Search\Result\Collection;
use Gpupo\Petfinder\Search\Query\QueryInterface;
use Gpupo\Petfinder\Search\Paginator\Paginator;

class Search  extends SearchAbstract implements SearchInterface
{
    protected static $_instance;

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            $class=get_called_class();
            self::$_instance = new $class();
        }

        return self::$_instance;
    }

    public function findByQuery(QueryInterface $query)
    {
        $collection = $this->getCollection(
            $query->getIndex(),
            $query->getFilters(),
            $query->getQueries(),
            $query->getFieldWeights(),
            $query->getLimit(),
            $query->getOffSet()
        );

        $paginator = new Paginator;
        
        if ($query->getPaginator()) {
            $page = $query->getPaginator()->getPage();
        } else {
            $page = 1;
        }

        $paginator->paginateResult($collection, $page);
        $collection->setPaginator($paginator);

        return $collection;
    }

    public function searchByKeyword($keyword)
    {
        $results = $this->search(
               'produtoIndex',
               null,
               array(array(
                   'key'    => '*',
                    'values' => array(
                       $keyword,
                    ),
                   'strict' => false,
               )),
               null,
               20,
               0
           );

        return $results;
    }

    public function getResultsByKeyword($keyword, CollectionInterface $collection)
    {
        $results = $this->searchByKeyword($keyword);

        if ($results) {
            $collection->load($results);

            return $collection;
        }
    }

    /**
     *
     * @return CollectionInterface
     */
    public function factoryCollection(array $array)
    {
        $collection =  new Collection($array);

        return $collection;
    }
    
    /**
     * Acesso ao Client Sphinx Search
     * 
     * @return \SphinxClient
     * 
     */
    public function getSphinxClient()
    {
        return SphinxService::getInstance()->getFreshClient();
    }
    
 }