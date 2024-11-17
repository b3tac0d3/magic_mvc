<?php
namespace Aces;
use sm;

RequireAces( 
    "config",
    "scripty_stuff/db",
    "scripty_stuff/log",
    "scripty_stuff/query_record_audits",
    "scripty_stuff/query",
    "scripty_stuff/table"
);

function RequireAces($files) {
    $files = func_get_args();
    foreach($files as $file)
        require_once(sm::Dir("Depends") . "aces-sql/" . $file . ".php");
}
?>