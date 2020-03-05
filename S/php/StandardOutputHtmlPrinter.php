<?php


final class StandardOutputHtmlPrinter implements PlainPrinter
{
    /**
     * @param string $page
     */
    public function printPage($page)
    {
        echo ("<div>" . $page . "</div>");
    }
}