<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Tests\Search\Query;

use Gpupo\Search\Query\Filters;
use Gpupo\Search\Query\FiltersInterface;
use Gpupo\Search\Query\Keywords;
use Gpupo\Search\Query\KeywordsInterface;
use Gpupo\Search\Query\Query;
use Gpupo\Search\Query\QueryInterface;
use Gpupo\Search\Search;
use Gpupo\Tests\Search\TestCaseAbstract;

class QueryTest extends TestCaseAbstract
{
    public function testPalavrasChavesModeladasEmObjeto()
    {
        $keywords = new Keywords();
        $keywords->addKeyword('shampoo');
        $keywords->addKeyword('condicionador');

        $data = $keywords->getData();

        foreach (['key', 'values', 'strict'] as $key) {
            $this->assertArrayHasKey($key, $data);
        }

        $this->assertContains('shampoo', $keywords->getValues());
        $this->assertContains('condicionador', $keywords->getValues());

        $this->assertEquals('*', $keywords->getKey());

        return $keywords;
    }

    /**
     * @depends testPalavrasChavesModeladasEmObjeto
     */
    public function testPesquisaAPartirDeQueriesModeladas(KeywordsInterface $keywords)
    {
        if (!$this->hasHost()) {
            return $this->markTestSkipped();
        }

        $query = new Query($keywords);
        $query->setIndex('produtoIndex');

        $this->assertEquals('produtoIndex', $query->getIndex());

        $collection = Search::getInstance()->findByQuery($query);

        $this->assertInstanceOf('\Gpupo\Search\Result\Collection', $collection);
        $this->assertGreaterThan(2, $collection->getTotal());
        $this->assertGreaterThan(2, $collection->getTotalFound());
        $this->assertInternalType('integer', $collection->getTotal());
        $this->assertInternalType('integer', $collection->getTotalFound());

        return $query;
    }

    /**
     * @depends testPesquisaAPartirDeQueriesModeladas
     */
    public function testQueriesPossuemAtributosModeladosEControlados(QueryInterface $query)
    {
        $this->assertInstanceOf('\Gpupo\Search\Query\QueryInterface', $query);
        $this->assertInternalType('array', $query->getQueries());
        $this->assertGreaterThanOrEqual(1, $query->getQueries());
    }

    public function testRecebeEntradaDeLimitesDeResultados()
    {
        $keywords = new Keywords();
        $query = new Query($keywords);
        $query->setLimit(30);
        $this->assertEquals(30, $query->getLimit());

        return $query;
    }

    /**
     * @depends testRecebeEntradaDeLimitesDeResultados
     */
    public function testRecebeEntradaDeOffsetParaResultados(QueryInterface $query)
    {
        $this->assertEquals(0, $query->getOffset());
        $query->setOffset(44);
        $this->assertEquals(44, $query->getOffset());

        return $query;
    }

    /**
     * @depends           testRecebeEntradaDeOffsetParaResultados
     * @expectedException Exception
     */
    public function testValidaEntradaDeOffsetParaResultados(QueryInterface $query)
    {
        $query->setOffset('fail');
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage Limit deve ser numerico
     */
    public function testValidaEntradaDeLimitesDeResultados()
    {
        $keywords = new Keywords();
        $query = new Query($keywords);
        $query->setLimit('fail');
    }

    public function testPermitePesquisaEmMultiplosIndices()
    {
        $keywordsList = [];
        $keywordsList['first'] = new Keywords();
        $keywordsList['second'] = new Keywords();
        $keywordsList['other'] = new Keywords();
        $query = new Query();
        $query->setKeywords($keywordsList);
        $this->assertEquals(3, count($query->getKeywords()));
        $this->assertEquals(3, count($query->getQueries()));

        return $keywordsList;
    }

    /**
     * @depends           testPermitePesquisaEmMultiplosIndices
     * @expectedException Exception
     */
    public function testValidaEntradaParaMultiplosIndices($keywordsList)
    {
        $keywordsList[] = new \stdClass();
        $query = new Query();
        $query->setKeywords($keywordsList);
    }

    public function testPossuiFiltrosModelados()
    {
        $filters = new Filters();
        $filters->addValuesFilter('fornecedor_id', [285]);

        $this->assertEquals(1, count($filters->toArray()));
        $filter = current($filters->toArray());

        $this->assertArrayHasKey('key', $filter);
        $this->assertEquals('fornecedor_id', $filter['key']);

        return $filters;
    }

    /**
     * @depends testPossuiFiltrosModelados
     */
    public function testRecebeEntradaDeFiltros(FiltersInterface $filters)
    {
        $keywords = new Keywords();
        $keywords->addKeyword('shampoo');

        $query = new Query();
        $query->setKeyword($keywords);
        $query->setFilters($filters);

        $filter = current($query->getFilters());
        $this->assertArrayHasKey('key', $filter);
        $this->assertArrayHasKey('values', $filter);
        $this->assertContains(285, $filter['values']);
    }

    public function dataProviderKeywords()
    {
        $keywords = new Keywords();

        return [
            [$keywords],
        ];
    }

    public function testSuporteABuscaFacetadaPorUmAtributo()
    {
        //Queries sem contagem de atributos nao possuem countableAttribute
        $keywords = new Keywords();
        $keywords->addKeyword('shampoo');
        $query = new Query();
        $query->setKeyword($keywords);
        $firstQuery = current($query->getQueries());
        $this->assertArrayNotHasKey('countableAttributes', $firstQuery);

        //Queries com contagem de atributos possuem countableAttribute
        $query2 = new Query();
        $query2->setKeyword($keywords);
        $query2->addCountableAttribute('categorias');
        $secondQuery = current($query2->getQueries());

        //Existencia do atributo
        $this->assertContains('categorias', $query2->getCountableAttributes());
        $this->assertContains('categorias', $secondQuery['countableAttributes']);
    }

    public function testSuporteABuscaFacetadaPorMuitosAtributos()
    {
        $countableAttributes = ['categoria','fornecedor', 'tamanho'];
        $keywords = new Keywords();
        $keywords->addKeyword('shampoo');
        $query = new Query();
        $query->setKeyword($keywords);
        $query->setCountableAttributes($countableAttributes);

        $this->assertEquals(3, count($query->getCountableAttributes()));

        $firstQuery = current($query->getQueries());
        $this->assertArrayHasKey('countableAttributes', $firstQuery);
        $this->assertEquals(3, count($firstQuery['countableAttributes']));

        foreach ($countableAttributes as $attribute) {
            $this->assertContains($attribute, $firstQuery['countableAttributes']);
        }
    }

    public function testEvitaContagemPorAtributosDuplicada()
    {
        $countableAttributes = ['categoria','fornecedor', 'tamanho',
            'categoria', 'fornecedor', 'categoria', ];
        $keywords = new Keywords();
        $keywords->addKeyword('shampoo');
        $query = new Query();
        $query->setKeyword($keywords);
        $query->setCountableAttributes($countableAttributes);

        $this->assertEquals(3, count($query->getCountableAttributes()));

        $firstQuery = current($query->getQueries());

        $this->assertArrayHasKey('countableAttributes', $firstQuery);
        $this->assertEquals(3, count($firstQuery['countableAttributes']));
    }
}
