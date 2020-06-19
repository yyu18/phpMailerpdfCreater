<?php

class Campaign_Service {
	const Meta_Type_Select = [ 'radio', "checkbox" ];

	private $db_service;

	private
	function __construct() {
		$this->db_service = DB_Service::factory();
		date_default_timezone_set( 'Asia/Hong_Kong' );
	}

	public static
	function factory() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new self();
		}

		return $instance;
	}

	public
	function get_campaign( $camp_id ) {
		$sql = 'SELECT * FROM campaign c where c.c_id = ' . $camp_id;		
		return $this->db_service->fetch_object( $sql );
	}

	public
	function get_campaigns( $user_id ) {
		if (isset($_SESSION['usercampids']) && !empty($_SESSION['usercampids'])){
			
			$sql = 'SELECT c.*, (SELECT count(1) FROM user_email u where u.c_id = c.c_id) as records'
				. ' FROM campaign c '
				. ' WHERE c.c_id in ('.$_SESSION['usercampids'].') '
				. ' order by c.c_id desc';			
		} else {
			$sql = 'SELECT c.*, (SELECT count(1) FROM user_email u where u.c_id = c.c_id) as records'
				. ' FROM campaign c order by c.c_id desc';
		}
		
		
		return $this->db_service->fetch_all( $sql );
	}

	public
	function get_campaign_meta( $camp_id, $format ) {
		switch ( strtolower( $format ) ) {
			case 'json':
				return $this->get_campaign_meta_json( $camp_id );
			default:
				return $this->get_campaign_meta_plain( $camp_id );
		}
	}

	public
	function get_campaign_system_meta( $camp_id, $meta_key ) {
		$sql = 'select * from campaign_meta where type = \'system\' and meta_key= \'' . $meta_key . '\' and c_id = ' . $camp_id . ' limit 1';
		$rows = $this->db_service->fetch_all( $sql );
		if ( count( $rows ) > 0 && isset( $rows[ 0 ]->option_value ) && !empty( $rows[ 0 ]->option_value ) )
			return ( object )array( 'cm_id' => $rows[ 0 ]->cm_id, 'option_value' => json_decode( $rows[ 0 ]->option_value ) );
		else
			return false;
	}

	private
	function get_campaign_meta_json( $camp_id ) {
		$sql = 'select * from campaign_meta where c_id = ' . $camp_id.' order by option_order';
		$rows = $this->db_service->fetch_all( $sql );

		$meta = array_map( function ( $row ) {
			return $this->parse_campaign_meta_json( $row );
		}, $rows );

		return $meta;
	}



	private
	function parse_campaign_meta_json( $row ) {
		$item = ( object )[
			'cm_id' => $row->cm_id,
			'key' => $row->meta_key,
			'ch_desc' => $row->ch_desc,
			'en_desc' => $row->en_desc,
			'option_value' => $row->option_value,
			'option_order' => $row->option_order,
			'type' => $row->type,
			'options' => [],
			'label' => '',
		];

		if ( !empty( $row->option_value ) ) {
			
			if ($value = json_decode( $row->option_value )){	
				$item->label = $value->question;
				$item->options = $value->answers;
			}
		}
		return $item;
	}

	private
	function get_campaign_meta_plain( $camp_id ) {
		$sql = 'select * from campaign_meta where c_id = ' . $camp_id.' order by option_order';
		$rows = $this->db_service->fetch_all( $sql );

		$keys = array_map( function ( $row ) {
			return $this->rtrim_other_suffix( $row->meta_key );
		}, $rows );
		$keys = array_values( array_unique( $keys ) );

		$meta = [];
		foreach ( $keys as $key ) {
			$options = array_filter( $rows, function ( $row )use( $key ) {
				return $this->rtrim_other_suffix( $row->meta_key ) == $key;
			} );
			$options = array_values( $options );

			foreach ( $options as $option ) {
				$option->label = '';
				$option->value = '';

				if ( !empty( $option->option_value ) ) {
					$parts = explode( '=>', $option->option_value );
					$option->label = $parts[ 0 ];

					if ( count( $parts ) > 1 ) {
						$option->value = $parts[ 1 ];
					}
				}
			}

			$item = ( object )[
				'key' => $key,
				'ch_desc' => $options[ 0 ]->ch_desc,
				'en_desc' => $options[ 0 ]->en_desc,
				'type' => $options[ 0 ]->type,
				'label' => $options[ 0 ]->label,
			];

			$item->options = array_map( function ( $option ) {
				return ( object )[
					'cm_id' => $option->cm_id,
					'value' => $option->value,
					'type' => $option->type,
					'order' => $option->option_number,
				];
			}, $options );

			$meta[] = $item;
		}
		return $meta;
	}

	private
	function rtrim_other_suffix( $key ) {
		$needle = 'Other';
		if ( strcmp( substr( $key, strlen( $key ) - strlen( $needle ) ), $needle ) === 0 ) {
			return substr( $key, 0, strlen( $key ) - strlen( $needle ) );
		}
		return $key;
	}

	public
	function create_update_device_token( $data ) {
				
		$sql = 'select token_id from app_device_token where os = \'' . $data[ 'os' ] . '\' and cookie= \'' . $data[ 'cookie' ] . '\' order by last_update DESC limit 1 ';
		$token = $this->db_service->fetch_all( $sql );
		
		if (is_array($token) && isset($token[0]->token_id)){
			$sql = 'UPDATE app_device_token SET device_token=\'' . $data[ 'token' ] . '\', tags=\'' . $data[ 'tags' ] . '\', last_update=CURRENT_TIMESTAMP WHERE token_id=' . $token[0]->token_id ;
		} else {		
			$sql = 'INSERT INTO app_device_token (os, cookie, device_token, tags) VALUES (\'' . $data[ 'os' ] . '\', \'' . $data[ 'cookie' ] . '\', \'' . $data[ 'token' ] . '\', \'' . $data[ 'tags' ] . '\')';
		}
		
		return $this->db_service->fetch_sql( $sql );
	}
	
	public
	function create_campaign( $camp_obj ) {
		$sql = 'INSERT INTO campaign (c_location, c_pin, admin_email, c_pw, cookie_inputId, format, c_title, c_domain, start_date, end_date, max_coupons ) 
			  VALUES (\'' . $camp_obj[ 'c_location' ] . '\', \'' . $camp_obj[ 'c_pin' ] . '\', \'' . $camp_obj[ 'admin_email' ] . '\', \'' . $camp_obj[ 'c_pw' ] . '\', \'' . $camp_obj[ 'cookie_inputId' ] . '\',  \'' . $camp_obj[ 'format' ] . '\', \'' . $camp_obj[ 'c_title' ] . '\', \'' . $camp_obj[ 'c_domain' ] . '\', \'' . $camp_obj[ 'start_date' ] . '\', \'' . $camp_obj[ 'end_date' ] . '\', \'' . $camp_obj[ 'max_coupons' ] . '\')';
		
		$create_result=$this->db_service->fetch_sql( $sql );
	
		if ($create_result === true){
			$sql = 'SELECT c_id FROM campaign WHERE c_pw=\'' . $camp_obj[ 'c_pw' ] .'\' ORDER BY c_id desc LIMIT 1';	
			$c_id = $this->db_service->fetch_all( $sql );
			
			if (isset($_SESSION['usercampids']) && !empty($_SESSION['usercampids'])){
				$new_ids=explode(',',$_SESSION['usercampids']);
				$new_ids[]=$c_id[0]->c_id;
				
				$sql='UPDATE users SET belong_campaign_id=\'' . implode(',',$new_ids) . '\' WHERE user_id=' . $_SESSION[ 'user_id' ];
				$_SESSION['usercampids'] = implode(',',$new_ids);
				return $this->db_service->fetch_sql( $sql );
			} else {
				return true;
			}
			
		} else {
			return $create_result;
		}
	}


	public
	function update_campaign( $camp_obj ) {
		$sql = 'UPDATE campaign 
			  SET c_location=\'' . $camp_obj[ 'c_location' ] . '\', 
			  	  c_pin=\'' . $camp_obj[ 'c_pin' ] . '\', 
				  admin_email=\'' . $camp_obj[ 'admin_email' ] . '\', 
				  c_pw=\'' . $camp_obj[ 'c_pw' ] . '\', 
				  cookie_inputId=\'' . $camp_obj[ 'cookie_inputId' ] . '\', 
				  format=\'' . $camp_obj[ 'format' ] . '\', 
				  c_title=\'' . $camp_obj[ 'c_title' ] . '\', 
				  c_domain=\'' . $camp_obj[ 'c_domain' ] . '\', 
				  start_date=\'' . $camp_obj[ 'start_date' ] . '\', 
				  end_date=\'' . $camp_obj[ 'end_date' ] . '\', 
				  max_coupons=\'' . $camp_obj[ 'max_coupons' ] . '\'
			  WHERE c_id=\'' . $camp_obj[ 'c_id' ] . '\'';

		return $this->db_service->fetch_sql( $sql );
	}
	
	public
	function save_campaign_meta( $c_id, $cm_id, $data ){
		
		if ( $cm_id > 0 ){
			$sql = 'UPDATE campaign_meta 
				  SET meta_key=\'' . $data[ 'meta_key' ] . '\', 
					  ch_desc=\'' . $data[ 'ch_desc' ] . '\', 
					  en_desc=\'' . $data[ 'en_desc' ] . '\', 
					  type=\'' . $data[ 'type' ] . '\', 
					  option_value=\'' . $data[ 'option_value' ] . '\'
				  WHERE c_id=' . $c_id . ' AND cm_id='.$cm_id;
		} else {
			$sql = 'INSERT INTO campaign_meta (c_id, meta_key, ch_desc, en_desc, type, option_value, option_order ) 
			  VALUES (\'' . $c_id . '\', \'' . $data[ 'meta_key' ] . '\', \'' . $data[ 'ch_desc' ] . '\', \'' . $data[ 'en_desc' ] . '\', \'' . $data[ 'type' ] . '\',  \'' . $data[ 'option_value' ] . '\',  \'' . $data[ 'option_order' ] . '\')';
		}
		
		return $this->db_service->fetch_sql( $sql );
	}
	
	
	public
	function update_campaign_meta_order( $c_id, $cm_id, $from, $to ){
		
		if ( $c_id > 0 && $to > 0 ){
			$sql = 'UPDATE campaign_meta 
				  SET option_order='. $from . '
				  WHERE c_id='.$c_id.' AND option_order='.$to;
		} 
		
		
		if ( $result=$this->db_service->fetch_sql( $sql ) ){			
			if ( $cm_id > 0 && $to > 0 ){
				$sql = 'UPDATE campaign_meta 
					  SET option_order=' . $to . '
					  WHERE c_id='.$c_id.' AND cm_id='.$cm_id;
			} 			
		} else {
			return $result;
		}
		
		
		return $this->db_service->fetch_sql( $sql );
	}
	
	public
	function delete_campaign_meta( $cm_id ){		
		$sql = 'DELETE FROM campaign_meta WHERE cm_id=' . $cm_id;		
		return $this->db_service->fetch_sql( $sql );
	}
	
	
	public
	function get_campaign_meta_for_question( $cm_id ) {
		$sql = 'SELECT * FROM campaign_meta WHERE cm_id=' . $cm_id;	
		$rows = $this->db_service->fetch_all( $sql );

		$meta = array_map( function ( $row ) {
			return $this->parse_campaign_meta_json( $row );
		}, $rows );
		
		if (isset($meta[0]))
			return $meta[0];
		else 
			return 'error found in parse campaign meta json';
	}

	public
	function delete_campaign( $camp_id ) {
		$sql = 'DELETE FROM campaign_meta WHERE c_id=' . $camp_id;
		$delete_meta = $this->db_service->fetch_sql( $sql );

		if ( $delete_meta ) {
			$sql = 'DELETE FROM campaign WHERE c_id=' . $camp_id;
			return $this->db_service->fetch_sql( $sql );

		} else {
			return $delete_meta;
		}
	}
	
	public
	function get_campaign_user_by_id( $user_id ) {
		$sql = 'select * from user_email where user_id = ' . $user_id ;
		
		$rows = $this->db_service->fetch_all( $sql );
		
		$users = array_map( function ( $row ) {
			return ( object )[
				'user_id' => $row->user_id,
				'reg_date' => $this->convert_local_timezone( $row->reg_date ),
				'email' => $row->email,
				'cookie' => $row->cookie,
				'ip_address' => $row->ip_address,
				'agreement' => $row->agreement,
			];
		}, $rows );

		return $users[0];
	}
	
	public
	function get_campaign_users( $data, $match_email = true, $match_cookie = true ) {


		if ( isset( $data[ 'id' ] ) )
			$sql = 'select * from user_email where c_id = \'' . $data[ 'id' ] . '\' ';
		elseif ( isset( $data[ 'cookie' ] ) )
			$sql = 'select * from user_email where cookie = \'' . $data[ 'cookie' ] . '\' ';
		elseif ( isset( $data[ 'email' ] ) )
			$sql = 'select * from user_email where email = \'' . $data[ 'email' ] . '\' ';
		else
			return [];

		if ( $match_email )
			$sql .= ' and email = \'' . $data[ 'email' ] . '\' ';

		if ( $match_cookie )
			$sql .= ' and cookie= \'' . $data[ 'cookie' ] . '\' ';
		
		$rows = $this->db_service->fetch_all( $sql );

		$users = array_map( function ( $row ) {
			return ( object )[
				'user_id' => $row->user_id,
				'reg_date' => $this->convert_local_timezone( $row->reg_date ),
				'email' => $row->email,
				'cookie' => $row->cookie,
				'ip_address' => $row->ip_address,
				'agreement' => $row->agreement,
			];
		}, $rows );

		return $users;
	}

	public
	function create_user_email_record( $data ) {

		$user = $this->get_campaign_users( $data );
		if ( isset( $user[ 0 ] ) && isset( $user[ 0 ]->user_id ) )
			return $user[ 0 ]->user_id;

		$sql = 'INSERT INTO user_email (c_id, email, cookie, agreement, ip_address ) 
			  VALUES (\'' . $data[ 'id' ] . '\', \'' . $data[ 'email' ] . '\', \'' . $data[ 'cookie' ] . '\', \'' . $data[ 'agreement' ] . '\', \'' . $data[ 'ip_address' ] . '\')';
		$insert = $this->db_service->fetch_sql( $sql );

		if ( $insert ) {
			$user = $this->get_campaign_users( $data );
			return $user[ 0 ]->user_id;
		}
		return $insert;
	}
	
	public 
	function update_user_email($user_id, $camp_id, $email, $ip_address){		
		$sql = 'UPDATE user_email 
				SET email=\'' . $email . '\',
					ip_address=\'' . $ip_address . '\' 
				WHERE c_id='.$camp_id.' AND user_id='.$user_id.'; ';
		
		return $this->db_service->fetch_sql( $sql );
	}

	public
	function create_voucher( $data ) {
		if ($this->create_voucher_record( $data ) == true){
			return $this->get_voucher_records( $data[ 'user_id' ], $data[ 'c_id' ], 0, true );
		}
		
		return false;		
	}

	public
	function delete_voucher( $c_id, $cv_id ) {
		$sql = 'SELECT cvr_id FROM campaign_voucher_records WHERE cv_id='.$cv_id.'; ';
		$rows = $this->db_service->fetch_sql( $sql );
		
		if (isset($rows->num_rows) && $rows->num_rows > 0 ){
			return (object)[
						'message' => 'The voucher has been issued to '. $rows->num_rows .' users, it can not be deleted.',
					];
		} else {
			$sql = 'DELETE FROM campaign_voucher WHERE cv_id='.$cv_id;
		}
		return $this->db_service->fetch_sql( $sql );
	}
	
	private
	function create_voucher_record( $data ) {
		$sql = 'INSERT INTO campaign_voucher_records (user_id, cv_id, voucher_code, voucher_qr_code ) 
			  VALUES (\'' . $data[ 'user_id' ] . '\', \'' . $data[ 'cv_id' ] . '\', \'' . $data[ 'voucher_code' ] . '\', \'' . $data[ 'voucher_qr_code' ] . '\')';
		return $this->db_service->fetch_sql( $sql );
	}
	
	public
	function update_voucher_last_visit ($cvr_id){
		$sql = 'UPDATE campaign_voucher_records 
				SET  last_visit=\'' . date('Y-m-d H:i:s') . '\'
				WHERE cvr_id='.$cvr_id.'; ';
		
		return $this->db_service->fetch_sql( $sql );
	}
	
	public
	function get_voucher_records( $user_id, $camp_id=0, $cv_id=0, $size=20, $page=0, $type='all') {
		
		$sql = 'SELECT cv.c_id camp_id, 
					   cvr.user_id user_id,
					   cv.cv_id voucher_id,
					   cvr.cvr_id voucher_record_id,
					   cv.voucher_startdate voucher_startdate,
					   cv.voucher_enddate voucher_enddate,
					   cv.voucher_link voucher_link,
					   cv.voucher_full_img voucher_full_img,
					   cv.voucher_feature_img voucher_feature_img,
					   cv.voucher_desc voucher_desc,
					   cvr.voucher_code voucher_code,
					   cvr.voucher_qr_code voucher_qr_code,
					   cvr.reg_date reg_date 
				FROM campaign_voucher cv, campaign_voucher_records cvr
				WHERE cv.cv_id=cvr.cv_id AND cvr.user_id = '. $user_id;
		
		if ( $camp_id > 0 ) 
			$sql .= ' AND cv.c_id = ' . $camp_id;	
		
		if ( $cv_id > 0 ) 
			$sql .= ' AND cvr.cvr_id = ' . $cv_id;	
		
		if ( $type !== 'all' ) {
			
			date_default_timezone_set( 'Asia/Hong_Kong' );
			switch ($type){
				case 'valid':
					$sql .= ' AND cv.voucher_enddate >= \'' . date('Y-m-d') .'\'';	
					break;
					
				case 'expired':					
					$sql .= ' AND cv.voucher_enddate < \'' . date('Y-m-d') .'\'';
					break;
			}
		}
		
		$sql .= ' ORDER BY cvr.reg_date DESC LIMIT '.$size. ' OFFSET '. ($page*$size);
		
		$rows = $this->db_service->fetch_all( $sql );		
		$voucher_records=[];
		foreach ($rows as $row){
			$voucher_records[]=(object)array(
									'voucher_id' => $row->voucher_record_id,
									'reg_date' => $this->convert_local_timezone( $row->reg_date ),
									'campaign' => (object)array(
													'c_id' => $row->camp_id,
													'voucher_startdate' => $row->voucher_startdate,
													'voucher_enddate' => $row->voucher_enddate,
													'voucher_link' => str_replace(array('[voucher_id]','[coupon_id]'),$row->voucher_record_id,$row->voucher_link),
													'voucher_full_img' => $row->voucher_full_img,
													'voucher_feature_img' => $row->voucher_feature_img,
													'voucher_desc' => $row->voucher_desc,
												 ),
									'voucher' => (object)array(
													'voucher_code' => $row->voucher_code,
													'voucher_qr_code' => $row->voucher_qr_code,
												 ),
								);
		}		
		
		return $voucher_records;
	}


	public
	function get_campaign_voucher( $c_id ){
		$sql = 'select * from campaign_voucher where c_id = ' . $c_id . ' order by last_update DESC limit 1 ';
		return $this->db_service->fetch_all( $sql );		
	}
	
	public
	function update_campaign_voucher( $c_id, $cv_id=0, $data ) {
		
		if ($cv_id==0 || empty($cv_id)) {		
			$sql = 'INSERT INTO campaign_voucher (c_id, voucher_startdate, voucher_enddate, voucher_link, voucher_full_img, voucher_feature_img, unique_email, unique_cookie, voucher_desc) 
			  VALUES (' . $c_id . ', 
			  		  \'' . $data[ 'voucher_startdate' ] . '\', 
			  		  \'' . $data[ 'voucher_enddate' ] . '\', 
					  \'' . $data[ 'voucher_link' ] . '\', 
			  		  \'' . $data[ 'voucher_full_img' ] . '\', 
					  \'' . $data[ 'voucher_feature_img' ] . '\', 
			  		  \'' . $data[ 'unique_email' ] . '\', 
			  		  \'' . $data[ 'unique_cookie' ] . '\', 
					  \'' . $data[ 'voucher_desc' ] . '\'); ';
		} elseif ($cv_id > 0) {
			
			$sql = 'UPDATE campaign_voucher 
					SET voucher_startdate=\'' . $data[ 'voucher_startdate' ] . '\',
						voucher_enddate=\'' . $data[ 'voucher_enddate' ] . '\',
						voucher_link=\'' . $data[ 'voucher_link' ] . '\',
						voucher_full_img=\'' . $data[ 'voucher_full_img' ] . '\',
						voucher_feature_img=\'' . $data[ 'voucher_feature_img' ] . '\',
						unique_email=\'' . $data[ 'unique_email' ] . '\',
						unique_cookie=\'' . $data[ 'unique_cookie' ] . '\',
						voucher_desc=\'' . $data[ 'voucher_desc' ] . '\' 
					WHERE cv_id='.$cv_id.' AND c_id='.$c_id.'; ';
		}
		
		
		return $this->db_service->fetch_sql( $sql );
		
	}
	
	public
	function remove_user_campaign_meta($user_id, $camp_id){
		$sql = 'DELETE FROM usermeta_record WHERE user_id='.$user_id.' AND c_id='.$camp_id;	
		return $this->db_service->fetch_sql( $sql );
	}
	
	public
	function add_user_campaign_meta($user_id, $camp_id, $meta_key, $value){
		$sql = 'select cm_id FROM campaign_meta WHERE meta_key=\''.$meta_key.'\' AND c_id='.$camp_id;		
		$camp_meta = $this->db_service->fetch_all( $sql );
		if (isset($camp_meta[0]->cm_id) && !empty($camp_meta[0]->cm_id)){
			return $this->create_user_campaign_meta($camp_meta[0]->cm_id, $camp_id, $user_id, $value);						
		}
	}
	
	private 
	function create_user_campaign_meta($cm_id, $c_id, $user_id, $value){
		
		$special_chars=array(
			array("'","\'"),
			);
		
		foreach ($special_chars as $special_char){
			$value=str_replace($special_char[0],$special_char[1], $value);			
		}
		
		$sql = 'INSERT INTO usermeta_record (cm_id, c_id, user_id, value) 
			  VALUES (' . $cm_id . ', 
			  		  ' . $c_id . ',
			  		  ' . $user_id . ',
			  		  \'' . $value . '\'); ';
		return $this->db_service->fetch_sql( $sql );
	}

	private
	function get_usermeta_records( $user_id, $camp_id=0, $cr_id=0, $latest=false ) {
		
		if ($camp_id > 0) {
			$sql = 'select * from usermeta_record where c_id = ' . $camp_id . ' and user_id =' . $user_id;
		} else {
			$sql = 'select * from usermeta_record where user_id =' . $user_id;			
		}
		
		
		if ($cr_id > 0)
			$sql .= ' and cr_id='.$cr_id;
		
		if ($latest)
			$sql .= ' order by cr_id desc limit 1';
		else 			
			$sql .= ' order by cr_id asc limit 1000';
		
		$rows = $this->db_service->fetch_all( $sql );

		$user_meta = array_filter( $rows, function ( $row ) {
				return $row->value !== '';
			} );
		
		return $user_meta;

	}
	

	public
	function get_campaign_records( $camp_id, $format ) {
		$data = $this->get_campaign_records_paged( $camp_id, $format, null, null );
		return $data->records;
	}

	
	public
	function get_campaign_records_by_user( $camp_id, $user_id ) {
		
		$sql = 'select count(cv_id) from campaign_voucher where c_id = ' . $camp_id;
		$has_voucher = $this->db_service->fetch_scalar( $sql );

		if ($has_voucher)
			$sql = 'select ue.*, cvr.voucher_code, cvr.reg_date voucher_reg_date from user_email ue 
				inner join campaign_voucher_records cvr on ue.user_id=cvr.user_id 
				where ue.c_id = ' . $camp_id . ' order by ue.user_id desc';
		else
			$sql = 'select ue.* from user_email ue 
				where ue.c_id = ' . $camp_id . ' order by ue.user_id desc';
		

		$sql = 'select * from usermeta_record where c_id = ' . $camp_id . ' and user_id='.$user_id;
		$meta = $this->db_service->fetch_all( $sql );
		

		$user_meta = array_filter( $meta, function ( $row ) {
				return $row->value !== '';
			} );
		
		return $user_meta;
	}

	
	public
	function get_campaign_records_paged( $camp_id, $format, $page, $size ) {
		$sql = 'select count(1) from user_email where c_id = ' . $camp_id;
		$total = $this->db_service->fetch_scalar( $sql );
		
		
		$sql = 'select count(cv_id) from campaign_voucher where c_id = ' . $camp_id;
		$has_voucher = $this->db_service->fetch_scalar( $sql );

		
		if ($has_voucher)
			$sql = 'select ue.*, cvr.voucher_code, cvr.reg_date voucher_reg_date from user_email ue 
				inner join campaign_voucher_records cvr on ue.user_id=cvr.user_id 
				where ue.c_id = ' . $camp_id . ' order by ue.user_id desc';
		else
			$sql = 'select ue.* from user_email ue 
				where ue.c_id = ' . $camp_id . ' order by ue.user_id desc';
		
		if ( !empty( $page ) && !empty( $size ) ) {
			$sql = $sql . ' limit ' . ( $page - 1 ) * $size . ', ' . $size;
		}
		
		$users = $this->db_service->fetch_all( $sql );
		if ( empty( $users ) ) {
			return ( object )[
				'total' => $total,
				'page' => $page,
				'size' => $size,
				'records' => [],
			];;
		}

		$user_ids = array_column( $users, 'user_id' );
		$sql = 'select * from usermeta_record where c_id = ' . $camp_id . ' and user_id in (' . implode( ',', $user_ids ) . ')';
		$meta = $this->db_service->fetch_all( $sql );
		

		foreach ( $users as $user ) {
			$user->reg_date = $this->convert_local_timezone( $user->reg_date );
			$user->agreement = empty( $user->agreement ) ? 'true' : $user->agreement;

			$user_meta = array_filter( $meta, function ( $row )use( $user ) {
				return $row->user_id == $user->user_id && $row->value !== '';
			} );

			foreach ( $user_meta as $umeta ) {
				$this->parse_user_meta_value( $format, $umeta );
			}

			$user->meta = array_values( $user_meta );
		}

		return ( object )[
			'total' => $total,
			'page' => $page,
			'size' => $size,
			'records' => $users,
		];
	}

	private
	function convert_local_timezone( $date ) {
		$dt = new DateTime( $date, new DateTimeZone( 'UTC' ) );
		$dt->setTimezone( new DateTimeZone( date_default_timezone_get() ) );
		return $dt->format( 'Y-m-d H:i:s' );
	}

	private
	function parse_user_meta_value( $format, $umeta ) {
		switch ( strtolower( $format ) ) {
			case 'json':
				$this->parse_user_meta_value_json( $umeta );
				break;
			default:
				$this->parse_user_meta_value_plain( $umeta );
				break;
		}
	}

	private
	function parse_user_meta_value_json( $umeta ) {
		if ( empty( $umeta->value ) ) {
			return;
		}

		$json = json_decode( $umeta->value );
		if ( json_last_error() != JSON_ERROR_NONE ) {
			return;
		}

		if ( !empty( $json->value ) ) {
			$umeta->value = $json->value;
		}

		if ( !empty( $json->append_text ) ) {
			//$umeta->append_text = $json->append_text;
			$umeta->value = ($json->value).','.($json->append_text);
			//$umeta->value = ($json->append_text);
		}
	}

	private
	function parse_user_meta_value_plain( $umeta ) {
		if ( empty( $umeta->value ) ) {
			return;
		}

		$parts = explode( '=>', $umeta->value );
		if ( count( $parts ) > 1 ) {
			$umeta->value = $parts[ 1 ];
		}
	}

	public
	function get_campaign_option_records( $camp_id, $format, $key, $cm_id, $value ) {
		$data = $this->get_campaign_option_records_paged( $camp_id, $format, $key, $cm_id, $value, null, null );
		return $data->records;
	}

	public
	function get_campaign_option_records_paged( $camp_id, $format, $key, $cm_id, $value, $page, $size ) {
		switch ( strtolower( $format ) ) {
			case 'json':
				return $this->get_campaign_option_records_paged_json( $camp_id, $cm_id, $value, $page, $size );
			default:
				return $this->get_campaign_option_records_paged_plain( $camp_id, $key, $cm_id, $page, $size );
		}
	}

	private
	function get_campaign_option_records_paged_json( $camp_id, $cm_id, $value, $page, $size ) {
		$sql = $this->sql_campaign_option_records_json( $camp_id, $cm_id, $value );
		$users = $this->db_service->fetch_all( $sql );

		$records = [];
		foreach ( $users as $item ) {
			$item->reg_date = $this->convert_local_timezone( $item->reg_date );
			$item->agreement = empty( $item->agreement ) ? 'true' : $item->agreement;
			$this->parse_user_meta_value_json( $item );

			if ( is_array( $item->value ) ) {
				foreach ( $item->value as $opt_value ) {
					if ( empty( $value ) || $opt_value == $value ) {
						$records[] = [
							'cr_id' => $item->cr_id,
							'cm_id' => $item->cm_id,
							'c_id' => $item->c_id,
							'reg_date' => $item->reg_date,
							'value' => $opt_value,
							'user_id' => $item->user_id,
							'email' => $item->email,
							'cookie' => $item->cookie,
							'agreement' => $item->agreement,
						];
					}
				}
			} else {
				$records[] = $item;
			}
		}

		$total = count( $records );
		if ( !empty( $page ) && !empty( $size ) ) {
			$records = array_slice( $records, ( $page - 1 ) * $size, $size );
		}

		return ( object )[
			'total' => $total,
			'page' => $page,
			'size' => $size,
			'records' => $records,
		];
	}

	private
	function sql_campaign_option_records_json( $camp_id, $cm_id, $value ) {
		$sql = 'select um.*, u.email, u.cookie from usermeta_record um'
			. ' inner join user_email u on um.user_id = u.user_id'
		. ' where um.c_id = ' . $camp_id . ' and um.cm_id = ' . $cm_id
			. " and um.value is not null and um.value <> ''";

		if ( !empty( $value ) ) {
			$sql = $sql . " and um.value like '%\"$value\"%'";
		}

		$sql = $sql . ' order by um.user_id desc';
		return $sql;
	}

	private
	function get_campaign_option_records_paged_plain( $camp_id, $key, $cm_id, $page, $size ) {
		if ( empty( $key ) ) {
			$cm_ids = ( array )$cm_id;
		} else {
			$sql = "select * from campaign_meta where c_id = $camp_id and meta_key = '$key'";
			$rows = $this->db_service->fetch_all( $sql );
			$cm_ids = array_column( $rows, 'cm_id' );
		}

		$sql = $this->sql_campaign_option_records_total_plain( $camp_id, $cm_ids );
		$total = $this->db_service->fetch_scalar( $sql );

		$sql = $this->sql_campaign_option_records_plain( $camp_id, $cm_ids );
		if ( !empty( $page ) && !empty( $size ) ) {
			$sql = $sql . ' limit ' . ( $page - 1 ) * $size . ', ' . $size;
		}
		$records = $this->db_service->fetch_all( $sql );

		foreach ( $records as $item ) {
			$item->reg_date = $this->convert_local_timezone( $item->reg_date );
			$item->agreement = empty( $item->agreement ) ? 'true' : $item->agreement;
			$this->parse_user_meta_value_plain( $item );
		}

		return ( object )[
			'total' => $total,
			'page' => $page,
			'size' => $size,
			'records' => $records,
		];
	}

	private
	function sql_campaign_option_records_total_plain( $camp_id, $cm_ids ) {
		return 'select count(1) from usermeta_record um'
			. ' inner join campaign_meta cm on um.c_id = cm.c_id and um.cm_id = cm.cm_id'
		. ' where um.c_id = ' . $camp_id . ' and um.cm_id in (' . implode( ',', $cm_ids ) . ')'
		. " and um.value is not null and um.value <> ''"
		. " and (cm.type not in ('radio', 'checkbox') or locate(um.value, cm.option_value))";
	}

	private
	function sql_campaign_option_records_plain( $camp_id, $cm_ids ) {
		return 'select um.*, u.email, u.cookie from usermeta_record um'
			. ' inner join campaign_meta cm on um.c_id = cm.c_id and um.cm_id = cm.cm_id'
		. ' inner join user_email u on um.user_id = u.user_id'
		. ' where um.c_id = ' . $camp_id . ' and um.cm_id in (' . implode( ',', $cm_ids ) . ')'
		. " and um.value is not null and um.value <> ''"
		. " and (cm.type not in ('radio', 'checkbox') or locate(um.value, cm.option_value))"
		. ' order by um.user_id desc';
	}
}

Campaign_Service::factory();