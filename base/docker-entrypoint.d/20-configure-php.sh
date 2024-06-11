#!/bin/sh

set -e

export PHP_MEMORY_LIMIT="${PHP_MEMORY_LIMIT}"
export PHP_MAX_EXECUTION_TIME="${PHP_MAX_EXECUTION_TIME}"
export PHP_UPLOAD_MAX_FILESIZE="${PHP_UPLOAD_MAX_FILESIZE}"
export PHP_POST_MAX_SIZE="${PHP_POST_MAX_SIZE}"
export PHP_TIMEZONE="${PHP_TIMEZONE}"


# shellcheck disable=SC2046
# Substitute variables in PHP configuration
envsubst </etc/php/conf.d/99-custom.ini >/tmp/99-custom.ini
mv -f /tmp/99-custom.ini /etc/php/conf.d/99-custom.ini

exit 0
