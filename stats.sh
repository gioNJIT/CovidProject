#!/bin/sh


while [ true ]
do

	urlfile=$1

	while IFS= read -r line
	do

	  case $line in
		*.*.*) 
		  website=${line#*.}
		  website=${website%.*};;
		*)
		  printf >&2 '%s\n' "failure in case $line"
	  esac
	  
	  curl -L --include -v "$line" > "$website.html"
	  java -jar tagsoup-1.2.1.jar --files "$website.html"  
	  rm "$website.html"
	  
	done < "$urlfile"


	python3 parser.py ${website}.xhtml

	rm "${website}.xhtml"

	sleep 1800

done
