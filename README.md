# PHP meta tags generator

Librairie permettant la génération des balises meta pour le \<head> en PHP. La librairie inclut les metas pour :
[Open Graph](https://ogp.me/), [Twitter Cards](https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/getting-started) et [Json-LD](https://json-ld.org/). 

### Installation 

```
composer require dbout/meta-tags
```

### Utilisation 

Créez une nouvelle instance `Dbout\MetaTags\MetaTags` puis ajoutez les metas :

```php
use Dbout\MetaTags\MetaTags;

$tags = new MetaTags();

$tags->title('My Awesome site')
    ->meta('keywords', 'card, twitter')
    ->charset('UTF-8')
    ->twitter('card', 'summary')
    ->twitter('site', '@dimbouteille')
    ->geo('title', 'the Title')
    ->stylesheet('https://fonts.googleapis.com/css?family=Muli:300,400,600,700,800,900')
    ->meta('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no')
    ->link('dns-prefetch', '//fonts.googleapis.com/')
    ->meta([
        'http-equiv' => 'X-UA-Compatible',
        'content' => 'IE=edge'
    ])
    ->jsonLd([
        "@context" => "https://schema.org/",
        "@type" => "Product",
        "name" => "Executive Anvil",
    ])
    ->style('body {background: red;}')
    ->script('alert("Hello world"); ');
```

Pour générer les metas, ajoutez cette ligne à l'intérieur de la balise `<head>` :

```php
<?= $tags->render(); ?>
```

L'exemple ci-dessus générera le code suivant :

```html
<title>My Awesome site</title>
<meta name="keywords" content="card, twitter">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@dimbouteille">
<meta name="geo.title" content="the Title">
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Muli:300,400,600,700,800,900">
<link rel="dns-prefetch" href="//fonts.googleapis.com/">
<style type="text/css">body {background: red;}</style>
<script>alert("Hello world"); </script>
<script type="application/ld+json">{
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "Executive Anvil"
}</script>
```