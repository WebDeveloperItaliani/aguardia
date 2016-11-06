# A'Guardia

Il tool di controllo dei post per il gruppo "Web Developer Italiani".

![](our_great_lord.jpg)

## Funzionamento

Questo bot è stato creato per agevolare, un minimo, il lavoro degli admin del gruppo. Uno dei problemi principali è l'assenza degli hashtag all'inizio dei post. Precisamente, A'Guardia si occupa di:

* cercare eventuali post non conformi, e segnarseli sul file `posts.json`;
* cancellarli se dopo un certo periodo (15 minuti) non sono stati corretti;

## Installazione

``` bash
$ git clone https://github.com/francescomalatesta/aguardia
$ cd aguardia
$ ./install
```

## Uso

``` bash
php aguardia process-latest     # prende creati/modificati nell'ultimo quarto d'ora, lasciando il commento se non validi
php aguardia process-reported   # controlla i post commentati precedentemente, li cancella se non validi
```

## Testing

``` bash
$ vendor/bin/phpunit
```

## License

La licenza usata è la (MIT). Maggiori informazioni (in inglese) [nel file di Licenza](LICENSE.md).
