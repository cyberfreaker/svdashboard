#!/usr/local/bin/perl -w
use lib '/usr/lib64/perl5/NetApp';
use Math::Round;
use NaServer;
use NaElement;
use DBI;
# Variable declaration

my $filer = shift;
my $user = ontapi_admin;
my $pw  = NetappAPI1;
my $udtime =  `date +%d-%b-%Y:%H:%M:%S:PDT`;
my $dbh = DBI->connect('DBI:mysql:database=snaptest;host=localhost','netapp','netapp')
  or die "Cannot connect: " . $DBI::errstr;


sub get_aggr_info(){

	my $out;


	my $s = NaServer->new ($filer, 1, 3);
	my $response = $s->set_style(LOGIN);
	if (ref ($response) eq "NaElement" && $response->results_errno != 0) 
	{
		my $r = $response->results_reason();
		print "Unable to set authentication style $r\n";
		exit 2;
	}
	$s->set_admin_user($user, $pw);
	$s->set_transport_type(HTTPS);
	$s->set_port(443);
	if (ref ($response) eq "NaElement" && $response->results_errno != 0) 
	{
		my $r = $response->results_reason();
		print "Unable to set HTTPS transport $r\n";
		exit 2;
	}

		$out = $s->invoke( "aggr-list-info");

	if ($out->results_status() eq "failed"){
		print($out->results_reason() ."\n");
	exit (-2);
	}

	my $aggr_info = $out->child_get("aggregates");
	my @result = $aggr_info->children_get();

	foreach $aggr (@result){
		my $aggr_name = $aggr->child_get_string("name");
		#print  "Aggregate name: $aggr_name \n";
		my $size_total = $aggr->child_get_int("size-total");
		my $total_aggr_size = round(($size_total)/(1024*1024*1024));
		#print  "Total Size: $total_aggr_size GB  \n";
		my $size_used = $aggr->child_get_int("size-used");
                my $used_aggr_size = round(($size_used)/(1024*1024*1024));
		#print  "Used Size: $used_aggr_size GB \n";
		my $size_free = ($size_total-$size_used);
                my $total_free_size = round(($size_free)/(1024*1024*1024));
		#print  "Free Size: $total_free_size GB \n";
	        $percent_space_avail = round(($total_free_size/$total_aggr_size)*100);  	
		#print  "Percent Free Space : $percent_space_avail \n";
	 my $sqlq = $dbh -> prepare("insert into aggrsize (filer,aggregate,total,used,free,percentage,udtime) values (?,?,?,?,?,?,?)");
            $sqlq ->execute($filer,$aggr_name,$total_aggr_size,$used_aggr_size,$total_free_size,$percent_space_avail,$udtime);
	}
}

get_aggr_info();
$dbh ->disconnect();
