<?php
/**
 * Implementation of the Disqus v1.1 API.
 *
 * http://groups.google.com/group/disqus-dev/web/api-1-1
 *
 * @author		Disqus <team@disqus.com>
 * @copyright	2007-2010 Big Head Labs
 * @link		http://disqus.com/
 * @package		Disqus
 * @version		1.1
 */

require_once(dirname(__FILE__) . '/url.php');

/** @#+
 * Constants
 */
/**
 * Base URL for Disqus.
 */

define('DISQUS_TYPE_SPAM', 'spam');
define('DISQUS_TYPE_DELETED', 'killed');
define('DISQUS_TYPE_KILLED', DISQUS_TYPE_DELETED);
define('DISQUS_TYPE_NEW', 'new');

define('DISQUS_STATE_APPROVED', 'approved');
define('DISQUS_STATE_UNAPPROVED', 'unapproved');
define('DISQUS_STATE_SPAM', 'spam');
define('DISQUS_STATE_DELETED', 'killed');
define('DISQUS_STATE_KILLED', DISQUS_STATE_DELETED);

define('DISQUS_ACTION_SPAM', 'spam');
define('DISQUS_ACTION_APPROVE', 'approve');
define('DISQUS_ACTION_DELETE', 'delete');
define('DISQUS_ACTION_KILL', 'kill');

if (!extension_loaded('json')) {
	require_once(dirname(__FILE__) . '/json.php');
	function dsq_json_decode($data) {
		$json = new JSON;
		return $json->unserialize($data);
	}
} else {
	function dsq_json_decode($data) {
		return json_decode($data);
	}
}

/**
 * Helper methods for all of the Disqus 1.1 API methods.
 *
 * @package		Disqus
 * @author		DISQUS.com <team@disqus.com>
 * @copyright	2007-2010 Big Head Labs
 * @version		1.1
 */
class DisqusAPI {
	var $user_api_key;
	var $forum_api_key;
	var $api_url = 'http://www.disqus.com/api/';
	var $api_version = '1.1';

	/**
	 * Creates a new interface to the Disqus API.
	 *
	 * @param $user_api_key
	 *   (optional) The User API key to use.
	 * @param $forum_api_key
	 *   (optional) The Forum API key to use.
	 * @param $api_url
	 *   (optional) The prefix URL to use when calling the Disqus API.
	 */
	function DisqusAPI($user_api_key, $forum_api_key, $api_url='http://www.disqus.com/api/') {
		$this->user_api_key = $user_api_key;
		$this->forum_api_key = $forum_api_key;
		$this->api_url = $api_url;
		$this->last_error = null;
	}

	/**
	 * Makes a call to a Disqus API method.
	 *
	 * @return
	 *   The Disqus object.
	 * @param $method
	 *   The Disqus API method to call.
	 * @param $args
	 *   An associative array of arguments to be passed.
	 * @param $post
	 *   TRUE or FALSE, depending on whether we're making a POST call.
	 */
	function call($method, $args=array(), $post=false) {
		$url = $this->api_url . $method . '/';

		if (!isset($args['user_api_key'])) {
			$args['user_api_key'] = $this->user_api_key;
		}
		if (!isset($args['forum_api_key'])) {
			$args['forum_api_key'] = $this->forum_api_key;
		}
		if (!isset($args['api_version'])) {
			$args['api_version'] = $this->api_version;
		}

		foreach ($args as $key=>$value) {
			// XXX: Disqus is lacking some exception handling and we sometimes
			// end up with 500s when passing invalid values
			if (empty($value)) unset($args[$key]);
		}

		if (!$post) {
			$url .= '?' . dsq_get_query_string($args);
			$args = null;
		}

		if (!($response = dsq_urlopen($url, $args)) || !$response['code']) {
			$this->last_error = 'Unable to connect to the Disqus API servers';
			return false;
		}

		if ($response['code'] != 200) {
			if ($response['code'] == 500) {
				// Try to grab the exception ID for better reporting
				if (!empty($response['headers']['X-Sentry-ID'])) {
				    $this->last_error = 'DISQUS returned a bad response (HTTP '.$response['code'].', ReferenceID: '.$response['headers']['X-Sentry-ID'].')';
				    return false;
				}
			} elseif ($response['code'] == 400) {
				$data = dsq_json_decode($response['data']);
				if ($data && $data->message) {
					$this->last_error = $data->message;
				} else {
					$this->last_error = "DISQUS returned a bad response (HTTP ".$response['code'].")";
				}
				return false;
			}
			$this->last_error = "DISQUS returned a bad response (HTTP ".$response['code'].")";
			return false;
		}

		$data = dsq_json_decode($response['data']);

		if (!$data) {
			$this->last_error = 'No valid JSON content returned from Disqus';
			return false;
		}

		if (!$data->succeeded) {
			if (!$data->message) {
				$this->last_error = '(No error message was received)';
			} else {
				$this->last_error = $data->message;
			}
			return false;
		}
		
		$this->last_error = null;

		return $data->message;
	}

