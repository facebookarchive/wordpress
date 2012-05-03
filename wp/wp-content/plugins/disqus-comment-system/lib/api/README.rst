Disqus API 1.1

Please see http://groups.google.com/group/disqus-dev/web/api-1-1 for more information.

Sample usage::

	require('disqus/disqus.php');
	
	$dsq = new DisqusAPI($user_api_key, $forum_api_key);
	if (($username = $dsq->get_user_name()) === false)
	{
	    throw new Exception($dsq->get_last_error());
	}


To run the included unit tests you will need to install PHPUnit::

	php disqus/tests.php <your user_api_key>
