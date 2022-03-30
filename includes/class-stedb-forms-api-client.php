<?php

/**
 * Class STEDB_Forms_Api_Client
 * functions for the STEdb API
 */
class STEDB_Forms_Api_Client {

	/** @var string|null $user_id - user id in wp */
	public $user_id;

	/** @var string|null $secret - secret key in wp */
	public $secret;

	/** @var string|null $base_url - base url of api */
	public $base_url;

	/**
	 * constructor
	 *
	 * @param string|null $user_id
	 * @param string|null $secret
	 * @param string|null $base_url
	 */
	public function __construct( $user_id = null, $secret = null, $base_url = null ) {

		$stedb_forms_account = get_option( 'stedb_forms_account', array() );

		/** user id */
		if ( is_null( $user_id ) ) {
			$user_id = $stedb_forms_account['user_id'];
		}

		$this->user_id = $user_id;

		/** secret */
		if ( is_null( $secret ) ) {
			$secret = $stedb_forms_account['secret'];
		}

		$this->secret = $secret;

		/** base url */
		if ( is_null( $base_url ) ) {
			$base_url = $stedb_forms_account['base_url'];
		}

		$this->base_url = $base_url;
	}

	/**
	 * send remote request to STEdb API
	 *
	 * @param $path
	 * @param string $method
	 * @param array $data
	 *
	 * @return array|WP_Error
	 */
	public function send_request( $path, $method = 'GET', $data = array() ) {

		/**
		 * set request url
		 * @var string $request_url
		 */
		$request_url = esc_url( $this->base_url . $path );

		/**
		 * set headers
		 * @var array $headers
		 */
		$headers = array();

		/** set headers auth user id */
		$headers['X-Auth-UserId'] = $this->user_id;

		/** set headers auth time */
		$auth_time              = gmdate( 'c', time() );
		$headers['X-Auth-Time'] = $auth_time;

		/** set headers auth signature */
		$auth_signature              = hash_hmac( 'SHA256', $this->secret, $this->user_id . ':' . $auth_time );
		$headers['X-Auth-Signature'] = $auth_signature;

		/**
		 * set
		 * methods
		 */

		/** GET method */
		if ( 'GET' == $method ) {
			$data = array_filter( $data );

			if ( ! empty( $data ) ) {
				$request_url .= '?' . http_build_query( $data );
			}
		}

		/** PUT method */
		if ( 'PUT' == $method ) {
			$headers['Content-Type'] = 'application/json';

			/** format data */
			$data = json_encode( $data );
		}
		
		/** send request */
		$response = wp_remote_request( $request_url, array(
			'method'    => $method,
			'timeout'   => 60,
			'sslverify' => false, //todo: check ssl verify
			'blocking'  => true,
			'headers'   => $headers,
			'body'      => $data,
		) );
		write_log(PHP_EOL . "Sent Data:");
		write_log($data);
		write_log(PHP_EOL . "Start Response");
		write_log($data);
		write_log(PHP_EOL . "End of Response");
		/** return response */
		if ( ! is_wp_error( $response ) ) {

			/** success and return response body */
			if ( 200 == $response['response']['code'] ) {
				return json_decode( $response['body'], true );
			}

			/** error and return wp error */
			if ( 400 == $response['response']['code'] ) {
				return new WP_Error( 'stedb_forms_api_error_400', sprintf( esc_html__( 'The API sent the following error message: %s', 'stedb-forms' ), $response['response']['message'] ), $response );
			}

			/** error and return wp error */
			if ( 401 == $response['response']['code'] ) {
				return new WP_Error( 'stedb_forms_api_error_401', sprintf( esc_html__( 'The API sent the following error message: %s', 'stedb-forms' ), $response['response']['message'] ), $response );
			}

			/** error and return wp error */
			if ( 404 == $response['response']['code'] ) {
				return new WP_Error( 'stedb_forms_api_error_404', sprintf( esc_html__( 'The API sent the following error message: %s', 'stedb-forms' ), $response['response']['message'] ), $response );
			}

			/** error and return wp error */
			if ( 405 == $response['response']['code'] ) {
				return new WP_Error( 'stedb_forms_api_error_405', sprintf( esc_html__( 'The API sent the following error message: %s', 'stedb-forms' ), $response['response']['message'] ), $response );
			}

			/** error and return wp error */
			if ( 500 == $response['response']['code'] ) {
				return new WP_Error( 'stedb_forms_api_error_500', sprintf( esc_html__( 'The API sent the following error message: %s', 'stedb-forms' ), $response['response']['message'] ), $response );
			}

			/** error and return wp error */
			if ( 501 == $response['response']['code'] ) {
				return new WP_Error( 'stedb_forms_api_error_501', sprintf( esc_html__( 'The API sent the following error message: %s', 'stedb-forms' ), $response['response']['message'] ), $response );
			}

			/** unknown error and return wp error */
			return new WP_Error( 'stedb_forms_api_error_default', esc_html__( 'The API sent an unknown error.', 'stedb-forms' ), $response );

		} else {

			/** error and return wp error */
			return $response;

		}
	}

