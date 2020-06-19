<?php

namespace App\View;

interface ViewInterface
{
    public function getContent(array $params = []);
}
