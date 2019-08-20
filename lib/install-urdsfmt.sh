#!/bin/bash


sudo cp urdtsfmt.so /usr/lib/php/20170718

echo 'extension=urdtsfmt.so' | sudo tee /etc/php/7.2/mods-available/urdtsfmt.ini
sudo phpenmod urdtsfmt
