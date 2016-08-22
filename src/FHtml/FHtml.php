<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 18.08.16
     * Time: 11:44
     */

    namespace Html5\FHtml;



    use Html5\Template\Directive\GoDirectiveExecBag;
    use Html5\Template\HtmlTemplate;
    use Html5\Template\Node\GoDocumentNode;
    use Html5\Template\Node\GoElementNode;
    use Html5\Template\Node\GoTextNode;

    /**
     * Class FHtml
     * @package Html5\FHtml
     *
     * @method $this div($attrs, ...$params)
     * @method $this input($attrs, ...$params)
     * @method $this p($attrs, ...$params)
     * @method $this b($attrs, ...$params)
     * @method $this h1($attrs, ...$params)
     * @method h2 $this
     * @method h3 $this
     *
     */
    class FHtml {

        /**
         * @var HtmlTemplate
         */
        private $template;
        private $directiveBag;
        private $documentNode;

        /**
         * @var GoElementNode
         */
        private $curNode;
        private $jumpMarks = [];

        private $emptyTags = ["meta"=>true, "img"=>true, "br"=>true, "hr"=>true, "input"=>true, "link"=>true];

        public function __construct(HtmlTemplate $bindParser=null) {
            if ($bindParser === null)
                $bindParser = new HtmlTemplate();
            $this->template = $bindParser;
            $this->directiveBag = $bindParser->getParser()->getDirectiveBag();
            $this->documentNode = new GoDocumentNode();
            $this->curNode = $this->documentNode;
        }





        public function __call($name, $arguments) : self
        {
            if (count ($arguments) === 0) {
                // Variant: ->div()
                return $this->elem("{$name}");
            } else if (is_array($arguments[0])) {
                // Variant: ->div(["@class=?", $className])
                $args = $arguments[0];
                $attrs = array_shift($args);
                return $this->elem("{$name} {$attrs}", ...$args);
            } else {
                // Variant: ->div("@class=?", $classname)
                $attrs = array_shift($arguments);
                return $this->elem("{$name} {$attrs}", ...$arguments);
            }
        }


        /**
         * Define the sub-Element of the current node
         *
         * Example
         *
         * $e->elem("div @class = a b c @name = some Name")
         *
         * @param $def
         * @return FHtml
         */
        public function elem($elemDef, ...$params) : self {
            if (is_string($elemDef)) {
                $arrayArgs = $params;
            } else if (is_array($elemDef)) {
                $elemDefArr = $elemDef;
                $elemDef = array_shift($elemDefArr);
                $arrayArgs = $elemDefArr;
            } else {
                throw new \InvalidArgumentException("Invalid string or array in input: elem(" . gettype($elemDef). ")");
            }


            $arr = explode("@", $elemDef);
            $tagName = trim(strtolower(array_shift($arr)));


            $attrs = [];
            $qmIndex = 0;
            foreach ($arr as $attdef) {
                if ($attdef === "")
                    continue;
                list ($key, $val) = explode("=", $attdef, 2);
                if ( ! isset ($val)) {
                    $attrs[trim ($key)] = null;
                    continue;
                }

                $val = trim ($val);
                if (isset ($arrayArgs)) {
                    if ($val == "?" && isset ($arrayArgs[$qmIndex])) {
                        $val = $arrayArgs[$qmIndex];
                        $qmIndex++;
                    }
                }
                $attrs[trim($key)] = $val;
            }




            $newNode = new GoElementNode();
            if (isset ($this->emptyTags[$tagName]))
                $newNode->isEmptyElement = true;
            $newNode->name = $tagName;
            $newNode->attributes = $attrs;
            $this->curNode->childs[] = $newNode;
            $newNode->parent = $this->curNode;

            if ($newNode->parent instanceof GoDocumentNode) {
                $newNode->preWhiteSpace = "\n";
            } else {
                $newNode->preWhiteSpace = $newNode->parent->preWhiteSpace . "    ";
            }

            return $this->cloneit($newNode);
        }

        private function cloneit ($curNode) : FHtml {
            $new = new self();
            $new->jumpMarks =& $this->jumpMarks;
            $new->curNode = $curNode;
            $new->documentNode = $this->documentNode;
            $new->directiveBag = $this->directiveBag;
            $new->template = $this->template;
            return $new;
        }


        /**
         * Generate <select> options on the Fly
         *
         * <example>
         * fhtml("select @name=country1")->options(["at"=>"Austria", "de" => "Germany", "us" => "USA"], $_POST["country1"]);
         * </example>
         *
         * @param array $options
         * @param string|null $selected
         * @return FHtml
         */
        public function options(array $options, string $selected=null) : self {
            foreach ($options as $key => $value) {
                if ($selected == $key)
                    $this->elem("option @value=? @selected=selected", $key)->text($value)->end();
                else
                    $this->elem("option @value=?", $key);
            }
            return $this;
        }


        public function end() : self {
            if ( ! isset ($this->curNode->parent))
                throw new \InvalidArgumentException("end(): Node has no parent.");
            return $this->cloneit($this->curNode->parent);
        }

        public function as($name) : self {
            $this->jumpMarks[$name] = $this->curNode;
            return $this;
        }

        public function goto($name) : self {
            if ( ! isset($this->jumpMarks[$name]))
                throw new \InvalidArgumentException("goto($name) undefined.");
           $this->jumpMarks[$name];
            return $this->cloneit($this->jumpMarks[$name]);
        }

        public function text($content) : self {
            $this->curNode->childs[] = new GoTextNode($content);
            return $this;
        }


        public function root() : self {
            return $this->cloneit($this->documentNode);
        }

        public function getDocument() : GoDocumentNode {
            return $this->documentNode;
        }

        public function render(array $data = []) : string {
            return $this->documentNode->run($data, $this->template->getExecBag());
        }

        public function __toString()
        {
            return $this->render();
        }

    }