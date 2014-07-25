# Petfinder

PHP Sphinx faceted search with oriented object results based


### Faceted Navigation

Os refinadores são baseados em propriedades gerenciadas do índice de pesquisa.
Para usar propriedades gerenciadas como refinadores, elas devem ser ativadas como refinadores.
Navegação facetada é o processo de procurar o conteúdo filtrando em refinadores vinculados às páginas de categoria.
Ela permite especificar refinadores diferentes para as páginas de categoria, mesmo quando a página subjacente que exibe as categorias é a mesma.


![Image](http://img42.com/jX1y9+)

### What faceting means in Sphinx

Faceting  (faceted search) is a common technique used for exploring information which has become popular among online retailers and libraries. For this reason faceted search has become a must-have for any search solution.

In essence, a facet is a list  of unique values for a given characteristic or attribute (in general, accompanied by a count of each value). Translated to Sphinx, it’s a grouping of an attribute for a certain query. For this reason, Sphinx doesn’t require any changes or setup for an index to perform a faceted search. It’s all done with querying. To sum it up, for a faceted search, several queries are made, which look almost the same, except that each does a grouping on the desired attribute to be faceted.

To help with this, Sphinx has a feature called multi-query optimization. If several searches are fired in a row and the search filters are not changed, the fulltext match results are kept in memory, resulting in a single search operation made at the first fired query. The rest of the queries will use the cached result to perform grouping or sorting operations.

However, in the current version of Sphinx there are several caveats: the optimization doesn’t work for string attributes and select expressions. That is, it only works for int/bigint/float/multi-value attributes and adding a new query that groups on a string attribute will disable the whole optimization. While this will not influence the returned result,  it will increase overall execution time. To make use of this optimization it is best to fire a multi-query that has only numeric facets and then run separate queries with string attributes and select expressions.

In case the facet counts is not desired, there is another optimization that can be applied – the cutoff option – which has been described in an earlier post.


### Simple Query Usage

```php

<?php

use Gpupo\Petfinder\Search\Search;
use Gpupo\Petfinder\Query\Keywords;
use Gpupo\Petfinder\Query\Query;

$keywords = new Keywords;
$keywords->addKeyword('magic');
$keywords->addKeyword('unicorn');
$query = new Query($keywords);
$query->setIndex('fantasyIndex');

$totalItens =  Search::getInstance()->findByQuery($query)->getTotal();

```

## Install

The recommended way to install is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "gpupo/similarity": "1.*"
    }
}
```

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
