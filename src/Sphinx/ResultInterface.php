<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Sphinx;

interface ResultInterface
{
    /**
     * Lista de documentos encontrados.
     */
    public function getMatches();

    /**
     * Quantidade de documentos encontrados para a query, independente da paginação.
     */
    public function getTotal();

    /**
     * @see getTotal()
     */
    public function getTotalFound();

    /**
     * Tempo necessário para a pesquisa.
     */
    public function getTime();

    /**
     * Detalhes dos resultados para cada palavra.
     */
    public function getWords();
}
