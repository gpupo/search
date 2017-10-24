<?php

/*
 * This file is part of gpupo/search
 *
 * (c) Gilmar Pupo <contact@gpupo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gpupo\Search\Sphinx;

use Doctrine\Common\Collections\ArrayCollection;

class SphinxService
{
    protected static $_instance;

    protected $client;

    protected $parameters;

    public function setParameters(Array $customParameters = [])
    {
        $defaultParameters = [
            'host'    => 'localhost',
            'port'    => '9313',
            'timeout' => 5,
        ];

        $array = array_merge($defaultParameters, $customParameters);

        $this->parameters = new ArrayCollection($array);

        return $this;
    }

    public function getParameters()
    {
        if (!$this->parameters) {
            $this->setParameters();
        }

        return $this->parameters;
    }

    /**
     * Factory e Configuracao padrao de um SphinxClient.
     *
     * @return SphinxClient
     */
    public function createService()
    {
        $host = $this->getParameters()->get('host');
        $port = $this->getParameters()->get('port');
        $timeout = $this->getParameters()->get('timeout');

        if (is_null($host) || is_null($port)) {
            throw new \Exception(
                'No sphinx server information found within the configuration!'
            );
        }

        $sphinxClient = new SphinxClient();
        $sphinxClient->SetServer($host, $port);
        $sphinxClient->SetConnectTimeout($timeout);
        $sphinxClient->SetArrayResult(true);
        $sphinxClient->setMatchModeByModeName('any');
        $sphinxClient->SetSortMode(SPH_SORT_RELEVANCE);
        $sphinxClient->SetRankingMode(SPH_RANK_PROXIMITY);

        return $sphinxClient;
    }

    /**
     * Acesso ao Cliente Sphinx Server.
     *
     * @return Gpupo\Search\Sphinx\SphinxClient
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = $this->createService();
        }

        return $this->client;
    }

    /**
     * Semelhante a *getClient()* mas com reset.
     *
     * @return Gpupo\Search\Sphinx\SphinxClient
     */
    public function getFreshClient()
    {
        return $this->reset()->getClient();
    }

    /**
     * Limpa informações de pesquisas anteriores.
     */
    public function reset()
    {
        $this->getClient()->ResetFilters();
        $this->getClient()->ResetGroupBy();
        $this->getClient()->ResetOverrides();

        return $this;
    }

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            $class = get_called_class();
            self::$_instance = new $class();
        }

        return self::$_instance;
    }
}
