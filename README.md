# Tvi Monitor Bundle #

[![Build Status](https://travis-ci.org/turnaev/monitor-bundle.svg?branch=master)](https://travis-ci.org/turnaev/monitor-bundle)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/turnaev/monitor-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/turnaev/monitor-bundle/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/turnaev/monitor-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/turnaev/monitor-bundle/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/turnaev/monitor-bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/turnaev/monitor-bundle/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/turnaev/monitor-bundle/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

### Checks:

##### php_version

```yaml
tvi_monitor:
  checks:
    php_version:
      check:
        expectedVersion: "7.0"
        operator: ">="
    php_version(s):
      items:
        a:
          check:
            expectedVersion: "7.0"
            operator: ">="
        b:
          check:
            expectedVersion: "7.0"

```
##### php_extension

```yaml
tvi_monitor:
  checks:
    php_version:
      check:
        expectedVersion: "7.0"
        operator: ">="
    php_version(s):
      items:
        a:
          check:
            expectedVersion: "7.0"
            operator: ">="
        b:
          check:
            expectedVersion: "7.0"

```
