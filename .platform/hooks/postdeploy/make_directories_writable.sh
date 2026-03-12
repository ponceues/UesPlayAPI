#!/bin/bash

cd /var/app/current || exit 1

sudo chmod -R 777 storage/
sudo chmod -R 777 bootstrap/cache/
