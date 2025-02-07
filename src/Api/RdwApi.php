<?php

namespace Jdkweb\Rdw\Api;

interface RdwApi
{
    public function fetch():string|array|null;
    public function setClient():void;
}
