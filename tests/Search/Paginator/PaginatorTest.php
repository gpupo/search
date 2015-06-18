<?php

/*
 * This file is part of gpupo/petfinder
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Tests\Petfinder\Search\Paginator;

use Gpupo\Petfinder\Search\Paginator\Paginator;
use Gpupo\Petfinder\Search\Query\Keywords;
use Gpupo\Petfinder\Search\Query\Query;
use Gpupo\Petfinder\Search\Search;
use Gpupo\Tests\Petfinder\TestCaseAbstract;

class PaginatorTest extends TestCaseAbstract
{
    protected function getResultCollection($word)
    {
        $keywords = new Keywords();
        $keywords->addKeyword($word);

        $query = new Query($keywords);
        $query->setIndex('produtoIndex');

        $collection = Search::getInstance()->findByQuery($query);

        return $collection;
    }

    public function testResultadosPossuiObjetoModeladoParaPaginacao()
    {
        if (!$this->hasHost()) {
            return $this->markTestSkipped();
        }

        $results = $this->getResultCollection('shampoo');
        $this->assertInstanceOf('\Gpupo\Petfinder\Search\Paginator\PaginableInterface', $results);
        $this->assertInstanceOf('\Gpupo\Petfinder\Search\Paginator\PaginatorInterface', $results->getPaginator());
        $this->assertInstanceOf('\Gpupo\Petfinder\Search\Paginator\Paginator', $results->getPaginator());

        return $results;
    }

    /**
     * @depends testResultadosPossuiObjetoModeladoParaPaginacao
     */
    public function testProcessaResultCollection($results)
    {
        $paginator = $results->getPaginator();
        $paginator->paginateResult($results, 1, 10);
        $this->assertEquals($results->getTotal(), $paginator->getTotalItemCount());
    }

    /**
     * @dataProvider dataProviderOffset
     */
    public function testMarcaAPaginaAtual($page, $resultados, $limit)
    {
        $paginator = new Paginator();
        $paginator->paginate($resultados, $page, $limit);
        $this->assertEquals($page, $paginator->getCurrentPageNumber());
    }
    /**
     * @dataProvider dataProviderOffset
     */
    public function testManipulaOffsetDeQuery($page, $resultados, $limit, $expectedValue)
    {
        $paginator = new Paginator();
        $paginator->paginate($resultados, $page, $limit);
        $this->assertEquals($expectedValue, $paginator->getOffset());
    }

    /**
     * @dataProvider dataProviderOffset
     */
    public function testManipulaLimitDeQuery($page, $resultados, $limit)
    {
        $paginator = new Paginator();
        $paginator->paginate($resultados, $page, $limit);
        $this->assertEquals($limit, $paginator->getItemNumberPerPage());
    }

    /**
     * @dataProvider dataProviderPaginas
     */
    public function testDivideResultadosEmPaginasDeAcordoComLimite($resultados,
        $page, $limit, $expectedPages)
    {
        $paginator = new Paginator();
        $this->assertInstanceOf('\Gpupo\Petfinder\Search\Paginator\PaginatorInterface', $paginator);
        $paginator->paginate($resultados, $page, $limit);

        $this->assertEquals($expectedPages, $paginator->getPagesCount());
    }

    /**
     * @dataProvider dataProviderRanges
     */
    public function testAcessoAoRangeDePaginasAproximadas($page, $resultados, $limit, array $rangeExpected)
    {
        $paginator = new Paginator();
        $paginator->paginate($resultados, $page, $limit);

        $data = $paginator->getPaginationData();

        $this->assertEquals($data['pagesInRange'], $rangeExpected);
    }

    /**
     * @dataProvider dataProviderCustomRanges
     */
    public function testPermiteCustomizacaoDoRangeDePaginasParaNavegacao($page, $resultados, $limit, $range, $rangeExpected)
    {
        $paginator = new Paginator();
        $paginator->paginate($resultados, $page, $limit);
        $paginator->setPageRange($range);

        $this->assertEquals($range, $paginator->getPageRange());
        $data = $paginator->getPaginationData();
        $this->assertEquals($rangeExpected, $data['pagesInRange']);
    }

    /**
     * @dataProvider dataProviderLongRanges
     */
    public function testAcessoAValoresDaPaginacao($page, $resultados, $limit)
    {
        $paginator = new Paginator();
        $paginator->paginate($resultados, $page, $limit);

        $list = [
            'last',
            'current',
            'numItemsPerPage',
            'first',
            'pageCount',
            'totalCount',
            'previous',
            'next',
            'pagesInRange',
            'firstPageInRange',
            'lastPageInRange',
        ];

        foreach ($list as $k) {
            $this->assertArrayHasKey($k, $paginator->getPaginationData());
        }
    }

    public function dataProviderRanges()
    {
        $array = $this->dataProviderLongRanges();

        $array[] = [2, 20,  10, [1,2]];

        return $array;
    }

    /**
     * @return array [page, resultados, limit]
     */
    public function dataProviderLongRanges()
    {
        return [

            [5, 100, 10, [3,4,5,6,7]],
            [6, 100, 10, [4,5,6,7,8]],
            [7, 100, 10, [5,6,7,8,9]],
            [8, 100, 10, [6,7,8,9,10]],
        ];
    }

    /**
     * @return array [page, resultados, limit, $range, expectedRange]
     */
    public function dataProviderCustomRanges()
    {
        return [
            [5, 100, 10, 3, [4,5,6]],
            [5, 100, 10, 7, [2,3,4,5,6,7,8]],
            [5, 100, 10, 9, [1,2,3,4,5,6,7,8,9]],
            [5, 100, 10, 11, [1,2,3,4,5,6,7,8,9,10]],
            [5, 100, 10, 21, [1,2,3,4,5,6,7,8,9,10]],
            [5, 100, 2, 11, [1,2,3,4,5,6,7,8,9,10,11]],
            [5, 100, 2, 13, [1,2,3,4,5,6,7,8,9,10,11,12,13]],
        ];
    }
    /**
     * @return array [resultados,page,limit,expectedPages]
     */
    public function dataProviderPaginas()
    {
        return [
            [100, 1, 10, 10],
            [10, 1, 10, 1],
            [99, 1, 10, 10],
            [101, 1, 10, 11],
            [11, 1, 10, 2],
            [11, 1, 2, 6],
        ];
    }

    public function dataProviderOffset()
    {
        //page,results,limit,expectedValue
        return [
            [
                3,10,2,4,
            ],
            [
                3,100,10,20,
            ],
            [
                4,100,10,30,
            ],
            [
                5,100,10,40,
            ],
            [
                9,99,10,80,
            ],
            [
                9,99,1,8,
            ],
            [
                99,99,1,98,
            ],
        ];
    }
}
