#!/bin/sh
git add .
echo "commit name :"
read name
git commit -m $name 