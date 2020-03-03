# OTIC Format 

Open Telemetry Interchange Containers Format


## Install using composer

```
composer require talpa/otic-php
vendor/talpa/otic-php/lib/install-otic-extension.sh
```

## Notes
Otic entries consist of 4 fields:

| Field | Types | Notes |
|---|---|---|
| Timestamp | float | internally stored with 4 decimals, rounded down |
|Name| string | Length restrictions:|
|Unit| string |  Name+Unit < 255 characters  |
|Value| bool/int/float/string/null | max 255 Bytes|


#####Example:
| Timestamp | Name | Unit | Value |
|---|---|---|---|
| 1582612681.9972 | acceleration | m/s² | 9.81 |

## Example


## Install OTIC PHP Extension

```bash
lib/install-otic-extension.sh
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
| libotic | 10,368,000 | 26.478s | 6.794s | 0.348s |
| libotic | 10,368,000 | 29.622s | 7.574s | 0.359s |
| libotic | 10,368,000 | 29.649s | 7.631s | 0.357s |
