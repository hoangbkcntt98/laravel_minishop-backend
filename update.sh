#!/bin/sh
git add .
echo "commit name :"
read name
git commit -m $name 
echo "push to server"
git push heroku master
echo "push to github"
git push origin master
