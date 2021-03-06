<?php
chdir(dirname(__FILE__).'/..');
include('vendor/autoload.php');

// Check the list of feeds and queue jobs for any that are due for an update
$feeds = ORM::for_table('feeds')
  ->select_many_expr('feeds.*')
  ->join('subscribers', ['feeds.id','=','subscribers.feed_id'])
  ->where_lt('next_check_at', date('Y-m-d H:i:s'))
  ->find_many();
foreach($feeds as $feed) {
  echo "Queuing $feed->id $feed->url\n";
  q()->queue('Jobs\\CheckFeed', 'poll', $feed->id);
}
