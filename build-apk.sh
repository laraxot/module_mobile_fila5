#!/usr/bin/env bash

set -euo pipefail

# Build the Restaurant NativePHP Android artifact from the Laravel project root.
# Signing is deliberately external: credentials must never live in this module.

project_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$project_dir"

: "${JAVA_HOME:=/usr/lib/jvm/java-17-openjdk-amd64}"
: "${ANDROID_HOME:=/opt/android-sdk}"
export JAVA_HOME ANDROID_HOME
export PATH="$JAVA_HOME/bin:$ANDROID_HOME/platform-tools:$ANDROID_HOME/emulator:$PATH"

if [[ ! -x "$JAVA_HOME/bin/java" ]]; then
    printf 'Java 17 is required (JAVA_HOME=%s)\n' "$JAVA_HOME" >&2
    exit 1
fi

php artisan native:install android --no-interaction

if [[ -z "${ANDROID_KEYSTORE_FILE:-}" || -z "${ANDROID_KEYSTORE_PASSWORD:-}" ||
    -z "${ANDROID_KEY_ALIAS:-}" || -z "${ANDROID_KEY_PASSWORD:-}" ]]; then
    printf '%s\n' 'Set ANDROID_KEYSTORE_FILE, ANDROID_KEYSTORE_PASSWORD, ANDROID_KEY_ALIAS and ANDROID_KEY_PASSWORD for a signed APK.' >&2
    exit 2
fi

php artisan native:package android --build-type=release --no-interaction --no-tty

apk="$project_dir/nativephp/android/app/build/outputs/apk/release/app-release.apk"
test -f "$apk"
printf 'APK: %s\nSHA-256: ' "$apk"
sha256sum "$apk"
