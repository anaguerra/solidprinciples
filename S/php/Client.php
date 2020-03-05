<?php


final class Client
{
    public function __construct()
    {
        $book = new Book();
        $currentPage = $book->getCurrentPage();
        $printer = new StandardOutputPlainPrinter();
        $printer->printPage($currentPage);
    }
}