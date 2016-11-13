# vccw/scaffold-vccw

[![Build Status](https://travis-ci.org/vccw-team/scaffold-vccw.svg?branch=master)](https://travis-ci.org/vccw-team/scaffold-vccw)

## Using

## Installing

Installing this package requires WP-CLI v0.23.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with:

```
$ wp package install vccw/scaffold-vccw
```

## Usage

```
NAME

  wp scaffold vccw

DESCRIPTION

  Generate a new VCCW environment.

SYNOPSIS

  wp scaffold vccw <directory> [--host=<hostname>] [--ip=<ip-address>]
  [--lang=<language>]

OPTIONS

  <directory>
    The directory of the new VCCW based guest machine.

  [--host=<hostname>]
    Hostname of the guest machine. Default is `vccw.dev`.

  [--ip=<ip-address>]
    IP address of the guest machine. Default is `192.168.33.10`.

  [--lang=<language>]
    Language of the WordPress. Default is `en_US`.

EXAMPLES

    $ wp scaffold vccw wordpress.dev
    Generating:   100% [===========================] 0:03 / 0:06
    Success: Generated.

    $ wp scaffold vccw wordpress.dev --lang=ja
    Generating:   100% [===========================] 0:03 / 0:06
    Success: Generated.
```
