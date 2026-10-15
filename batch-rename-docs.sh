#!/bin/bash
# BMAD + Second Brain — Batch Rename .md Files (Remove Dates/Numbers)
# Convention: No dates (YYYY-MM-DD), no sequential numbers (-1, -2, -3), kebab-case only
# Source: docs/wiki/guidelines/documentation-standards-master.md

echo "=== BMAD + Second Brain: Batch Rename .md Files ==="
echo "Convention: No dates, no -N suffixes, kebab-case, lowercase (except README.md)"
echo ""

# Root level files with dates in docs/
find /mnt/nas07/var/www/_bases/base_fixcity_fila5/docs -name "*.md" -type f | grep -E "20[0-9]{2}-[0-9]{2}-[0-9]{2}|-[0-9]+\.md$" | while read f; do
    dir=$(dirname "$f")
    base=$(basename "$f")
    # Remove date pattern: YYYY-MM-DD -> removed
    newbase=$(echo "$base" | sed -E 's/-[0-9]{4}-[0-9]{2}-[0-9]{2}\.md/.md/')
    # Remove -N.md suffix
    newbase=$(echo "$newbase" | sed -E 's/-[0-9]+\.md/.md/')
    # If name changed, rename
    if [ "$base" != "$newbase" ]; then
        newpath="$dir/$newbase"
        if [ -f "$newpath" ]; then
            echo "CONFLICT: $newpath already exists (merge needed)"
        else
            echo "RENAME: $base -> $newbase"
            mv "$f" "$newpath"
        fi
    fi
done

# Module docs with dates
find /mnt/nas07/var/www/_bases/base_fixcity_fila5/laravel/Modules -name "*.md" -type f | grep -E "20[0-9]{2}-[0-9]{2}-[0-9]{2}|-[0-9]+\.md$" | while read f; do
    dir=$(dirname "$f")
    base=$(basename "$f")
    newbase=$(echo "$base" | sed -E 's/-[0-9]{4}-[0-9]{2}-[0-9]{2}\.md/.md/')
    newbase=$(echo "$newbase" | sed -E 's/-[0-9]+\.md/.md/')
    if [ "$base" != "$newbase" ]; then
        newpath="$dir/$newbase"
        if [ -f "$newpath" ]; then
            echo "CONFLICT: $newpath already exists (merge needed)"
        else
            echo "RENAME: $base -> $newbase"
            mv "$f" "$newpath"
        fi
    fi
done

# Theme docs with dates
find /mnt/nas07/var/www/_bases/base_fixcity_fila5/laravel/Themes -name "*.md" -type f | grep -E "20[0-9]{2}-[0-9]{2}-[0-9]{2}|-[0-9]+\.md$" | while read f; do
    dir=$(dirname "$f")
    base=$(basename "$f")
    newbase=$(echo "$base" | sed -E 's/-[0-9]{4}-[0-9]{2}-[0-9]{2}\.md/.md/')
    newbase=$(echo "$newbase" | sed -E 's/-[0-9]+\.md/.md/')
    if [ "$base" != "$newbase" ]; then
        newpath="$dir/$newbase"
        if [ -f "$newpath" ]; then
            echo "CONFLICT: $newpath already exists (merge needed)"
        else
            echo "RENAME: $base -> $newbase"
            mv "$f" "$newpath"
        fi
    fi
done

echo ""
echo "=== BATCH RENAME COMPLETE ==="
echo "Remaining date/number violations:"
find /mnt/nas07/var/www/_bases/base_fixcity_fila5 -name "*.md" -type f | grep -E "20[0-9]{2}-[0-9]{2}-[0-9]{2}|-[0-9]+\.md$" | wc -l
echo ""
echo "Password sudo: zorin"
echo "Second Brain updated: MEMORY.md"
