<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 18.08.16
 * Time: 18:14
 */

namespace Html5\FHtml\Test;


use Html5\FHtml\FHtml;

class FhtmlTest extends \PHPUnit_Framework_TestCase
{




    public function testDifferenVariantsReturnTheSame () {

        $ret = fhtml("input @type=text @value=?", "some")->render();
        self::assertEquals('<input type="text" value="some"/>', trim ($ret));

        $ret = (new FHtml())->input("@type=text @value=?", "some")->render();
        self::assertEquals('<input type="text" value="some"/>', trim ($ret));

        $ret = (new FHtml())->input(["@type=text @value=?", "some"])->render();
        self::assertEquals('<input type="text" value="some"/>', trim ($ret));

        $ret = (new FHtml())->elem(["input @type=text @value=?", "some"])->render();
        self::assertEquals('<input type="text" value="some"/>', trim ($ret));

        $ret = (new FHtml())->elem("input @type=text @value=?", "some")->render();
        self::assertEquals('<input type="text" value="some"/>', trim ($ret));
    }


    public function testSelectOptions() {

        $ret = fhtml("select @name=sel1")->options(["key1"=>"val1", "key2"=>"val2"], "key1");
        self::assertEquals('<select name="sel1">
    <option value="key1" selected="selected">val1</option>
    <option value="key2"></option></select>', trim ($ret));

    }


    public function testCorrectEscaping() {
        $ret = (new FHtml())->elem(["input @type=text @value=?", "some \" unescaped@text"])->render();
        self::assertEquals('<input type="text" value="some &quot; unescaped@text"/>', trim ($ret));
    }


}
