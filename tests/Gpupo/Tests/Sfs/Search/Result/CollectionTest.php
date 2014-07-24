<?php

namespace Gpupo\Tests\Sfs\Search\Result;

use Gpupo\Tests\Sfs\TestCaseAbstract;
use Gpupo\Sfs\Search\Search;
use Gpupo\Sfs\Search\Result\Collection;

class CollectionTest extends TestCaseAbstract
{
    /**
     * @dataProvider dataProviderProdutosComMarcaNoNome
     */
    public function testResultadosComPropriedadesProcessadas($keyword)
    {
        $collection = new Collection;
        $results = Search::getInstance()->getResultsByKeyword($keyword, $collection);

        $this->assertInstanceOf('\Gpupo\Sfs\Search\Result\CollectionInterface', $results);

        $this->assertGreaterThan(2, $results->count());

        foreach ($results->toArray() as $produto) {
            $this->assertInstanceOf('\Gpupo\Sfs\Search\Result\ItemInterface', $produto);
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
