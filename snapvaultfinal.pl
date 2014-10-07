#!/usr/local/bin/perl -w

use lib '/usr/lib64/perl5/NetApp';
use NaServer;
use NaElement;
use DBI;

# Variable declaration

my $argc = $#ARGV + 1;
my $filer = $ARGV[0];
my $user = <user>;
my $pw  = <password>;
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
        $response = $s->set_transport_type(HTTP);
        if (ref ($response) eq "NaElement" && $response->results_errno != 0)
        {
                my $r = $response->results_reason();
                print "Unable to set HTTP transport $r\n";
                exit 2;
        }

                relationship_status($s);

        exit 0;
}


# Usage: snapvault.pl <filer> <user> <password> relationshipStatus
sub relationship_status($)
{


        print " Filer: $filer";
        my $s = $_[0];
        my $records;
        my $tag;
        my $i;
        my @result;

        my $out = $s->invoke
                ("snapvault-secondary-relationship-status-list-iter-start");
         #print "iter start";
        if($out->results_status() eq "failed")
        {
                print($out->results_reason() ."\n");
                exit(-2);
        }

        print "\n-------------------------------------------------------------\n";
        $records = $out->child_get_string("records");
        print("Number Of relationships In Filer $filer : $records \n");

        $tag = $out->child_get_string("tag");
        #print("Tag: $tag \n");
        print "\n-------------------------------------------------------------\n";

        for ($i = 0; $i < $records; $i++)
        {
                my $rec = $s->invoke
                        ("snapvault-secondary-relationship-status-list-iter-next",
                                "maximum", 1, "tag", $tag);

                if($rec->results_status() eq "failed")
                {
                        print($rec->results_reason() ."\n");
                        exit(-2);
                }

        #       print("Records: ".$rec->child_get_string("records")."\n");

                my $statList = $rec->child_get("status-list");
                if(!($statList eq "undef"))
                {
                        @result = $statList->children_get();
                }
                else
                {
                        exit(0);
                }

                foreach $stat (@result)
                {
                        #print("Destination path: ");
                        #print($stat->child_get_string("destination-path")."\t");
                        my $dpath = $stat->child_get_string("destination-path");
                        #print("Destination system: ");
                        #print($stat->child_get_string("destination-system")."\t");
                        my $dfiler = $stat->child_get_string("destination-system");

                        #print("Source path: ");
                        #print($stat->child_get_string("source-path")."\t");
                        my $spath = $stat->child_get_string("source-path");

                        #print("Source system: ");
                        #print($stat->child_get_string("source-system")."\t");
                        my $sfiler = $stat->child_get_string("source-system");

                        #print("State: ");
                        #print($stat->child_get_string("state")."\t");
                        my $state = $stat->child_get_string("state");

                        #print("Status: ");
                        #print($stat->child_get_string("status")."\t");
                        my $status = $stat->child_get_string("status");


                         #print("Last transfer type: ");
                         #print($stat->child_get_string("last-transfer-type")."\t");
                        my $ltran = $stat->child_get_string("last-transfer-type");

                        #print("Lag Time: ");
                         #printf ("%.2f",($stat->child_get_string("lag-time")/3600)."\t");
                        my $flag = $stat->child_get_string("lag-time");
                        if($flag ne '')
                        {

                         $lag = int((($flag)/3600));
                                }
                        #print $lag;


                        #print "\n--------------------------------------------------------\n";



                                my $sqlq = $dbh -> prepare("insert into svlatest (dfiler,dpath,sfiler,spath,state,status,ltrans,lag,udtime) values (?,?,?,?,?,?,?,?,?)");
                                $sqlq ->execute($dfiler,$dpath,$sfiler,$spath,$state,$status,$ltran,$lag,$udtime);


                }
        }

        my $end = $s->invoke
                ("snapvault-secondary-relationship-status-list-iter-end","tag", $tag);

$dbh ->disconnect();
}
