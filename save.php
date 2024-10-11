<?php

include "./config.php";
include "./common-headers.php";

function sendError($reason) {
    echo '{"ok":0,"msg":"' . $reason . '"}';
}

// Nazwy NPC dozwolone do zapisywania
$ALLOWED_NPCS = array(
    // z .pl
    "Ogromna płomiennica tląca", "Ogromna dzwonkówka tarczowata", 
    "Ogromny bulwiak pospolity", "Ogromny mroźlarz", "Ogromny szpicak ponury",

    // z .com
    "Giant Smoldering Light", "Giant Shieldbell", "Giant Common Bulbcap",
    "Giant Frostcap", "Giant Gloomspike"
);

// Poziomy NPC dozwolone do zapisywania
$ALLOWED_LVLS = array(100, 200);

// Domyślny czas respawnu
$defaultTimer = 60*15;

if (strpos($_SERVER['HTTP_ORIGIN'], $REQUIRED_WORLD) === false) {
    sendError("bad world (expecting $REQUIRED_WORLD)");
    return;
}

if (empty($_POST["npc"]) || gettype($_POST["npc"]) != "string") {
    sendError("malformed npc (1)");
    return;
}

$npc = json_decode($_POST["npc"], true);

if (!($npc != null && isset($npc["nick"]) && isset($npc["lvl"]) && gettype($npc["lvl"]) == "integer" 
  && isset($npc["icon"]) && isset($npc["x"]) && isset($npc["y"]) && isset($npc["map"]))) {
    sendError("malformed npc (2)");
    return;
}

if (!in_array($npc["nick"], $ALLOWED_NPCS)) {
    sendError("malformed npc (3)");
    return;
}

if (!in_array($npc["lvl"], $ALLOWED_LVLS)) {
    sendError("malformed npc (4)");
    return;
}

if (!isset($npc["timer"]) || $npc["timer"] > $defaultTimer) {
    sendError("malformed npc (5)");
    return;
}

$npcKey = $npc["nick"] . "-" . $npc["lvl"];

$file = fopen("data/timestamps.json", "r");
$timestampJson = fread($file, filesize("data/timestamps.json"));
fclose($file);
$timestamp = json_decode($timestampJson, true);

if ($timestamp == null) {
    $timestamp = array();
}

$timestampForThisNpc = $timestamp[$npcKey];
if (empty($timestampForThisNpc)) {
    $timestampForThisNpc = 0;
}

$foundBy = $_POST["foundBy"] ?? "Nick error";

if (strlen($foundBy) >= 48) {
    $foundBy = "Ktoś kto majstrował przy wysyłanym nicku";
}
$requiredDiff = 60*30; // 60sec * 30
if ($timestampForThisNpc + $requiredDiff < time()) {
    $timerRemaining = $npc["timer"];
    if ($timerRemaining < 0) {
        $timerRemaining = $defaultTimer;
    }

    $timestamp[$npcKey] = time() + ($timerRemaining - $defaultTimer);
    $file = fopen("data/timestamps.json", "w");
    fwrite($file, json_encode($timestamp));
    fclose($file);

    $discordMessage = json_encode([
        "content" => "$PING " . $npc["nick"] . " (" . $npc["lvl"] . "lvl) - " . $npc["map"] . " (" . $npc["x"] . "," . $npc["y"] . ")",
        "username" => $npc["nick"],
        "avatar_url" => $NPC_ICON_PATH . $npc["icon"],
        "embeds" => [
            [
                "title" => $foundBy . " znalazł grzyba!",
                "type" => "rich",
                "description" => $npc["nick"] . " (" . $npc["lvl"] . "lvl)\n" . $npc["map"] . " (" . $npc["x"] . "," . $npc["y"] . ")\n\n"
                    . "**Zniknie za:** <t:" . ($timerRemaining + time()) .":R>",
                "thumbnail" => [
                    "url" => $NPC_ICON_PATH . $npc["icon"]
                ]
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init( $WEBHOOK_URL );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $discordMessage);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec( $ch );
    // Jak coś nie trybi to odkomentuj poniższą linię i sprawdź odpowiedź API Discorda
    // file_put_contents('php://stderr', print_r($response, TRUE));
    curl_close( $ch );
    echo '{"ok":1}';
} else {
    echo '{"ok":1,"msg":"timestamp not expired"}';
}

?>