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

        private $emptyTags = ["meta"=>true, "img"=>true, "br"=>true, "hr"=>true, "input"=>true];

        public function __construct(HtmlTemplate $bindParser=null) {
            if ($bindParser === null)
                $bindParser = new HtmlTemplate();
            $this->template = $bindParser;
            $this->directiveBag = $bindParser->getParser()->getDirectiveBag();
            $this->documentNode = new GoDocumentNode();
            $this->curNode = $this->documentNode;
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
        public function elem($def) : self {
            if (is_string($def)) {
                $paramStr = $def;
            } else if (is_array($def)) {
                $paramStr = array_shift($def);
                $arrayArgs = $def;
            } else {
                throw new \InvalidArgumentException("Invalid string or array in input: elem(" . gettype($def). ")");
            }


            $arr = explode("@", $paramStr);
            $tagName = trim (array_shift($arr));

            $attrs = [];
            $qmIndex = 0;
            foreach ($arr as $attdef) {
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



    }