# OTIC Format 

Open Telemetry Interchange Containers Format


## Install using composer

```
composer require talpa/otic-php
vendor/talpa/otic-php/lib/install-otic-extension.sh
```




## Example


## Install OTIC PHP Extension

```bash
lib/install-urdsfmt.sh
```

## Using the command line tool

Compress data from stdin and save to file
```
/opt/bin/otic.php --otic --pack --indurad5colQuickfix --failOnErr --stdin --out=/tmp/out.otic
```

## Benchmarks
Benchmark results for urdsfmt and libotic

| Library | Lines | Write | Read All | Read Selection (2) |
|---------|-------|-------|----------|----------------|
| urdsfmt | 10,368,000 | 35.123s | 17.227s | 0.541s |
| urdsfmt | 10,368,000 | 33.440s | 17.030s | 0.463s |
| urdsfmt | 10,368,000 | 34.243s | 17.399s | 0.538s |
| libotic | 10,368,000 | 26.478s | 6.794s | 0.348s |
| libotic | 10,368,000 | 29.622s | 7.574s | 0.359s |
| libotic | 10,368,000 | 29.649s | 7.631s | 0.357s |
|||||
| ⌀ urdsfmt | 10,368,000    | 34,2686   | 17,2186 | 0,514   |
| ⌀ libotic | 10,368,000    | 28,583    | 7,333   | 0,3546  |
| factor    |               | 1,1989    | 2,3481  | 1,4495  |

