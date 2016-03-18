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


#Retrieve & print volume information : vol name, total size, used size
sub get_volume_info(){

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

		$out = $s->invoke( "volume-list-info");

	if ($out->results_status() eq "failed"){
		print($out->results_reason() ."\n");
	exit (-2);
	}

	my $volume_info = $out->child_get("volumes");
	my @result = $volume_info->children_get();

	foreach $vol (@result){
		my $vol_name = $vol->child_get_string("name");
		#print  "Volume name: $vol_name \n";
		my $size_total = $vol->child_get_int("size-total");
		my $total_volume_size = round(($size_total)/(1024*1024*1024));
		#print  "Total Size: $total_volume_size GB  \n";
		my $size_used = $vol->child_get_int("size-used");
                my $used_volume_size = round(($size_used)/(1024*1024*1024));
		#print  "Used Size: $used_volume_size GB \n";
		my $size_free = ($size_total-$size_used);
                my $total_free_size = round(($size_free)/(1024*1024*1024));
		#print  "Free Size: $total_free_size GB \n";
	        $percent_space_avail = round(($total_free_size/$total_volume_size)*100);  	
		#print  "Percent Free Space : $percent_space_avail \n";
		my $max_total = $vol->child_get_int("files-total");
		my $files_used = $vol->child_get_int("files-used");
		my $inodepercent = round (($files_used/$max_total)*100);
                #print "Inode used Percentage : $inodepercent \n";
		#print "--------------------------------------\n";
	 my $sqlq = $dbh -> prepare("insert into svvsize (filer,volume,total,used,free,percentage,inodeused,udtime) values (?,?,?,?,?,?,?,?)");
            $sqlq ->execute($filer,$vol_name,$total_volume_size,$used_volume_size,$total_free_size,$percent_space_avail,$inodepercent,$udtime);
	}
}

get_volume_info();
$dbh ->disconnect();
