<?php
class HTML_Builder {
	private $tag;
	private $content;
	private $classes;
	private $attrs;
	private $css;

	private static $no_closing_tag = array(
		'img',
		'input',
		'br',
		'hr',
		'frame',
		'area',
		'base',
		'basefont',
		'col',
		'isindex',
		'link',
		'meta',
		'param'
	);

	public function __construct() {
		$this->tag = '';
		$this->content = '';
		$this->classes = array();
		$this->attrs = array();
		$this->css = array();
	}

	private function build_attr_string() {
		$strs = array();
		foreach ( $this->attrs as $attr => $val ) {
			$strs[] = "$attr=\"$val\"";
		}
		return implode( ' ', $strs );
	}

	private function build_css_string() {
		$strs = array();
		foreach ( $this->css as $attr => $val ) {
			$strs[] = "$attr: $val;";
		}
		return implode( ' ', $strs );
	}

	public function build() {
		$attr_string = $this->build_attr_string();
		$css_string = $this->build_css_string();
		$class_string = implode( ' ', $this->classes );

		if ( in_array( strtolower( $this->tag ), self::$no_closing_tag ) ) {
			return "<$this->tag $attr_string class=\"$class_string\" style=\"$css_string\" />";
		}

		return "<$this->tag $attr_string class=\"$class_string\" style=\"$css_string\">$this->content</$this->tag>";
	}

	public function addClass() {
		$classes = func_get_args();
		foreach ( $classes as $class ) {
			$this->classes[] = $class;
		}
		return $this;
	}

	public function content( $content ) {
		$this->content = $content;
		return $this;
	}

	public function tag( $tagName ) {
		$this->tag = $tagName;
		return $this;
	}

	public function css( $attr, $val ) {
		$this->css[$attr] = $val;
		return $this;
	}

	public function attr( $attr, $val ) {
		$this->attrs[$attr] = $val;
		return $this;
	}

	// For neat setting of attributes, like so: ->src( 'hello.png' )
	public function __call( $name, $arguments ) {
		$this->attr( $name, $arguments[0] );
		return $this;
	}

	// For easy chaining when intializing the builder, passing in the tag too
	public static function element( $tagName = null ) {
		$builder = new HTML_Builder();
		if ( isset( $tagName ) ) {
			$builder = $builder->tag( $tagName );
		}
		return $builder;
	}
}
?>