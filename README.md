[<img src="https://travis-ci.org/dermatthes/FHtml.svg">](https://travis-ci.org/dermatthes/FHtml)


# FHtml - Fluent HTML5 Generator

Fluent HTML Generator for PHP7 - Version 0.2 - 2016-08-23

Generating correct and secure HTML Code within your Code-Blocks is
always a headache. FHtml helps with a fluent speaking Api for
generating clean XHTML-compliant code.

It comes with an `@`-attribute-parser for easy and pain-less writing; you don't
need any quotes (`'` or `"`) within your quoted strings.

Example? See the Kickstart:

## Kickstart

```php
echo fhtml("input @type=text @name=firstName @value=?", $_POST["firstName"]);
```

yes - no double quotes needed to generate valid and proper escaped xhtml: 

```html
<input type="text" name="firstName" value="Some Value"/>
```

Use the fluent-API to generate structures:

```php
echo fhtml("div @class=style1 @style=display:block;width:100%")
        ->h1()->text("Hi there!")->end()
        ->p()
            ->input("@type=text @value=?", $someUnescapedValue);
```

yeah: Your PHP-code is shorter! And proper indention is done as well:

```html
<div class="style1" style="display:block;width:100%">
    <h1>Hi There!</h1>
    <p>
        <input type="text" value="<html encoded content of $someUnescapedValue>"/>
    </p>
</div>
```
 
**Using an IDE like PhpStorm?** FHtml will come with full code-completion on
all layers. Stop typing - just hit `CTRL-SPACE` -> `ENTER`.
 
## Features

* Will generate proper escaped XHTML-compliant HTML5 code
* Quick writing
* Full IDE support (tested on Jetbrains PhpStorm)
* Makes use of PHP7 return / parameter declaration
* Autodetecing multiple APIs compatible to I18n Plugins 

## Related Projects

If you like FHtml you might be interested in my other Projects:

* **html5/template**: An angular-js / phptal implementation of inline-template components
* **html5/htmlreader**: A fast and stupid HTML5 tokenizer not using libxml. (Stupid === Good; if you've ever tried to parse Html without libxml manipulating your elements.)  
 
## Install

FHtml is available at packagist.com:

```
composer require html5\fhtml
```

## Error Reports

Please report errors, wishes and feedback to my github page:
 
 https://github.com/dermatthes/FHtml


## Usage


### Quick writing for Attributes

FHtml parses a single string input on `elem()` Method (or any direct tag-method like `div()` or `span()`) and parses it.

To inject insecure or not properly escaped values from outside, use
the Array construct:

**DON'T DO THAT:**
```php
$t->input("@type=text @value=$userInput"); // <- Don't do that!
```

use the auto-Escaping array construct with `?` as placeholder:

```php
$t->input("@value=?", $userInput); // <-- Right way: Will escape the value
```


### Inserting a text-node

To insert plain text to an element use the `text($text)` - method. 
The text in Parameter 1 will be html-escaped.

```php
echo fhtml("span")->text("Some text here!");
```

Will generate:

```html
<span>Some text here!</span>
```

### End an element

You can use the `end()` Method to jump back to the parent element.

### Jump Marks

You can use the `as(<jumpMark>)` Method to define a element you
can jump to anytype by using `goto(<jumpMark>)`:

```php

(new FHtml())
->body()->as("body-elem")
    ->div()
        ->div()
->goto("body-elem")
    ->p();
```
    
Will will append the `<p>` element to the `<body>` element.


### Generating `<select>` Options from arrays

Use the `options(array $options, string $preselect)` - method to create select-options with preselected
Value on the fly:

Imagine you have some mapping of key-values array for your select-field:
```php
$options = [
    "foo" => "Foo Apples",
    "bar" => "Bar Bananas"
    "baz" => "Batzen for Cats"
];
```

Just set them as first parameter of `options()` and the submitted Value
from Post into the second parameter:

```php
echo fhtml("select @name=sel1")->options($options, $_POST["sel1"]);
```

will generate:

```html
<select name="sel1">
    <option value="foo">Foo Apples</option>
    <option value="bar" selected="selected">Bar Bananas</option>
    <option value="baz">Batzen for Cats</option>
</select>
```

### API Alternatives

These Samples are doing exact the same. Choose the api you like most:

```php
echo fhtml("input @type=text @name=name1 @value=?", $_POST["name1"]);
echo fhtml()->input("@type=text @name=name1 @value=?, $_POST["name1"]);
echo fhtml()->input(["@type=text @name=name1 @value=?, $_POST["name1"]]);

echo fhtml()->elem("input @type=text @name=name1 @value=?, $_POST["name1"]);
echo fhtml()->elem(["input @type=text @name=name1 @value=?, $_POST["name1"]]);
```

I prefer using the shortest:

```php
echo fthml()->input("@type=text @name=name1 @value=?", $_POST["name1"]);
```
## Author

Written 2016 by Matthias Leuffen http://leuffen.de