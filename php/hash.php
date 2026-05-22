<?php
$hash = password_hash('red123', PASSWORD_BCRYPT);
echo $hash;
echo "\n\nLength: " . strlen($hash);