	/**
	 * Retrieve the last error message recorded.
	 *
	 * @return
	 *   The last recorded error from the API
	 */
	function get_last_error() {
		if (empty($this->last_error)) return;
		if (!is_string($this->last_error)) {
			return var_export($this->last_error);
		}
		return $this->last_error;
	}

	/**
	 * Validate API key and get username.
	 *
	 * @return
	 *   Username matching the API key
	 */
	function get_user_name() {
		return $this->call('get_user_name', array(), true);
	}

	/**
	 * Returns an array of hashes representing all forums the user owns.
	 *
	 * @return
	 *   An array of hashes representing all forums the user owns.
	 */
	function get_forum_list() {
		return $this->call('get_forum_list');
	}

	/**
	 * Get a forum API key for a specific forum.
	 *
	 * @param $forum_id
	 *   the unique id of the forum
	 * @return
	 *   A string which is the Forum Key for the given forum.
	 */
	function get_forum_api_key($forum_id) {
		$params = array(
			'forum_id'		=> $forum_id,
		);

		return $this->call('get_forum_api_key', $params);
	}

	/**
	 * Get a list of comments on a website.
	 *
	 * Both filter and exclude are multivalue arguments with comma as a divider.
	 * That makes is possible to use combined requests. For example, if you want
	 * to get all deleted spam messages, your filter argument should contain
	 * 'spam,killed' string.
	 *
	 * @param $forum_id
	 *   The forum ID.
	 * @param $params
	 *   - limit: Number of entries that should be included in the response. Default is 25.
	 *   - start: Starting point for the query. Default is 0.
	 *   - filter: Type of entries that should be returned.
	 *   - exclude: Type of entries that should be excluded from the response.
	 * @return
	 *   Returns posts from a forum specified by id.
	 */
	function get_forum_posts($forum_id, $params=array()) {
		$params['forum_id'] = $forum_id;

		return $this->call('get_forum_posts', $params);
	}

	/**
	 * Count a number of comments in articles.
	 *
	 * @param $thread_ids
	 *   an array of thread IDs belonging to the given forum.
	 * @return
	 *   A hash having thread_ids as keys and 2-element arrays as values.
	 */
	function get_num_posts($thread_ids) {
		$params = array(
			'thread_ids'	=> is_array($thread_ids) ? implode(',', $thread_ids) : $thread_ids,
		);

		return $this->call('get_num_posts', $params);
	}

	/**
	 * Returns a list of categories that were created for a website (forum) provided.
	 *
	 * @param $forum_id
	 *   the unique of the forum
	 * @return
	 *   A hash containing category_id, title, forum_id, and is_default.
	 */
	function get_categories_list($forum_id) {
		$params = array(
			'forum_id'		=> $forum_id,
		);

		return $this->call('get_categories_list', $params);
	}

	/**
	 * Get a list of threads on a website.
	 *
	 * @param $forum_id
	 *   the unique id of the forum.
	 * @param $params
	 *   - limit: Number of entries that should be included in the response. Default is 25.
	 *   - start: Starting point for the query. Default is 0.
	 *   - category_id: Filter entries by category
	 * @return
	 *   An array of hashes representing all threads belonging to the given forum.
	 */
	function get_thread_list($forum_id, $params=array()) {
		$params['forum_id'] = $forum_id;

		return $this->call('get_thread_list', $params);
	}

	/**
	 * Get a list of threads with new comments.
	 *
	 * @param $forum_id
	 *   The Forum ID.
	 * @param $since
	 *   Start date for new posts. Format: 2009-03-30T15:41, Timezone: UTC.
	 * @return
	 *   An array of hashes representing all threads with new comments since offset.
	 */
	function get_updated_threads($forum_id, $since) {
		$params = array(
			'forum_id'		=> $forum_id,
			'since'			=> is_string($since) ? $string : strftime('%Y-%m-%dT%H:%M', $since),
		);

		return $this->call('get_updated_threads', $params);
	}

