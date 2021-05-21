@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../neilime/php-css-lint/scripts/php-css-lint
php "%BIN_TARGET%" %*
