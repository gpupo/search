<?php

namespace Gpupo\Tests\Sfs\Search\Result;

use Gpupo\Tests\Sfs\TestCaseAbstract;
use Gpupo\Petfinder\Search\Search;
use Gpupo\Petfinder\Search\Result\Collection;

class CollectionTest extends TestCaseAbstract
{
    /**
     * @dataProvider dataProviderProdutosComMarcaNoNome
     */
    public function testResultadosComPropriedadesProcessadas($keyword)
    {
        $collection = new Collection;
        $results = Search::getInstance()->getResultsByKeyword($keyword, $collection);

        $this->assertInstanceOf('\Gpupo\Petfinder\Search\Result\CollectionInterface', $results);

        $this->assertGreaterThan(2, $results->count());

        foreach ($results->toArray() as $produto) {
            $this->assertInstanceOf('\Gpupo\Petfinder\Search\Result\ItemInterface', $produto);
        }

        return $results;
    }

    public function dataProviderProdutosComMarcaNoNome()
    {
        return array(
            array('herrera', "carolina"),
        );
    }
}
