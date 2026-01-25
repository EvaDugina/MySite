<?php

if (isset($_POST["flag-getStatistic"]) && $_POST["flag-getStatistic"]) {

    $statistic_json = json_decode(file_get_contents("./01Rfk6m6SUDXSD.json"), true);

    echo json_encode(array(
        "spittle" => $statistic_json["spittle"],
        "kiss" => $statistic_json["kiss"]
        )
    );

    exit;
}

if (isset($_POST["flag-updateStatistic"]) && $_POST["flag-updateStatistic"]) {

    $statistic_json = json_decode(file_get_contents("./01Rfk6m6SUDXSD.json"), true);

    $kiss = $statistic_json["kiss"];
    $spittle = $statistic_json["spittle"];

    if ($_POST["type"] == "kiss")
        $kiss += 1;
    else
        $spittle += 1;

    $statistic_json = json_encode(array(
        "spittle" => $spittle,
        "kiss" => $kiss
        ), JSON_PRETTY_PRINT
    );

    file_put_contents("./01Rfk6m6SUDXSD.json", $statistic_json, LOCK_EX);

    exit;
}