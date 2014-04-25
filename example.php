<?php
include_once dirname( __FILE__ ) . '/class.html-builder.php';

$str =
	HTML_Builder::element( 'a' )
		->href( 'http://link.com' )
		->content( 'this is some text' )
		->content( HTML_Builder::element()->tag( 'img' )
					->src( 'hello.png' )
					->addClass( 'test', 'klz' )
					->css( 'height', '120px' )
					->css( 'width', '200px' )
					->data( 'test', 'value' )
		)
		->build();
echo $str;
// => <a href="http://link.com" class="" style="">some text<img src="hello.png" class="test klz" style="height: 120px; width: 200px;" /></a>
?>
