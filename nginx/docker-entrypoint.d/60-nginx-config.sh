#!/bin/sh

set -e

export NGINX_UPSTREAM="${NGINX_UPSTREAM:-unix:/run/php-fpm.sock}"
export NGINX_CLIENT_MAX_BODY_SIZE="${NGINX_CLIENT_MAX_BODY_SIZE:-10m}"
export NGINX_KEEPALIVE_TIMEOUT="${NGINX_KEEPALIVE_TIMEOUT:-65}"

# shellcheck disable=SC2046
# Substitute variables in NGINX configuration
defined_envs=$(printf "\${%s} " $(env | grep -E "^NGINX_.*" | cut -d= -f1))
envsubst "$defined_envs" </etc/nginx/nginx.conf >/tmp/nginx.conf
mv -f /tmp/nginx.conf /etc/nginx/nginx.conf

# Substitute variables in PHP configuration
envsubst </etc/php/conf.d/99-custom.ini >/tmp/99-custom.ini
mv -f /tmp/99-custom.ini /etc/php/conf.d/99-custom.ini

exit 0
