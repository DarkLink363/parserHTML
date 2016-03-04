require('parser.php'); //include the library to file
try{ //try parse html for catch the errors of parsing or bad sentece
	$html = new ParserHTML('/one.html'); //prepare HTML document to parse
	$html->vars_ADD('new','This is parsed text'); //add variable
	$html->parse(); //parse document
	$html->show(); //show document
}catch(Exception $e){
	echo $e->getMessage(); //show error if exist
}
