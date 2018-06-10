<?php

// Single Responsibility Principle Violation
class Report
{
    public function getTitle()
    {
        return 'Report Title';
    }
    public function getDate()
    {
        return '2016-04-21';
    }
    public function getContents()
    {
        return [
            'title' => $this->getTitle(),
            'date' => $this->getDate(),
        ];
    }
    public function formatJson()
    {
        return json_encode($this->getContents());
    }
}

