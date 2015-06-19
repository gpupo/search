<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Tests\Search;

use Gpupo\Search\Sphinx\SphinxService;

abstract class TestCaseAbstract extends \PHPUnit_Framework_TestCase
{
    protected function getSphinxHost()
    {
        return $this->getConstant('SPHINX_HOST');
    }

    protected function hasHost()
    {
        return ($this->getSphinxHost());
    }

    protected function getSphinxServerParameters()
    {
        return [
            'host'      => $this->getSphinxHost(),
            'port'      => $this->getConstant('SPHINX_PORT', '9313'),
            'timeout'   => $this->getConstant('SPHINX_TIMEOUT', '5'),
        ];
    }

    /**
     * Configure Sphinx Server Parameters.
     */
    public function setUp()
    {
        if ($this->hasHost()) {
            SphinxService::getInstance()
                ->setParameters($this->getSphinxServerParameters());
        }
    }
    /**
     * Verifica se uma string possui a ocorroncia de um dos valores do array informado.
     *
     * @param string $string
     * @param array  $findme
     *
     * @return bool
     */
    public function stringContainsOneOfArray($string, array $findme)
    {
        $string = strtolower($string);

        foreach ($findme as $find) {
            if (!empty($find)) {
                if (strpos($string, strtolower($find)) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    public function stringContainOneOrAlternatives($string, $keyword, $keywords = null)
    {
        if ($keywords) {
            if (is_array($keywords)) {
                array_unshift($keywords, $keyword);
            } else {
                $keywords = [$keyword, $keywords];
            }
        } else {
            $keywords = [$keyword];
        }

        return $this->stringContainsOneOfArray($string, $keywords);
    }

    /**
     * Asserts that a string contains one keyword.
     *
     * @param string $keyword
     * @param string $string
     * @param string $message
     * @param array  $alternativeKeywords
     */
    public function assertStringContains($keyword, $string, $message = '',
        $alternativeKeywords = null)
    {
        $contain = $this->stringContainOneOrAlternatives(strtolower($string), $keyword, $alternativeKeywords);

        if (empty($message)) {
            $message = "[$string] must contain $keyword";
        }

        return $this->assertTrue($contain, $message, $string);
    }

    /**
     * Asserts that a array contains one of keywords.
     *
     * Example:
     * <code>
     *   $this->assertArrayContainsOneOrMore(array('shampoo','condicionador'), $item);
     * </code>
     *
     * @param array  $keywords
     * @param array  $array
     * @param string $message
     */
    public function assertArrayContainsOneOrMore($keywords, array $array, $message = '')
    {
        $string = $this->md_implode($array);

        return $this->stringContainOneOrAlternatives($string, null, $message, $keywords);
    }

    protected function md_implode($array, $glue = ' ')
    {
        if (is_array($array)) {
            $output = '';
            foreach ($array as $v) {
                $output .= $this->md_implode($v, $glue);
            }

            return $output;
        } else {
            return $array.$glue;
        }
    }
}
