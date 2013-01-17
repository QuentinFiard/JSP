#!/bin/sh

ls *.pdf | sort -nr | xargs -t /usr/local/bin/gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$1

for f in *.pdf;
do
	if [ "$f" != "$1" ]; then
		rm $f
	fi
done  