	/**
	 * POST /account/create
	 * account creation
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function create_account( $data = '' ) {

		if ( empty( $data ) ) {
			$data = array(
				'email'  => get_bloginfo( 'admin_email' ),
				'domain' => get_option( 'siteurl' ),
			);
		}

		return $this->send_request( 'account/create/', 'POST', $data );
	}

	/**
	 * POST /account/reactivate
	 * account reactivation
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function reactivate_account( $data = '' ) {
		return $this->send_request( 'account/reactivate/', 'POST', $data );
	}

	/**
	 * GET /account/save_address
	 * View account address
	 *
	 * @return array|WP_Error
	 */
	public function account_view_address() {
		return $this->send_request( 'accnt/view_address/', 'GET' );
	}

	/**
	 * POST /account/save_address
	 * Save account address
	 *
	 * @param $data
	 *
	 * @return array|WP_Error
	 */
	public function account_save_address( $data ) {
		return $this->send_request( 'accnt/save_address/', 'POST', $data );
	}

	/**
	 * GET /account/sm_providers
	 * Get social media providers
	 *
	 * @param $id
	 *
	 * @return array|WP_Error
	 */
	public function get_sm_providers( $id ) {
		return $this->send_request( 'accnt/sm_providers/' . intval( $id ), 'GET' );
	}

	/**
	 * GET /lists/{id}
	 * list information
	 *
	 * @param int|null $id
	 *
	 * @return array|WP_Error
	 */
	public function get_lists( $id = null ) {
		$get_path = 'lists/';

		/** add id to path */
		if ( ! empty( $id ) ) {
			$get_path .= intval( $id );
		}

		return $this->send_request( $get_path, 'GET' );
	}

	/**
	 * POST /lists
	 * list creation
	 *
	 * @param $data
	 *
	 * @return array|WP_Error
	 */
	public function create_lists( $data ) {
		$data = $this->filter_lists_data( $data );

		return $this->send_request( 'lists/', 'POST', $data );
	}

	/**
	 * PUT /lists
	 * list update
	 *
	 * @param $data
	 *
	 * @return array|WP_Error
	 */
	public function update_lists( $data ) {
		$data = $this->filter_lists_data( $data );

		return $this->send_request( 'lists/', 'PUT', $data );
	}

	/**
	 * DELETE /lists/{id}
	 * list removal
	 *
	 * @param $id
	 *
	 * @return array|WP_Error
	 */
	public function delete_lists( $id ) {
		return $this->send_request( 'lists/' . intval( $id ), 'DELETE' );
	}

	/**
	 * Filter
	 * lists data
	 */
	public function filter_lists_data( $data ) {

		/** change id to list id (due to wpdb structure) */
		if ( array_key_exists( 'list_id', $data ) ) {
			$data['id'] = $data['list_id'];
		}

		/** change name to list_name (due to wpdb structure) */
		if ( array_key_exists( 'name', $data ) ) {
			$data['list_name'] = $data['name'];
		}

		/** change form_builder_content to extra_data (due to wpdb structure) */
		if ( array_key_exists( 'form_builder_content', $data ) ) {
			$data['extra_data'] = $data['form_builder_content'];
		}

		return array_intersect_key( $data, array_flip( array( 'id', 'list_name', 'receiver', 'extra_data' ) ) );
	}

