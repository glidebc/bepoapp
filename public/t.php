<?php

if(true){
	$h='10.110.150.213';
	$u='pmauser';
	$p='bepo66007766';
	$link=@mysql_connect($h,$u,$p);
	$res=mysql_query("select * from bepo.bepoapp_ad where id=217");
	while($row=mysql_fetch_assoc($res)){
		echo $row['id'];
	}
}

if(false){
	$m = new Memcached;
	$m->addServer('10.110.150.222', 11211);
	$m->set('test.0','hello');
	echo $m->get('test.0');
}


