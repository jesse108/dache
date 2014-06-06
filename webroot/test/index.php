<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';

$page = new Html_Page(20, 300);
$html = $page->getHtml();

echo($html);