

IMG=10.110.150.237:/home/web/gotv/newbepotv
INST=.
COMPOSER=/usr/local/bin/composer
MONGO_BACUP=mongodump  --host 10.110.150.214 --username admin --password iamadmin --authenticationDatabase admin --db test --out /root/mongodbbackup/$(shell date '+%Y%m%d.%H%M%S')

all:
		php artisan clear-compiled
	    php artisan cache:clear
	    php artisan config:clear
	    php artisan debugbar:clear
	    php artisan route:clear
	    php artisan view:clear


perm:
	    -find -type f | xargs chmod -R 664
	    -find -type d | xargs chmod -R 775

set_own_group:
	    chown -R :$(group) .

set_own_user:
	    chown -R $(user) .

info:
	@echo "-Nginx"
	    @ps aux | grep nginx | grep -v 'grep' | awk -e '{print $$1}' | uniq
	    @echo "-Fpm"
	    @ps aux | grep fpm | grep -v 'grep' | awk -e '{print $$1}' | uniq
	    @echo ""



backup:
	git add .
	git commit -a -m 'update'
	git push -ff cti_origin master


super_start:super_stop
	 supervisord -c /etc/supervisord.conf

super_stop:
	 -ps aux | grep supervisord | grep -v 'grep'  | awk -e '{print $$2}' | xargs kill

super_status:
	supervisorctl status


clean:
	php artisan clear-compiled
	php artisan cache:clear
	php artisan config:clear
	php artisan debugbar:clear
	php artisan route:clear
	php artisan view:clear
	-$(COMPOSER) dump-autoload
	rm -rf storage/logs/*.log
	chown -R nginx .

depoly:
	php artisan clear-compiled
	php artisan cache:clear
	php artisan config:clear
	php artisan debugbar:clear
	php artisan route:clear
	php artisan view:clear
	php artisan version
	-$(COMPOSER) dump-autoload
	rm -rf storage/logs/*.log
	chown -R nginx .
	chmod -R 777 .

tar:
	tar --exclude=database/* --exclude=.git/* --exclude=public/images/ -cvf bepoweb.tar *
	gzip -f -9 bepoweb.tar
	mv bepoweb.tar.gz public/


install_asset:
	mount -t nfs $(IMG) $(INST)/public/newbepotv/
	chmod -R 777 .
	chown -R nginx .


mongo_backup:
	@echo "run $(MONGO_BACUP)"
	$(MONGO_BACUP)
