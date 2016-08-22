[<img src="https://travis-ci.org/dermatthes/FHtml.svg">](https://travis-ci.org/dermatthes/FHtml)


# FHtml - Fluent HTML 

**Fluent HTML Generator for PHP7** - Version **0.2** - **2016-08-23** - Written by Matthias Leuffen

Generating correct and secure HTML Code within your PHP-Code causes headache. 
Using FHtml's fluent speaking Api will save you lots of money you'd otherwise spent on aspirin.

It comes with an `@`-attribute-parser for easy and pain-less writing; you don't
need any quotes (`'` or `"`) within your quoted strings.

Example? See the Kickstart:

## Kickstart - See FHtml in action:

```php
echo fhtml("input @type=text @name=firstName @value=?", $_POST["firstName"]);
```

yes - no double quotes needed to generate valid and proper escaped xhtml: 

```
<!-- Output: -->
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

```
<!-- Output: -->
<div class="style1" style="display:block;width:100%">
    <h1>Hi There!</h1>
    <p>
        <input type="text" value="<html encoded content of $someUnescapedValue>"/>
    </p>
</div>
```
 
 
Have you ever written a `<select>`-box with preselected value? This is the FHtml-way:

```php
echo fhtml("select @name=select1")->options($optionsArr, $_POST["select1"]);
```
 
**Using an IDE (PhpStorm)?** FHtml comes with full code-completion on
all layers. Stop typing - just hit `CTRL-SPACE` and `ENTER`.
 
## Features

* Generates proper escaped XHTML-compliant HTML5 code
* Quick writing without quoted quotes
* Full IDE support (tested on Jetbrains PhpStorm)
* Makes use of PHP7 return / parameter declaration
* Autodetecing multiple APIs compatible to I18n Plugins 
* Unit-Tested and CI by TravisCI


## Install

FHtml is available as composer-package at **packagist.com**:

```
composer require html5\fhtml
```

or just add it to your `composer.json`:

```
    require: {
        "html5/fhtml": "0.2"
    }
```

_Hint: Use `composer upgrade --prefer-dist` to omit unit-tests and development files_

## Error Reports

Please report errors, wishes and feedback:
 
* Github-Page: https://github.com/dermatthes/FHtml
* Report Issues: https://github.com/dermatthes/FHtml/issues


## Usage

### @-attributes

FHtml parses a single string input on `elem()` Method (or any direct tag-method like `div()` or `span()`) and parses it.

To inject insecure or not properly escaped values from outside, use
the `?`-Placeholder construct:

**DON'T DO THAT:**
```php
$t->input("@type=text @value=$userInput"); // <- Don't do that!
```

use the auto-escaping array construct with `?` as placeholder and add the raw
values as additional parameters:

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
can jump to from anywhere by using `goto(<jumpMark>)`:

```php

(new FHtml())
->body()->as("body-elem")
    ->div()
        ->div()
->goto("body-elem")
    ->p();
```
    
The example will append the `<p>` element to the `<body>` element.


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

## Related Projects

If you like FHtml you might be interested in some of my other projects:

* **html5/template**: An angular-js / phptal implementation of inline-template components
* **html5/htmlreader**: A fast and stupid HTML5 tokenizer not using libxml. (Stupid === Good; if you've ever tried to parse HTML without having libxml manipulating your elements)  
 

## Author

Written 2016 by Matthias Leuffen [leuffen.de](http://leuffen.de)

## Licencse

MIT-License