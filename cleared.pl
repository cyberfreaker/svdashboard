#!/usr/local/bin/perl -w
use DBI;

my $dbh = DBI->connect('DBI:mysql:database=snaptest;host=localhost','netapp','netapp') or die "Cannot connect: " . $DBI::errstr;

 my $sqlq = $dbh -> prepare("TRUNCATE TABLE svlatest");
  $sqlq ->execute();

  $dbh ->disconnect();
