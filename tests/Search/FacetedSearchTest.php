<?php

/*
 * This file is part of gpupo/petfinder
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Tests\Petfinder\Search;

use Gpupo\Petfinder\Search\Search;
use Gpupo\Petfinder\Sphinx\SphinxService;
use Gpupo\Tests\Petfinder\TestCaseAbstract;

class FacetedSearchTest extends TestCaseAbstract
{
    /**
     * Multi-queries are just a mechanism that lets you send several search
     * queries to searchd in one batch. That, in turn, lets searchd internally
     * optimize common parts between the queries. And that’s where the savings
     * come from.
     *
     * @see http://sphinxsearch.com/blog/2010/04/05/facets-multi-queries-and-searching-3x-faster/
     */
    public function testMultiQueries()
    {
        $cl = SphinxService::getInstance()->getClient();
        $this->assertInstanceOf('\SphinxClient', $cl);

        $cl->SetMatchMode(SPH_MATCH_EXTENDED2);
        $cl->SetSortMode(SPH_SORT_RELEVANCE);
        $cl->AddQuery('perfume', 'produtoIndex');
        $cl->SetSortMode(SPH_SORT_EXTENDED, 'categoria desc');
        $cl->AddQuery('perfume', 'produtoIndex');
        $cl->SetSortMode(SPH_SORT_EXTENDED, 'categoria asc');

        $cl->AddQuery('perfume', 'produtoIndex');

        if (!$this->hasHost()) {
            return $this->markTestSkipped();
        }

        $results = $cl->RunQueries();

        $this->assertInternalType('array', $results);

        foreach ($results as $item) {
            $this->assertArrayHasKey('attrs', $item);
        }
    }

    /**
     * Grouping (clustering) search results
     * Section 5.7.
     *
     * @see http://sphinxsearch.com/docs/current.html
     */
    public function testGroupBy()
    {
        $cl = SphinxService::getInstance()->getClient();
        $cl->SetGroupBy('categoria', SPH_GROUPBY_ATTR);
        $cl->AddQuery('shampoo', 'produtoIndex');

        if (!$this->hasHost()) {
            return $this->markTestSkipped();
        }

        $results = $cl->RunQueries();

        foreach ($results[0]['matches'] as $item) {
            $this->assertGreaterThan(0, $item['attrs']['@count']);
            $this->assertInternalType('integer', $item['attrs']['@count']);
            $this->assertInternalType('integer', $item['attrs']['categoria']);
        }
    }

    public function testSimplificaMultiplasQueriesGroupby()
    {
        $cl = SphinxService::getInstance()->getClient();

        $cl->SetMatchMode(SPH_MATCH_EXTENDED2);
        $cl->SetSortMode(SPH_SORT_RELEVANCE);

        if (!$this->hasHost()) {
            return $this->markTestSkipped();
        }

        $cl->addFacetedQuery('perfume', 'produtoIndex', ['categoria', 'fornecedor', 'tamanho']);

        $results = $cl->RunQueries();

        $this->assertMultiQueryResults($results);
    }

    /**
     * O mesmo teste que testSimplificaMultiplasQueriesGroupby() mas
     * usando Search::query().
     */
    public function testMultiqueryComGroupby()
    {
        $countableAttributes = ['categoria','fornecedor', 'tamanho'];

        $querys = [];

        $querys[] =   [
            'key'     => '*',
             'values' => [
                'shampoo',
             ],
            'countableAttributes' => $countableAttributes,
        ];

        if (!$this->hasHost()) {
            return $this->markTestSkipped();
        }

        $results = Search::getInstance()->query(
            'produtoIndex',
            null,
            $querys,
            null,
            2,
            0
        );

        foreach ($results[0]['matches'] as $item) {
            $this->assertArrayNotHasKey('@count', $item['attrs']);
        }

        $this->assertMultiQueryResults($results);
    }

    /**
     * Reutilizacao de afirmacoes.
     */
    protected function assertMultiQueryResults(array $results)
    {
        foreach ($results[1]['matches'] as $item) {
            $this->assertTrue(is_array($item['attrs']));
            $this->assertArrayHasKey('@count', $item['attrs']);
            $this->assertGreaterThan(0, $item['attrs']['@count']);
            $this->assertInternalType('integer', $item['attrs']['@count']);
            $this->assertInternalType('integer', $item['attrs']['categoria']);
        }

        foreach ($results[2]['matches'] as $item) {
            $this->assertTrue(is_array($item['attrs']));
            $this->assertArrayHasKey('@count', $item['attrs']);
            $this->assertGreaterThan(0, $item['attrs']['@count']);
            $this->assertInternalType('integer', $item['attrs']['@count']);
            $this->assertInternalType('integer', $item['attrs']['fornecedor']);
        }

        foreach ($results[3]['matches'] as $item) {
            $this->assertTrue(is_array($item['attrs']));
            $this->assertArrayHasKey('@count', $item['attrs']);
            $this->assertGreaterThan(0, $item['attrs']['@count']);
            $this->assertInternalType('integer', $item['attrs']['@count']);
        }

        //Resultados paginados + 3 atributos
        $this->assertCount(4, $results);
    }
}
