# Happyr BlazeBundle

[![Latest Version](https://img.shields.io/github/release/Happyr/BlazeBundle.svg?style=flat-square)](https://github.com/Happyr/BlazeBundle/releases)
[![Build Status](https://img.shields.io/travis/Happyr/BlazeBundle/master.svg?style=flat-square)](https://travis-ci.org/Happyr/BlazeBundle)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Happyr/BlazeBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/Happyr/BlazeBundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/Happyr/BlazeBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/Happyr/BlazeBundle)
[![Total Downloads](https://img.shields.io/packagist/dt/happyr/blaze-bundle.svg?style=flat-square)](https://packagist.org/packages/happyr/blaze-bundle)

This bundle lets you configure dynamic routes. A piece of code explains the benefit:

```html
// Generate the path /blog-post/{post_id}/comment/{comment_id}/edit
<a href="{{ path('edit_comment', {'comment_id':comment.id, 'post_id':comment.post.id}) }}">Click here</a>
<a href="{{ comment|blaze('edit') }}">Click here</a>
```

## Installation

### Step 1: Using Composer

```bash
$ composer require happyr/blaze-bundle
```

### Step 2: Register the bundle

 To register the bundles with your kernel:

```php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Happyr\BlazeBundle\HappyrBlazeBundle(),
    // ...
);
```

### Step 3: Configure the bundle

``` yaml
# app/config/config.yml

happyr_blaze:
  objects:
    Acme\DemoBundle\Entity\Foo:
      edit:
        route: 'foo_edit'
        parameters: {id:'getId'}
      show:
        route: 'foo_show'
        parameters: {id:'getId'}

    Acme\DemoBundle\Entity\Bar:
      show:
        route: 'bar_show'
        parameters: {id:'getId'}

    Acme\DemoBundle\Entity\Baz:
      anything:
        route: 'baz_show'
        parameters: {id:'getId', foo_id:'getFoo.getId'}

    #if you need support for routes where the objects have no relation:
    Acme\DemoBundle\Entity\FooBar:
      manage:
        route: 'foobar_manage'
        parameters: [{id: 'getId'}, {baz_id: 'getId', baz_name: 'getName'}, {bazbar_id: 'getSlug'}]
        complementaryObjects: ["Acme\DemoBundle\Entity\Baz", "Acme\DemoBundle\Entity\BazBar"]
```



## Usage

### Twig
```html
{# foo is a Foo object #}
<a href="{{ foo|blaze('show') }}">Show Foo</a>

{# baz is a Baz object #}
<a href="{{ baz|blaze('anything') }}">Show Baz</a>

{# and the multiple objects .. #}
<a href="{{ [foobar,baz,bazbar]|blaze('manage') }}">Show Baz</a>
```

### PHP

```php
use Happyr\BlazeBundle\Service\BlazeManagerInterface;

class MyController
{
    private $blaze;
    public function __construct(BlazeManagerInterface $blaze) {
        $this->blaze = $blaze;
    }

    public function SomeAction() {
        $blaze = $this->get(BlazeManagerInterface::class);

        $showUrl = $blaze->getPath($foo, 'show');
        $manageUrl = $blaze->getPath($foobar, 'show', array($baz, $bazbar));

        // ...
    }
}
```
