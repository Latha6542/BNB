<?php
echo password_hash($argv[1] ?? 'changeme123', PASSWORD_DEFAULT) . PHP_EOL;
