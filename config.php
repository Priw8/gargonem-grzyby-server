<?php

// ========================================
// ============= KONFIGURACJA =============
// ========================================

// Link do webhooka na którego wysyłane mają być komunikaty
$WEBHOOK_URL = "";

// Świat z którego akceptowane mają być grzyby (sprawdzany po nagłówku origin)
$REQUIRED_WORLD = "aether";

// Adres z którego mają być brane obrazki grzybów do pokazania na Discordzie
// (Margonem blokuje IP Discorda, więc nie działa bezpośrednio z cdn!)
// Należy ustawić na "<adres serwera na którym działa serwer>/obrazki"
// (MUSI się kończyć znakiem /)
$NPC_ICON_PATH = "https://priw8.com/margo/grzyby/obrazki/";

// Rola do powiadomienia w webhooku (trzeba skopiować ID roli i zamienić numerki niżej)
$PING = "<@&1158098635767152641>";

// ========================================
// ========================================
// ========================================

?>