# FHtml

Fluent HTML Generator for PHP7

Generating correct and secure HTML Code within your Code-Blocks is
sometimes an headache. FHtml helps with a fluent speaking Api for
generating HTML Code.

It comes with an attribute-parser for easy code-writing.

## Introducing Example

```php
$html = Fhtml()->elem("div @class=style1 @style=display:block;width:100%")
            ->elem("h1")->text("Hi there!")->end()
            ->elem("p")
                ->elem(["input @type=text @value=?", $someUnescapedValue])->end()
            ->end()
       ->render();
```

Will generate proper encoded Html-Code. 

```html
<div class="style1" style="display:block;width:100%">
    <h1>Hi There!</h1>
    <p>
        <input type="text" value="<html encoded content of $someUnescapedValue>"/>
    </p>
</div>
```
 
 
## Install

```
composer require html5\fhtml
```


## Usage

### Quick writing for Attributes

FHtml parses a single string input on `elem()` Method and parses it.

To inject insecure or not properly escaped values from outside, use
the Array construct:

**DON'T DO THAT:**
```php
$t->elem("input @value=$userInput"); // <- Don't do that!
```

use the auto-Escaping array construct with `?` as placeholder:

```php
$t->elem(["input @value=?", $userInput]); // <-- Right way: Will escape the value
```

### End an element

You can use the `end()` Method to jump back to the parent element.

### Jump Marks

You can use the `as(<jumpMark>)` Method to define a element you
can jump to anytype by using `goto(<jumpMark>)`:

```php

(new FHtml())
->elem("body")->as("body-elem")
    ->elem("div")
        ->elem("div")
->goto("body-elem")
    ->elem("p");
```
    
Will will append the `<p>` element to the `<body>` element.


## Author

Written 2016 by Matthias Leuffen http://leuffen.de