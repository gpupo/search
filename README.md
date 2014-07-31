# Petfinder

PHP Sphinx faceted search with oriented object results based

### Faceted Navigation

Os refinadores são baseados em propriedades gerenciadas do índice de pesquisa.
Para usar propriedades gerenciadas como refinadores, elas devem ser ativadas como refinadores.
Navegação facetada é o processo de procurar o conteúdo filtrando em refinadores vinculados às páginas de categoria.
Ela permite especificar refinadores diferentes para as páginas de categoria, mesmo quando a página subjacente que exibe as categorias é a mesma.

[[/Resources/doc/search_query.png]]

### What faceting means in Sphinx

Faceting  (faceted search) is a common technique used for exploring information which has become popular among online retailers and libraries. For this reason faceted search has become a must-have for any search solution.

In essence, a facet is a list  of unique values for a given characteristic or attribute (in general, accompanied by a count of each value). Translated to Sphinx, it’s a grouping of an attribute for a certain query. For this reason, Sphinx doesn’t require any changes or setup for an index to perform a faceted search. It’s all done with querying. To sum it up, for a faceted search, several queries are made, which look almost the same, except that each does a grouping on the desired attribute to be faceted.

To help with this, Sphinx has a feature called multi-query optimization. If several searches are fired in a row and the search filters are not changed, the fulltext match results are kept in memory, resulting in a single search operation made at the first fired query. The rest of the queries will use the cached result to perform grouping or sorting operations.

However, in the current version of Sphinx there are several caveats: the optimization doesn’t work for string attributes and select expressions. That is, it only works for int/bigint/float/multi-value attributes and adding a new query that groups on a string attribute will disable the whole optimization. While this will not influence the returned result,  it will increase overall execution time. To make use of this optimization it is best to fire a multi-query that has only numeric facets and then run separate queries with string attributes and select expressions.

In case the facet counts is not desired, there is another optimization that can be applied – the cutoff option – which has been described in an earlier post.


### Simple Query Usage


```PHP

<?php
use Gpupo\Petfinder\Search\Search;
use Gpupo\Petfinder\Query\Keywords;
use Gpupo\Petfinder\Query\Query;

$keywords = new Keywords;
$keywords->addKeyword('magic');
$keywords->addKeyword('unicorn');
$query = new Query($keywords);
$query->setIndex('fantasyIndex');

//Configure Sphinx Server Parameters:
SphinxService::getInstance()->setParameters(array(
	'host'    => 'foo.bar.com', //default value is localhost
	'port'    => '9313', //default value
    'timeout' => 5, //default value
));

$results = Search::getInstance()->findByQuery($query);

$results->getTotal(); // Itens found

```

## Install

The recommended way to install is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "gpupo/petfinder": "1.*"
    }
}
```

# Dev


	composer install --dev;
	cp phpunit.xml.dist phpunit.xml;


Customize Sphinx Search Server parameters in ``phpunit.xml``:

    <php>
        <const name="SPHINX_HOST" value="localhost"/>
        <const name="SPHINX_PORT" value="9313"/>
        <const name="SPHINX_TIMEOUT" value="5"/>
    </php>



## Tests results

<!-- output of this command:
         phpunit --testdox | sed "s/.*\[/-&/" | sed 's/.*Gpupo.*/&\'$'\n/g'
-->

Gpupo\Tests\Petfinder\Search\FacetedSearch

- [x] Multi queries
- [x] Group by	
- [x] Simplifica multiplas queries groupby
- [x] Multiquery com groupby

Gpupo\Tests\Petfinder\Search\Paginator\Paginator

- [x] Resultados possui objeto modelado para paginacao
- [x] Processa result collection
- [x] Marca a pagina atual
- [x] Manipula offset de query
- [x] Manipula limit de query
- [x] Divide resultados em paginas de acordo com limite
- [x] Acesso ao range de paginas aproximadas
- [x] Permite customizacao do range de paginas para navegacao
- [x] Acesso a valores da paginacao

Gpupo\Tests\Petfinder\Search\Query\Filters

- [x] Filtra por lista de valores de uma chave
- [x] Filtra por range de valores de uma chave
- [x] Adiciona um valor a values filters existente

Gpupo\Tests\Petfinder\Search\Query\Keywords

- [x] Processa palavras chave a partir de string
- [x] Sucesso com palavras chaves validas
- [x] Valida string de palavras chave vazias ou menor que o permitido
- [x] Sucesso ao pesquisar com frases

Gpupo\Tests\Petfinder\Search\Query\Query

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

Gpupo\Tests\Petfinder\Search\Result\Collection

- [x] Resultados com propriedades processadas

Gpupo\Tests\Petfinder\Search\Search

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

Gpupo\Tests\Petfinder\Sphinx\SphinxService

- [x] Permite acesso aos parametros default
- [x] Permite definicao de parametros personalizados
- [x] Disponibiliza acesso ao client
- [x] Acesso singleton ao client com reset


## Todo

* Translate items written originally in Brazilian portuguese;

###  Update sphinxapi PHP

 	lynx --dump --source https://sphinxsearch.googlecode.com/svn/trunk/api/sphinxapi.php > \
 	src/Gpupo/Petfinder/Sphinx/sphinxapi.php
 	

## Search Patterns - A Mapmaker’s Manifesto

by Peter Moreville and Jeffrey Callender

* Search is a problem too big to ignore.
* Browsing doesn’t scale, even on an IPhone.
* Size matters. Linear growth compels a step change in design.
* Simple, fast, and relevant are table stakes.
* One size won’t fit all. Search must adapt to context.
* Search in iterative, social, and multisensory.
* Increments aren’t enough. Even Google must innovate or die.
* It’s not just about findability. It’s not just about the Web.
* The challenge is radically multidisciplinary.
* We must engage engineers and executives in design.
* We can learn from the past. Library science is still relevant.
* We can learn from behavior. Interaction design affords actionable results.
* We can learn from one user. Analytics is enriched by ethnography.
* Some patterns, we should study and reuse.
* Some patterns, we should break like a bad habit.
* Search is a complex adaptive system.
* Emergence, cocreation, and self-organization are in play.
* To discover the seeds of change, go outside.
* In science, fiction, and search, the map invents the territory.
* The future isn’t just unwritten—it’s unsearched.
