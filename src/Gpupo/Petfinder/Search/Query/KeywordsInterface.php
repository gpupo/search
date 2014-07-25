<?php
/*
 * This file is part of the sfs package.
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Petfinder\Search\Query;

use Gpupo\Petfinder\Search\Core\RegisterableInterface;

interface KeywordsInterface extends RegisterableInterface
{
    public function addKeyword($string);
    public function readString($string);
    public function getData();
    public function getValues();
}
