!/usr/local/bin/perl -w
#============================================================#

use lib '/usr/lib64/perl5/NetApp';
use NaServer;
use NaElement;
use DBI;

# Variable declaration

my $argc = $#ARGV + 1;
my $filer = $ARGV[0];
my $user = ontapi_admin;
my $pw  = NetappAPI1;
my $command = shift;
my $value = shift;
my $udtime =  `date +%d-%b-%Y:%H:%M:%S:PDT`;
my $dbh = DBI->connect('DBI:mysql:database=snaptest;host=localhost','netapp','netapp')
  or die "Cannot connect: " . $DBI::errstr;

#Invoke routine
main();

sub main 
{
        # check for valid number of parameters


        my $s = NaServer->new ($filer, 1, 3);
        my $response = $s->set_style(LOGIN);
        if (ref ($response) eq "NaElement" && $response->results_errno != 0) 
        {
                my $r = $response->results_reason();
                print "Unable to set authentication style $r\n";
                exit 2;
        }
        $s->set_admin_user($user, $pw);
	$s->set_port(443);
        $response = $s->set_transport_type(HTTPS);
        if (ref ($response) eq "NaElement" && $response->results_errno != 0) 
        {
                my $r = $response->results_reason();
                print "Unable to set HTTPS transport $r\n";
                exit 2;
        }

                relationship_status($s);

        exit 0;
}

sub relationship_status($)
{


        print " Filer: $filer";
        my $s = $_[0];
        my $records;
        my $i;
        my @result;

        my $out = $s->invoke
                ("snapmirror-get-status");
        if($out->results_status() eq "failed")
        {
                print($out->results_reason() ."\n");
                exit(-2);
        }
	
       #	print $out->sprintf();
	my $status = $out->child_get("snapmirror-status");
	if(!($status eq undef))
	{
		@result = $status->children_get();
	}
	else
	{
		exit(0);
	}

     foreach $snapStat (@result){
		
		#print("Destination location: ");
		#print($snapStat->child_get_string("destination-location")."\n");
		my $dest = $snapStat->child_get_string("destination-location");
		

		#print("Lag time: ".$snapStat->child_get_string("lag-time")."\n");
		my $lagseconds = $snapStat->child_get_string("lag-time");

		#print("Last transfer duration: ");
		#print($snapStat->child_get_string("last-transfer-duration")."\n");
		my $ltseconds = $snapStat->child_get_string("last-transfer-duration");

		#print("Last transfer size: ");
		#print($snapStat->child_get_string("last-transfer-size")."\n");
		my $ltkb = $snapStat->child_get_string("last-transfer-size");


		#print("Source location: ");
		#print($snapStat->child_get_string("source-location")."\n");
		my $spath = $snapStat->child_get_string("source-location");

		#print("State: ".$snapStat->child_get_string("state")."\n");
		my $state=$snapStat->child_get_string("state");

		#print("Status: ".$snapStat->child_get_string("status")."\n");
		my $status = $snapStat->child_get_string("status");

		
		#print "------------------------------------------------------------\n";


		
		#Calcuate /Covert values to make sense

		my $lag = int(($lagseconds)/3600);
		my $lttime = int(($ltseconds)/60);
		my $ltsize = int(($ltkb)/1024);
		
		my $sqlq = $dbh -> prepare("insert into smstatus (dfiler,source,dpath,lag,lttime,ltsize,state,status,udtime) values(?,?,?,?,?,?,?,?,?)");
		$sqlq ->execute($filer,$spath,$dest,$lag,$lttime,$ltsize,$state,$status,$udtime);	
		

	} #end foreach
	$dbh ->disconnect();


} #end relationship status





