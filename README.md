Symfony Levenify Bundle
====================
A better Levenshtein function in your Symfony project

How to install Levenify
----------
* Step 1 : Install package
```bash
composer req levenify/levenify-bundle
```

* Step 2 : Setup your database with command
``` bash
bin/console levenify:install
```

* Step 3 : Setup your config.yml
``` yml
# app/config/config.yml
doctrine:
    orm:
        dql:
            numeric_functions:
                levenshtein: Levenify\LevenifyBundle\ORM\Doctrine\DQL\Levenshtein
                levenshtein_ratio: Levenify\LevenifyBundle\ORM\Doctrine\DQL\LevenshteinRatio
```
## How to use Levenify
* With Query Builder
``` php
<?php
    public function getProductByName($searchString, $tolerance = 3) {
        $queryBuilder = $this->_em->createQueryBuilder()
           ->select('p')
           ->from('Product::class', 'p')
           ->where('LEVENSHTEIN(p.name, :searchString) <= :tolerance')
           ->setParameter('searchString', $searchString)
           ->setParameter('tolerance', $tolerance)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
?>
```
* With DQL
``` php
<?php
    public function getProductByName($searchString, $tolerance = 3) {

        $dqlString = '
            SELECT product
            FROM Product::class p
            WHERE LEVENSHTEIN(p.name, :searchString) <= :tolerance
        ';

        $query = $this->_em->createQuery($dqlString)
           ->setParameter('searchString', $searchString)
           ->setParameter('tolerance', $tolerance)
        ;

        return $query->getResult();
    }
?>
```
--- **Q:** What is the difference between basic Levenshtein function and Levenify ?

--- **A:** First parameter of the function can be composed of multiple words !
