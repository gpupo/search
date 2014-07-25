<?php

namespace Gpupo\Tests\Sfs\Search\Query;

use Gpupo\Tests\Sfs\TestCaseAbstract;
use Gpupo\Petfinder\Search\Search;
use Gpupo\Petfinder\Search\Query\Query;
use Gpupo\Petfinder\Search\Query\Keywords;

class KeywordsTest extends TestCaseAbstract
{

    /**
     * @cover \Gpupo\Petfinder\Search\Query\Keywords
     */
    public function testProcessaPalavrasChaveAPartirDeString()
    {
        $keywords = new Keywords;
        $keywords->readString('shampoo condicionador');

        $data = $keywords->getData();

        foreach (array('key', 'values', 'strict') as $key) {
            $this->assertArrayHasKey($key, $data);
        }

        $this->assertContains('shampoo', $keywords->getValues());
        $this->assertContains('condicionador', $keywords->getValues());

        return $keywords;
    }

    /**
     * @dataProvider      dataProviderPalavrasValidas
     */
    public function testSucessoComPalavrasChavesValidas($string)
    {
        $keywords = new Keywords;
        $keywords->readString($string);
        $this->assertEquals(array($string), $keywords->getValues());
    }

    /**
     * @dataProvider      dataProviderPalavrasInvalidas
     * @expectedException Exception
     */
    public function testValidaStringDePalavrasChaveVaziasOuMenorQueOPermitido($string)
    {
        $keywords = new Keywords;
        $keywords->readString($string);
        $this->assertEquals(array(), $keywords->getValues());
    }

    /**
     * @dataProvider      dataProviderFrasesValidas
     */
    public function testSucessoAoPesquisarComFrases($string)
    {
        $keywords = new Keywords;
        $keywords->readString($string);
        $query = new Query($keywords);
        $query->setIndex('produtoIndex');

        $collection = Search::getInstance()->findByQuery($query);

        $this->assertInstanceOf('\Gpupo\Petfinder\Search\Result\Collection', $collection);
        $this->assertGreaterThan(5, $collection->getTotal());
        $this->assertGreaterThan(5, $collection->getTotalFound());
        $this->assertInternalType('integer', $collection->getTotal());
        $this->assertInternalType('integer', $collection->getTotalFound());

    }

    public function dataProviderPalavrasValidas()
    {
        return array(
            array('per'),
            array('perfume'),
            array('perf'),
            array('sham'),
        );
    }

    public function dataProviderPalavrasInvalidas()
    {
        return array(
            array(''),
            array(' '),
            array(' a'),
            array(' a '),
            array(' a 4'),
            array(' a 4'),
        );
    }

    public function dataProviderFrasesValidas()
    {
        return array(
            array('Shampoo e condicionador'),
            array('Perfume Feminino'),
        );
    }
}
