<?php


final class StandardOutputPlainPrinter implements PlainPrinter
{
    /**
     * @param string $page
     */
    public function printPage($page)
    {
        echo $page;
    }
}