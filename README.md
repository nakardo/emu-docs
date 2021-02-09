# [emu-docs](https://nakardo.github.io/emu-docs)

This is an emu-docs.org dump from the web.archive.org. Content copyright belong
to their respective owners. This repo was created for sake of preserving.

## How to dump

```
$ docker run --rm -it -v $PWD/docs:/websites \
    hartator/wayback-machine-downloader http://emu-docs.org \
    --directory ./websites \
    --to 20161219081244 \
    --concurrency 5 \
    --maximum-snapshot 10000 \
    --all

$ ./rename.sh
```

Inspired from [-u/-Mahn](https://www.reddit.com/user/-Mahn/) post on [r/EmuDev](https://www.reddit.com/r/EmuDev/comments/glgxad/emudocsorg_site_archive_old_but_gold_reference/).
