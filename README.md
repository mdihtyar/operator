# Operator
This repository contains an example to interconnect IP-PBX Asterisk with operator for receiving calls and other different actions.

Operator can analyze calls history and listen calls records when it is required.

## Requirements
* Ubuntu 16.04 server
* MySQL
* php 5.6 (for running operator management console)
* IP-PBX Asterisk
* User softphone, like as **SJphone** or physical device like as IP phone, that can be connected to **IP-PBX Asterisk** by using SIP protocol.

This example shows how can be connected GSM gateway like **OpenVox VS-GW1202-4G** for managing calls by operator to IP-PBX Asterisk.

## Usage

At first dedicate Ubuntu server for running application.

```shell

$ git clone https://github.com/mdihtyar/operator.git
$ cd operator
$ ./install.sh

```

After successfully completed installation operator management console will be reachable through the web browser by using such url: `http://<your dedicated server ip address>/operator`.
