<?php

function wpt_fake_data($arg, $dev) {
    if(!$dev) return [];
    return [
        "wpt_investment" => [
            [
                "usuario" => 1,
                "fecha" => "3-9-2021",
                "monto" => 50
            ],
            [
                "usuario" => 2,
                "fecha" => "10-9-2021",
                "monto" => 300
            ],
            [
                "usuario" => 1,
                "fecha" => "1-11-2021",
                "monto" => 100
            ]
        ],
    ][$arg];
}