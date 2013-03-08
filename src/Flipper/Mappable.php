<?php

namespace Flipper;

interface Mappable
{
    function map($requestedTypes, $data, $splitMapper = []);
    function mapOne($requestedTypes, $data, $splitMapper = []);
}
