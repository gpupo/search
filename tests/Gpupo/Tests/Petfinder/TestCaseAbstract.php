<?php

namespace Gpupo\Tests\Sfs;

abstract class TestCaseAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * Verifica se uma string possui a ocorroncia de um dos valores do array informado
     *
     * @param  string  $string
     * @param  array   $findme
     * @return boolean
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
                $keywords = array($keyword, $keywords);
            }
        } else {
            $keywords = array($keyword);
        }

        return $this->stringContainsOneOfArray($string, $keywords);

    }

   /**
    * Asserts that a string contains one keyword
    *
    * @param  string $keyword
    * @param  string $string
    * @param  string $message
    * @param  array  $alternativeKeywords
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
    * Asserts that a string contains one of keywords
    *
    * Example:
    * <code>
    *   $this->assertStringContainsOneOrMore(array('shampoo','condicionador'), json_encode($item));
    * </code>
    * @param  array $keywords
    * @param  string $string
    * @param  string $message    
    */
    public function assertStringContainsOneOrMore(array $keywords, $string, $message = '')
    {
        return $this->assertStringContains(current($keywords), $string, $message, $keywords);
    }
    
}

