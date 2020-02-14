#!/bin/bash


path=$(dirname $0)

#sudo cp $path/urdtsfmt.so /usr/lib/php/20170718

#echo 'extension=urdtsfmt.so' | sudo tee /etc/php/7.2/mods-available/urdtsfmt.ini
#sudo phpenmod urdtsfmt

sudo cp $path/libotic_php.so /usr/lib/php/20170718

echo 'extension=libotic_php.so' | sudo tee /etc/php/7.2/mods-available/libotic_php.ini
sudo phpenmod libotic_php

