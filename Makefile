all:
	echo "targets available:"
	echo "\tupgrade\tpulls from GitHub, updating the core Bashpress"

upgrade:
	git pull origin master
	sudo /etc/init.d/apache2 restart
