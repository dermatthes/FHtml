<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 18.08.16
 * Time: 18:14
 */

namespace Html5\Test;


use Html5\FHtml\FHtml;

class FhtmlTest extends \PHPUnit_Framework_TestCase
{


    public function testCorrectEscaping() {
        $ret = (new FHtml())->elem(["input @type=text @value=?", "some \" unescaped@text"])->render();
        self::assertEquals('<input type="text" value="some &quot; unescaped@text"/>', trim ($ret));
    }


}
