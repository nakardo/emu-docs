#!/bin/bash
LC_CTYPE=C
WEBSITE_DIR="docs"
for f in $WEBSITE_DIR/*
do
    if [[ $f =~ ^.*\/(\?.+)$ ]]
    then
        DIR=${BASH_REMATCH[1]}
        NEW_DIR=${DIR#?}
        mv "${WEBSITE_DIR}/${DIR}" "${WEBSITE_DIR}/${NEW_DIR}"
    fi
done

find $WEBSITE_DIR -type f -name "*.html" -exec sed -i "" -E \
    -e "s~(href)=\"/\??(.*)~\1=\"/emu-docs/\2~g" \
    -e "s~(src)=\"/\??(.*)~\1=\"/emu-docs/\2~g" {} \;