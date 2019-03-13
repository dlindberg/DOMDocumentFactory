# DOCFactory

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

The DOMDocument extension in PHP is very powerful and incredibly useful for manipulating HTML. However, there is just enough boilerplate code that having a little utility factory makes things just a little bit easier. I also find that what I frequently want to do is use HTMLPurifier to clean up some crufty HTML input, turn that into a DOMNode, manipulate it and then convert that back into a string. This is a simple factory to help with that workflow. It takes a string containing a fragment of HTML, purifies it, and turns it into a `<body>` DOMNode. There is also a very little bit of boiler plate in getting that string back out, so this can handle that too—optionally with different pass of HTMLPurifier on the way out (not frequently necessary, but occasionally helpful).

 This factory is setup so that you can simply initialize and invoke it and get back a DOMNode. Manipulate the DOM however you need to and then stringify your DOMNode back out. Of course defaults are good, but flexibility is important. So you can inject a DOCFactoryConfig to adjust the settings as needed—useful if you would need to implement a factory anyway because of your use case.

## Install

Via Composer

``` bash
$ composer require dlindberg/DOMDocumentFactory
```

## Usage

### Basic Invocation

If all you really need to do is take a string of html and get a quickly usable DOMNode out of it, you can use the simply create an instance of the DOMDocumentFactory class and invoke it.

``` php
$html = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>'; // Etc. Etc.

$docFactory = new dlindberg\DOMDocumentFactory();

$DOMNode = $docfactory($html);

/* Do something with your DOMNodes */

echo $docFactory->stringify($DOMNode->firstChild);
```

For an input of `<p>This is some Text</p>` if you made not further changes to your DOMNodes, the result would also be `<p>this is some text</p>`

Alternatively, there are two additional methods for invoking the factory. `getNode(string $blob)` and `getDocument(string $blob)`. The `getNode` method does the same thing that invoking the class does, and returns the `body` from the fragment. Using `getDocument` will return the entire `DOMDocument` class. Note that you can always get to the parent DOMDocument even when using the `getNode` method by using DOMDocument's [`ownerDocument`](https://secure.php.net/manual/en/class.domnode.php#domnode.props.ownerdocument) method.

If you have a section of HTML that has multiple immediate child nodes, for example:

```html
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
<p>In vel nibh eget turpis sagittis posuere ut vitae purus.<p>
<p>Donec in libero mauris. Aenean eu consectetur tortor.</p>
<p>Sed dolor neque, maximus et est eu, ultricies interdum libero.</p>
<p>Cras sed feugiat ante. Suspendisse ultrices eros at arcu feugiat dictum.</p>
```

Simply using:

```php
$DOMElement = $docfactory($html);
echo $docfactory->stringify($DOMElement->firstChild);
```

would result in:

```html
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
```

And using:

```php 
echo $docfactory->stringify($DOMElement);
```

results in:

```html
<body>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
<p>In vel nibh eget turpis sagittis posuere ut vitae purus.<p>
<p>Donec in libero mauris. Aenean eu consectetur tortor.</p>
<p>Sed dolor neque, maximus et est eu, ultricies interdum libero.</p>
<p>Cras sed feugiat ante. Suspendisse ultrices eros at arcu feugiat dictum.</p>
</body>
```

To get the same thing out that you put in you can use the `stringifyFromList` method. This returns an array of strings from each child node in a `NodeList`. If you need them as an array, you can simply use it as is. Or you can flatten the array using `implode`.

```php
echo \implode(\PHP_EOL, $docfactory->stringifyFromList($DOMElement));
```

### Custom Invocation

Sometimes you want to do something a little more complex, so the DOMDocumentFactory class constructor can take an instance DOMDocumentFactoryConfig class as its sole argument.

To create an instance of DOMDocumentFactoryConfig:

```php
$DOMDocumentFactoryConfig = new DOMDocumentFactoryConfig(array $settings = [], \HTMLPurifier $inputPurifier = null, \HTMLPurifier $outputPurifier = null);
```

If you do not pass an instance of [HTMLPurifier](http://htmlpurifier.org) as `$inputPurifier` your settings for the HTMLPurifier will be used instead of a default HTMLPurifier object. By default no output purification is preformed. Should you want to purify the output an additional HTMLPurifier may be passed as `$outputPurifier`.

The configuration can also be modified after creating it:

```php
$DOMDocumentFactoryConfig->setInputPurifier(\HTMLPurifier $purifier);
$DOMDocumentFactoryConfig->setOutputPurifier(\HTMLPurifier $purifier);
$DOMDocumentFactoryConfig->version = '1.0';
```

The `$settings` array defaults to:

```php
$settings = [
    'version'             => '1.0',
    'encoding'            => 'UTF-8',
    'recover'             => true,
    'preserveWhiteSpace'  => false,
    'formatOutput'        => true,
    'DOMOptions'          => LIBXML_NOERROR | LIBXML_NOWARNING,
];
```

### As a Static Function

You can also use this factory as a one off static function. You can provide an optional `$DOMDocumentFactoryConfig` when you do so. Internally the static methods spin up an instance of the DOMDocumentFactory class to do their work, so this method of use is mostly just a shortcut to actually integrating the factory into a project.

```php
$node = DOMDocumentFactory::getDomNode(string $blob, DOMDocumentFactoryConfig $config = null);

$string = DOMDocumentFactory::stringifyNode(\DOMNode $node, DOMDocumentFactoryConfig $config = null);

$array = DOMDocumentFactory::stringifyNodeList(\DOMNodeList $nodes, DOMDocumentFactoryConfig $config = null);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

The current tests are fairly basic; tests that more effectively attack possible edge cases or unexpected / unpredictable behaviors would be helpful.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email dane@lindberg.xyz instead of using the issue tracker.

## Credits

- [Dane Lindberg][link-author]
- [All Contributors][link-contributors]

The boiler plate for this project is based on [ The League of Extraordinary Packages'](http://thephpleague.com) [Skeleton](https://github.com/thephpleague/skeleton) package repository.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/dlindberg/dom-document-factory.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/dlindberg/DOMDocumentFactory/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/dlindberg/DOMDocumentFactory.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/dlindberg/DOMDocumentFactory.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dlindberg/dom-document-factory.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/dlindberg/dom-document-factory
[link-travis]: https://travis-ci.org/dlindberg/DOMDocumentFactory
[link-scrutinizer]: https://scrutinizer-ci.com/g/dlindberg/DOMDocumentFactory/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/dlindberg/DOMDocumentFactory
[link-downloads]: https://packagist.org/packages/dlindberg/dom-document-factory
[link-author]: https://github.com/dlindberg
[link-contributors]: ../../contributors
