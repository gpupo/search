<?php

namespace Gpupo\Tests\Sfs\Search\Query;

use Gpupo\Tests\Sfs\TestCaseAbstract;
use Gpupo\Petfinder\Search\Search;
use Gpupo\Petfinder\Search\Query\Query;
use Gpupo\Petfinder\Search\Query\Keywords;
use Gpupo\Petfinder\Search\Query\Filters;
use Gpupo\Petfinder\Search\Query\KeywordsInterface;
use Gpupo\Petfinder\Search\Query\QueryInterface;
use Gpupo\Petfinder\Search\Query\FiltersInterface;

class QueryTest extends TestCaseAbstract
{
    public function testPalavrasChavesModeladasEmObjeto()
    {
        $keywords = new Keywords;
        $keywords->addKeyword('shampoo');
        $keywords->addKeyword('condicionador');

        $data = $keywords->getData();

        foreach (array('key', 'values', 'strict') as $key) {
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

        $query = new Query($keywords);
        $query->setIndex('produtoIndex');

        $this->assertEquals('produtoIndex', $query->getIndex());

        $collection = Search::getInstance()->findByQuery($query);

        $this->assertInstanceOf('\Gpupo\Petfinder\Search\Result\Collection', $collection);
        $this->assertGreaterThan(2, $collection->getTotal());
        $this->assertGreaterThan(2, $collection->getTotalFound());
        $this->assertInternalType('integer', $collection->getTotal());
        $this->assertInternalType('integer', $collection->getTotalFound());

        return $query;
    }

    /**
     *
     * @depends testPesquisaAPartirDeQueriesModeladas
     */
    public function testQueriesPossuemAtributosModeladosEControlados(QueryInterface $query)
    {
        $this->assertInstanceOf('\Gpupo\Petfinder\Search\Query\QueryInterface', $query);
        $this->assertInternalType('array', $query->getQueries());
        $this->assertGreaterThanOrEqual(1, $query->getQueries());
    }

    public function testRecebeEntradaDeLimitesDeResultados()
    {
        $keywords = new Keywords;
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
        $keywords = new Keywords;
        $query = new Query($keywords);
        $query->setLimit('fail');
    }

    public function testPermitePesquisaEmMultiplosIndices()
    {
        $keywordsList = array();
        $keywordsList['first'] = new Keywords;
        $keywordsList['second'] = new Keywords;
        $keywordsList['other'] = new Keywords;
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
        $filters = new Filters;
        $filters->addValuesFilter('fornecedor_id', array(285));

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
        $keywords = new Keywords;
        $keywords->addKeyword('shampoo');

        $query = new Query();
        $query->setKeyword($keywords);
        $query->setFilters($filters);

        $filter = current($query->getFilters());
        $this->assertArrayHasKey('key', $filter);
        $this->assertArrayHasKey('values', $filter);
        $this->assertContains(285, $filter['values']);
    }

    /**
     * @expectedException        Exception
     */
    public function testValidaEntradaDeFiltros()
    {
        $query = new Query();
        $query->setFilters(array());
    }

    public function dataProviderKeywords()
    {
        $keywords = new Keywords;

        return array(
            array($keywords),
        );
    }

    public function testSuporteABuscaFacetadaPorUmAtributo()
    {
        //Queries sem contagem de atributos nao possuem countableAttribute
        $keywords = new Keywords;
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
        $countableAttributes = array('categoria','fornecedor', 'tamanho');
        $keywords = new Keywords;
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
        $countableAttributes = array('categoria','fornecedor', 'tamanho',
            'categoria', 'fornecedor', 'categoria');
        $keywords = new Keywords;
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