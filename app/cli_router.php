#!/usr/local/bin/php
<?php
$msg = <<< EOF
# #############################################################################
# Cli for the Old Slim Framework 2.x is described here:
# http://akrabat.com/run-a-slim-2-application-from-the-command-line/
#
# Cli for Slim 3.x is using pavlakis/slim-cli , take a look at index.php, as
# a Middleware
# Usage:
# $ php index.php /cli/hello GET name=marcel
# #############################################################################
EOF;

echo $msg . PHP_EOL;