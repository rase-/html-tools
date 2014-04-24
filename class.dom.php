<?php
include_once dirname( __FILE__ ) . '/class.html-builder.php';

class DOM {
	public $root;

	public function __construct() {
		$this->root = null;
	}

	// Level-order tree walk
	private function find_by( $matcher ) {
		$results = array();
		$queue = array( $this->root );
		while ( ! empty( $queue ) ) {
			$node = array_shift( $queue );

			// Strategy
			if ( $matcher->matches( $node ) ) {
				$results[] = $node;
			}

			// Queue next level nodes
			foreach ( $node->children as $child ) {
				array_unshift( $queue, $child );
			}
		}

		return $results;
	}

	public function find_by_tag( $tagName ) {
		return $this->find_by( new Find_By_Tag_Strategy( $tagName ) );
	}

	public function find_by_attr( $attr, $val ) {
		return $this->find_by( new Find_By_Attribute_Strategy( $attr, $val ) );
	}

	public function HTML() {
		return $this->root->HTML();
	}

	public static function parse() {
		// TODO
		return new DOM();
	}
}

class DOM_Element {
	// Attributes
	public $tag;
	public $content;
	public $classes;
	public $attrs;
	public $css;

	// Tree links
	public $children;
	public $parent;

	public function __construct($tag = '', $attrs = array(), $classes = array(), $css = array(), $content = '' ) {
		// Attributes
		$this->tag = $tag;
		$this->attrs = $attrs;
		$this->classes = $classes;
		$this->css = $css;
		$this->content = $content;

		// Tree links
		$this->children = array();
		$this->parent = null;
	}

	public function HTML() {
		// Prepare children HTML
		$content = '';
		foreach ( $this->children as $child ) {
			$content .= $child->HTML();
		}

		$el = HTML_Builder::element( $this->tag )->content( $content );

		// Transfer attributes
		foreach( $this->attrs as $attr => $val ) {
			$el = $el->attr( $attr, $val );
		}

		foreach( $this->classes as $class ) {
			$el = $el->addClass( $class );
		}

		// Transfer all CSS
		foreach( $this->css as $attr => $val ) {
			$el = $el->css( $attr, $val );
		}

		return $el->build();
	}

	public function is_leaf() {
		return empty( $this->children );
	}
}

class Find_By_Tag_Strategy {
	private $tagName;

	public function __construct( $tagName ) {
		$this->tagName = $tagName;
	}

	public function matches( $node ) {
		return $this->tagName == $node->tag;
	}
}

class Find_By_Attribute_Strategy {
	private $attr;
	private $val;

	public function __construct( $attr, $val ) {
		$this->attr = $attr;
		$this->val = $val;
	}

	public function matches( $node ) {
		return isset( $node->attrs[$this->attr] ) && $this->val == $node->attrs[$this->attr];
	}
}

$dom = new DOM();
$el = new DOM_Element( 'div', array( 'data-attr', 'true' ) );
$dom->root = $el;
$el->children[] = new DOM_Element( 'p', array( 'data-attr' => 'true' ), array(), array(), 'here' );
$el->children[] = new DOM_Element( 'p', array( 'data-attr' => 'true' ), array(), array(), 'is some' );
$el->children[] = new DOM_Element( 'p', array( 'data-attr' => 'true' ), array(), array(), 'text' );

echo $dom->HTML();
?>
