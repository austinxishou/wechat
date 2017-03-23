<?php
function parseJsonSimulationClosure()
{
    return function($method, $params) {
        return [
            'api' => $params[0],
            'params' => empty($params[1]) ? null : $params[1],
        ];
    };
}
