<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <contact@gpupo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Tests\Search\Query;

use Gpupo\Search\Query\Keywords;
use Gpupo\Search\Query\Query;
use Gpupo\Search\Search;
use Gpupo\Tests\Search\TestCaseAbstract;

class KeywordsTest extends TestCaseAbstract
{
    /**
     * @cover \Gpupo\Search\Query\Keywords
     */
    public function testProcessaPalavrasChaveAPartirDeString()
    {
        $keywords = new Keywords();
        $keywords->readString('shampoo condicionador');

        $data = $keywords->getData();

        foreach (['key', 'values', 'strict'] as $key) {
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
        $keywords = new Keywords();
        $keywords->readString($string);
        $this->assertEquals([$string], $keywords->getValues());
    }

    /**
     * @dataProvider      dataProviderPalavrasInvalidas
     * @expectedException \Exception
     */
    public function testValidaStringDePalavrasChaveVaziasOuMenorQueOPermitido($string)
    {
        $keywords = new Keywords();
        $keywords->readString($string);
    }

    /**
     * @dataProvider      dataProviderFrasesValidas
     */
    public function testSucessoAoPesquisarComFrases($string)
    {
        if (!$this->hasHost()) {
            return $this->markTestSkipped();
        }

        $keywords = new Keywords();
        $keywords->readString($string);
        $query = new Query($keywords);
        $query->setIndex('produtoIndex');

        $collection = Search::getInstance()->findByQuery($query);

        $this->assertInstanceOf('\Gpupo\Search\Result\Collection', $collection);
        $this->assertGreaterThan(5, $collection->getTotal());
        $this->assertGreaterThan(5, $collection->getTotalFound());
        $this->assertInternalType('integer', $collection->getTotal());
        $this->assertInternalType('integer', $collection->getTotalFound());
    }

    public function dataProviderPalavrasValidas()
    {
        return [
            ['per'],
            ['perfume'],
            ['perf'],
            ['sham'],
        ];
    }

    public function dataProviderPalavrasInvalidas()
    {
        return [
            [''],
            [' '],
            [' a'],
            [' a '],
            [' a 4'],
            [' a 4'],
        ];
    }

    public function dataProviderFrasesValidas()
    {
        return [
            ['Shampoo e condicionador'],
            ['Perfume Feminino'],
        ];
    }
}