	/**
	 * GET /fields/{id}
	 * field information
	 *
	 * @param int|null $id
	 *
	 * @return array|WP_Error
	 */
	public function get_fields( $id = null ) {
		$get_path = 'fields/';

		/** add id to path */
		if ( ! empty( $id ) ) {
			$get_path .= intval( $id );
		}

		return $this->send_request( $get_path, 'GET' );
	}

	/**
	 * POST /fields
	 * field creation
	 *
	 * @param $data
	 *
	 * @return array|WP_Error
	 */
	public function create_fields( $data ) {
		$data = $this->filter_fields_data( $data );

		return $this->send_request( 'fields/', 'POST', $data );
	}

	/**
	 * DELETE /fields/{id}
	 * field removal
	 *
	 * @param $id
	 *
	 * @return array|WP_Error
	 */
	public function delete_fields( $id ) {
		return $this->send_request( 'fields/' . intval( $id ), 'DELETE' );
	}

	/**
	 * Filter
	 * fields data
	 */
	public function filter_fields_data( $data ) {

		/** change name to field_name (due to wpdb structure) */
		if ( array_key_exists( 'name', $data ) ) {
			$data['field_name'] = $data['name'];
		}

		/** change type to field_type (due to wpdb structure) */
		if ( array_key_exists( 'type', $data ) ) {
			$data['field_type'] = $data['type'];
		}

		/** change values to extra_data (due to wpdb structure) */
		if ( array_key_exists( 'values', $data ) ) {
			$data['extra_data'] = $data['values'];
		}

		if ( empty( $data['default_value'] ) ) {
			$data['default_value'] = '';
		}

		return array_intersect_key( $data, array_flip( array(
			'name',
			'field_name',
			'type',
			'field_type',
			'default_value',
			'list_id',
			'extra_data',
		) ) );
	}

	/**
	 * GET /campaigns/{id}
	 * campaign information
	 *
	 * @param int|null $id
	 *
	 * @return array|WP_Error
	 */
	public function get_campaigns( $id = null ) {
		$get_path = 'campaigns/';

		/** add id to path */
		if ( ! empty( $id ) ) {
			$get_path .= intval( $id );
		}

		return $this->send_request( $get_path, 'GET' );
	}

	/**
	 * POST /campaigns
	 * list creation
	 *
	 * @param $data
	 *
	 * @return array|WP_Error
	 */
	public function create_campaigns( $data ) {
		$data = $this->filter_campaigns_data( $data );

		return $this->send_request( 'campaigns/', 'POST', $data );
	}

	/**
	 * PUT /campaigns
	 * list update
	 *
	 * @param $data
	 *
	 * @return array|WP_Error
	 */
	public function update_campaigns( $data ) {
		$data = $this->filter_campaigns_data( $data );

		return $this->send_request( 'campaigns/', 'PUT', $data );
	}

	/**
	 * DELETE /campaigns/{id}
	 * list removal
	 *
	 * @param $id
	 *
	 * @return array|WP_Error
	 */
	public function delete_campaigns( $id ) {
		return $this->send_request( 'campaigns/' . intval( $id ), 'DELETE' );
	}

	/**
	 * GET /campaign/check_from_email/{email}?code={code}
	 * Get from email status information
	 *
	 * @param $email
	 * @param null $code
	 *
	 * @return array|WP_Error
	 */
	public function get_campaigns_check_from_email( $email, $code = null ) {

		$path = sanitize_email( $email );

		return $this->send_request( 'campaigns/check_from_email/' . $path, 'GET', array(
			'code' => sanitize_text_field( $code ),
		) );
	}

