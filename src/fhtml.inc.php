<?php
/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 22.08.16
 * Time: 23:17
 */


    /**
     * Create a Fluent HTML Template
     *
     * <example>
     * echo fhtml("input @name=name1 @type=text @value=?", $_POST["name1"]);
     * </example>
     *
     * @param $tag
     * @param array ...$params
     * @return \Html5\FHtml\FHtml
     */
    function fhtml($tag, ...$params) : \Html5\FHtml\FHtml {
        return (new \Html5\FHtml\FHtml())->elem($tag, ...$params);
    }