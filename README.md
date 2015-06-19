[![Author](http://img.shields.io/badge/author-@gpupo-blue.svg)](https://twitter.com/gpupo)
[![MIT License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://github.com/gpupo/search/blob/master/LICENSE)
[![Build Status](https://secure.travis-ci.org/gpupo/search.png?branch=master)](http://travis-ci.org/gpupo/search

# Search

PHP Sphinx faceted search over Official Sphinx searchd client (PHP API) with Oriented Object results based

### Simple Query Usage

```PHP

<?php
use Gpupo\Search\Search\Search;
use Gpupo\Search\Query\Keywords;
use Gpupo\Search\Query\Query;

$keywords = new Keywords;
$keywords->addKeyword('magic');
$keywords->addKeyword('unicorn');
$query = new Query($keywords);
$query->setIndex('fantasyIndex');

//Configure Sphinx Server Parameters:
SphinxService::getInstance()->setParameters(array(
	'host'    => 'foo.bar.com', //default value is localhost
));

$results = Search::getInstance()->findByQuery($query);

$results->getTotal(); // Itens found

```
## Sandbox

**New!** Check [index.php](https://github.com/gpupo/peccary/blob/master/web/index.php)
in [peccary project](https://github.com/gpupo/peccary/)
(Catalog Sandbox with Search component, Sphinx Search and Silex Framework) for more examples.


## Install

The recommended way to install is [through composer](http://getcomposer.org).

    composer require gpupo/search

---

# Dev

Install [through composer](http://getcomposer.org):

	composer install;

Copy ``phpunit`` configuration file:

    cp phpunit.xml.dist phpunit.xml;

Customize Sphinx Search Server parameters in ``phpunit.xml``:

```XML
<php>
	<const name="SPHINX_HOST" value="localhost"/>
 	<const name="SPHINX_PORT" value="9313"/>
 	<const name="SPHINX_TIMEOUT" value="5"/>
</php>
```

To run localy the test suite:

    $ phpunit

or see the testdox output

    $ phpunit --testdox


## Tests results

<!-- output of this command:
         phpunit --testdox | sed "s/.*\[/-&/" | sed 's/.*Gpupo.*/&\'$'\n/g'
-->

Gpupo\Tests\Search\Search\FacetedSearch

- [x] Multi queries
- [x] Group by
- [x] Simplifica multiplas queries groupby
- [x] Multiquery com groupby

Gpupo\Tests\Search\Search\Paginator\Paginator

- [x] Resultados possui objeto modelado para paginacao
- [x] Processa result collection
- [x] Marca a pagina atual
- [x] Manipula offset de query
- [x] Manipula limit de query
- [x] Divide resultados em paginas de acordo com limite
- [x] Acesso ao range de paginas aproximadas
- [x] Permite customizacao do range de paginas para navegacao
- [x] Acesso a valores da paginacao

Gpupo\Tests\Search\Search\Query\Filters

- [x] Filtra por lista de valores de uma chave
- [x] Filtra por range de valores de uma chave
- [x] Adiciona um valor a values filters existente

Gpupo\Tests\Search\Search\Query\Keywords

- [x] Processa palavras chave a partir de string
- [x] Sucesso com palavras chaves validas
- [x] Valida string de palavras chave vazias ou menor que o permitido
- [x] Sucesso ao pesquisar com frases

Gpupo\Tests\Search\Search\Query\Query

- [x] Palavras chaves modeladas em objeto
- [x] Pesquisa a partir de queries modeladas
- [x] Queries possuem atributos modelados e controlados
- [x] Recebe entrada de limites de resultados
- [x] Recebe entrada de offset para resultados
- [x] Valida entrada de offset para resultados
- [x] Valida entrada de limites de resultados
- [x] Permite pesquisa em multiplos indices
- [x] Valida entrada para multiplos indices
- [x] Possui filtros modelados
- [x] Recebe entrada de filtros
- [x] Valida entrada de filtros
- [x] Suporte a busca facetada por um atributo
- [x] Suporte a busca facetada por muitos atributos
- [x] Evita contagem por atributos duplicada

Gpupo\Tests\Search\Search\Result\Collection

- [x] Resultados com propriedades processadas

Gpupo\Tests\Search\Search\Search

- [x] Resultados contendo objetos modelados
- [x] Pesquisa palavra chave simples
- [x] Possui quantidade limite de resultados
- [x] Resultados possuem atributos
- [x] Pesquisa por multiplas palavras
- [x] Pesquisa por parte de palavra
- [x] Pesquisa com palavras fora de ordem
- [x] Acesso a quantidade de resultados disponiveis
- [x] Acesso a quantidade de resultados disponiveis por palavra
- [x] Acesso a resultados em objetos modelados
- [x] Suporte a multi queries

Gpupo\Tests\Search\Sphinx\SphinxService

- [x] Permite acesso aos parametros default
- [x] Permite definicao de parametros personalizados
- [x] Disponibiliza acesso ao client
- [x] Acesso singleton ao client com reset


## Todo

- [ ] Translate items written originally in Brazilian portuguese;
- [ ] Finds a Sphinx Search public server for use in ``Travis`` tests (see the [stackoverflow question](http://stackoverflow.com/questions/24958234/there-are-sphinx-search-public-servers))

###  Update sphinxapi PHP

See public read-only repository mirror for [Sphinxsearch Repository](https://code.google.com/p/sphinxsearch/)

Current stable and tested Release: **2.1**

Update command:

```bash

RELEASE='21';

lynx --dump --source https://sphinxsearch.googlecode.com/svn/branches/rel${RELEASE}/api/sphinxapi.php > src/Gpupo/Search/Sphinx/sphinxapi.php

```

----

[Contributors](https://github.com/gpupo/search/graphs/contributors)

## License

MIT, see LICENSE.

## Links

* [Search Composer Package](https://packagist.org/packages/gpupo/search) on packagist.org
* [What faceting means in Sphinx](http://sphinxsearch.com/blog/2013/06/21/faceted-search-with-sphinx/).
* [Search Patterns - A Mapmaker’s Manifesto](http://tm.durusau.net/?p=602) by Peter Moreville and Jeffrey Callender
