<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Query;

interface KeywordsInterface
{
    public function addKeyword($string);
    public function readString($string);
    public function getData();
    public function getValues();
}
