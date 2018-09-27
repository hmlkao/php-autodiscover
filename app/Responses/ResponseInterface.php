<?php

namespace PhpAutodiscover\Responses;

interface ResponseInterface
{
    /**
     * Render response in XML format from template
     */
    public function render();
}