	/**
	 * POST /campaign/check_from_email
	 * Save from email and send confirmation code
	 *
	 * @param $email
	 *
	 * @return array|WP_Error
	 */
	public function set_campaigns_check_from_email( $email ) {

		return $this->send_request( 'campaigns/check_from_email/', 'POST', array(
			'from_email' => sanitize_email( $email ),
			'id'         => $this->user_id,
		) );
	}

	/**
	 * Filter
	 * campaigns data
	 */
	public function filter_campaigns_data( $data ) {

		return array_intersect_key( $data, array_flip( array(
			'id',
			'from_name',
			'from_email',
			'subject',
			'content',
			'type',
			'status',
			'list_id',
		) ) );
	}

	/**
	 * GET /emails/{id}
	 * email information
	 *
	 * @param int|null $id
	 *
	 * @return array|WP_Error
	 */
	public function get_emails( $id = null ) {
		$get_path = 'emails/';

		/** add id to path */
		if ( ! empty( $id ) ) {
			$get_path .= intval( $id );
		}

		return $this->send_request( $get_path, 'GET' );
	}

	/**
	 * POST /emails
	 * list creation
	 *
	 * @param $data
	 *
	 * @return array|WP_Error
	 */
	public function create_emails( $data ) {
		$data = $this->filter_emails_data( $data );

		return $this->send_request( 'emails/', 'POST', $data );
	}

	/**
	 * PUT /emails
	 * list update
	 *
	 * @param $data
	 *
	 * @return array|WP_Error
	 */
	public function update_emails( $data ) {
		$data = $this->filter_emails_data( $data );

		return $this->send_request( 'emails/', 'PUT', $data );
	}

	/**
	 * DELETE /emails/{id}
	 * list removal
	 *
	 * @param $id
	 *
	 * @return array|WP_Error
	 */
	public function delete_emails( $id ) {
		return $this->send_request( 'emails/' . intval( $id ), 'DELETE' );
	}

	/**
	 * Filter
	 * emails data
	 */
	public function filter_emails_data( $data ) {

		return array_intersect_key( $data, array_flip( array(
			'list_id',
			'email',
			'custom_fields',
		) ) );
	}

	/**
	 * GET /reports/list
	 * lists report
	 *
	 * @return array|WP_Error
	 */
	public function get_lists_report() {
		return $this->send_request( 'reports/list/', 'GET' );
	}

	/**
	 * GET /reports/list/{id}
	 * subscribers report
	 *
	 * @param int $id
	 *
	 * @return array|WP_Error
	 */
	public function get_subscribers_report( $id, $offset = null, $limit = null ) {
		$get_path = 'reports/list/' . intval( $id );

		return $this->send_request( $get_path, 'GET', array(
			'offset' => intval( $offset ),
			'limit'  => intval( $limit ),
		) );
	}

	/**
	 * GET /reports/campaign
	 * campaigns report
	 *
	 * @param null $date_start
	 * @param null $date_end
	 * @param null $page
	 *
	 * @return array|WP_Error
	 */
	public function get_campaigns_report( $date_start = null, $date_end = null, $page = null ) {
		$get_path = 'reports/campaigns/';

		return $this->send_request( $get_path, 'GET', array(
			'date_start' => sanitize_text_field( $date_start ),
			'date_end'   => sanitize_text_field( $date_end ),
			'page'       => intval( $page ),
		) );
	}

	/**
	 * GET /plans/{id}
	 * plans information
	 *
	 * @param null $id
	 *
	 * @return array|WP_Error
	 */
	public function get_plans( $id = null ) {
		$get_path = 'plans/';

		/** add id to path */
		if ( ! empty( $id ) ) {
			$get_path .= intval( $id );
		}

		return $this->send_request( $get_path, 'GET' );
	}

	/**
	 * GET /payments/{id}
	 * payments information
	 *
	 * @param null $id
	 *
	 * @return array|WP_Error
	 */
	public function get_payments( $id = null ) {
		$get_path = 'payments/';

		/** add id to path */
		if ( ! empty( $id ) ) {
			$get_path .= intval( $id );
		}

		return $this->send_request( $get_path, 'GET' );
	}
}