	/**
	 * Get a list of comments in a thread.
	 *
	 * Both filter and exclude are multivalue arguments with comma as a divider.
	 * That makes is possible to use combined requests. For example, if you want
	 * to get all deleted spam messages, your filter argument should contain
	 * 'spam,killed' string. Note that values are joined by AND statement so
	 * 'spam,new' will return all messages that are new and marked as spam. It
	 * will not return messages that are new and not spam or that are spam but
	 * not new (i.e. has already been moderated).
	 *
	 * @param $thread_id
	 *   The ID of a thread belonging to the given forum
	 * @param $params
	 *   - limit: Number of entries that should be included in the response. Default is 25.
	 *   - start: Starting point for the query. Default is 0.
	 *   - filter: Type of entries that should be returned (new, spam or killed).
	 *   - exclude: Type of entries that should be excluded from the response (new, spam or killed).
	 * @return
	 *   An array of hashes representing representing all posts belonging to the
	 *   given forum.
	 */
	function get_thread_posts($thread_id, $params=array()) {
		$params['thread_id'] = $thread_id;

		return $this->call('get_thread_posts', $params);
	}

	/**
	 * Get or create thread by identifier.
	 *
	 * This method tries to find a thread by its identifier and title. If there is
	 * no such thread, the method creates it. In either case, the output value is
	 * a thread object.
	 *
	 * @param $identifier
	 *   Unique value (per forum) for a thread that is used to keep be able to get
	 *   data even if permalink is changed.
	 * @param $title
	 *   The title of the thread to possibly be created.
	 * @param $params
	 *   - category_id:  Filter entries by category
	 *   - create_on_fail: if thread does not exist, the method will create it
	 * @return
	 *   Returns a hash with two keys:
	 *   - thread: a hash representing the thread corresponding to the identifier.
	 *   - created: indicates whether the thread was created as a result of this
	 *     method call. If created, it will have the specified title.
	 */
	function thread_by_identifier($identifier, $title, $params=array()) {
		$params['identifier'] = $identifier;
		$params['title'] = $title;

		return $this->call('thread_by_identifier', $params, true);
	}

	/**
	 * Get thread by URL.
	 *
	 * Finds a thread by its URL. Output value is a thread object.
	 *
	 * @param $url
	 *   the URL to check for an associated thread
	 * @param $partner_api_key
	 *   (optional) The Partner API key.
	 * @return
	 *   A thread object, otherwise NULL.
	 */
	function get_thread_by_url($url, $partner_api_key=null) {
		$params = array(
			'url'			=> $url,
			'partner_api_key'	=> $partner_api_key,
		);

		return $this->call('get_thread_by_url', $params);
	}

 	/**
	 * Updates thread.
	 *
	 * Updates thread, specified by id and forum API key, with values described in
	 * the optional arguments.
	 *
	 * @param $thread_id
	 *   the ID of a thread belonging to the given forum
	 * @param $params
	 *   - title: the title of the thread
	 *   - slug: the per-forum-unique string used for identifying this thread in
	 *           disqus.com URL’s relating to this thread. Composed of
	 *           underscore-separated alphanumeric strings.
	 *   - url: the URL this thread is on, if known.
	 *   - allow_comments: whether this thread is open to new comments
	 * @return
	 *   Returns an empty success message.
	 */
	function update_thread($thread_id, $params=array()) {
		$params['thread_id'] = $thread_id;

		return $this->call('update_thread', $params, true);
	}

	/**
	 * Creates a new post.
	 *
	 * Creates a comment to the thread specified by id.
	 *
	 * @param $thread_id
	 *   the thread to post to
	 * @param $message
	 *   the content of the post
	 * @param $author_name
	 *   the post creator’s name
	 * @param $author_email
	 *   the post creator’s email address
	 * @param $params
	 *   - partner_api_key
	 *   - created_at: Format: 2009-03-30T15:41, Timezone: UTC
	 *   - ip_address: the author’s IP address
	 *   - author_url: the author's homepage
	 *   - parent_post: the id of the parent post
	 *   - state: Comment's state, must be one of the following: approved,
	 *            unapproved, spam, killed
	 * @return
	 *   Returns modified version.
	 */
	function create_post($thread_id, $message, $author_name, $author_email, $params=array()) {
		$params['thread_id'] = $thread_id;
		$params['message'] = $message;
		$params['author_name'] = $author_name;
		$params['author_email'] = $author_email;

		return $this->call('create_post', $params, true);
	}

	/**
	 * Delete a comment or mark it as spam (or not spam).
	 *
	 * @param $post_id
	 *   The Post ID.
	 * @param $action
	 *   Name of action to be performed. Value can be 'spam', 'approve' or 'kill'.
	 * @return
	 *   Returns modified version.
	 */
	function moderate_post($post_id, $action) {
		$params = array(
			'post_id'		=> $post_id,
			'action'		=> $action,
		);

		return $this->call('moderate_post', $params, true);
	}
}

?>
