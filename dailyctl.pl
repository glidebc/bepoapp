#!/usr/bin/perl
#
#
#
#
#
&main(30);

sub main{
	my $idle=shift||30;
	&logx('idle='.$idle);
	for(;;){
		my $synccmd='cd /home/web/admin;php artisan bepo:dbsync';
		my $cmd="ps aux | grep  'bepo:dbsync' | grep -v 'grep' | awk -e '{print \$2}'";
		my $pid=`$cmd`;
		chomp $pid;
		&logx("pid=$pid");
		if(!$pid){
			system($synccmd);
		}
		sleep($idle);
	}
}


sub logx{
	my $mesg=shift;
	print &ts();
	print ' '.$mesg;
	print "\n";
}

sub ts{
	my $ts=`date '+%Y-%m-%d %H:%M:%S'`;
	chomp $ts;
	return $ts;
}
