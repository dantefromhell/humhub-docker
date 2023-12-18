#!/bin/sh
# This script reads configuration options for a file provided via environment variable
# HUMHUB_CONFIG_FILE and applies the settings sequentially to this container during startup.

# Stop script execution on weird shell conditions.
set -o errexit -o nounset

# Allow debugging this shell script by setting a shell variable called
# "TRACE" to value "1".
if [ "${TRACE:-0}" = "1" ]; then
  set -o xtrace
fi

echo "START === Configuring HumHub options"
CONFIG_FILE="${HUMHUB_CONFIG_FILE:-none}"

# Stop if nothing needs to be done.
if [ "${CONFIG_FILE}" = "none" ]; then
  echo "No config file (or name none) provided, aborting..."
  exit 0
fi

# Check the file actually exists and is readable and has content.
if [ ! -r "${CONFIG_FILE}" ]; then
  echo "Config file ${CONFIG_FILE} either does not exist or is not readable."
fi
if [ ! -s "${CONFIG_FILE}" ]; then
  echo "Config file ${CONFIG_FILE} seems to be empty."
fi

# Actually do the stuff.
grep -vE '^(\s*$|#)' "${CONFIG_FILE}" | while read -r LINE; do
  echo "Found config line: ${LINE}"
  su -s /bin/sh nginx -c "php yii settings/set ${LINE} --interactive=0"
done

echo "END === DONE configuring HumHub"
