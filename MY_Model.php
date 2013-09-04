<?php

class MY_Model extends CI_Model
{
	public $table = '';

	public function __construct()
	{
		parent::__construct();
	}

	public function get( $id )
	{
		if ( $id )
		{
			$this->db->where( 'id', $id );
		}

		return $this->db->get( $this->table );
	}

	public function insert( $put ) 
	{

		$this->db->set( $put );
		$this->_insert( TRUE ); 

		return $this->db->insert_id();
	}

	public function update( $id, $post, $return_modified_fields = FALSE ) 
	{
		$this->db->where( 'id', $id );
		$this->db->set( $post );
		if ( $this->_update( TRUE ) )
		{
			if ( !$return_modified_fields ) {
				$this->_exceptions();
				$this->_only();
			}
			else
			{
				foreach ( $post as $k => $v )
				{
					$select[] = $k;
				}
				$this->db->select( implode( ",", $select ) );
			}

			$this->db->where( 'id', $id );
			return $this->db->get( $this->table )->result()[0];
		}

		return FALSE;
	}

	public function delete( $id )
	{
		if ( is_numeric( $id ) )
		{
			$this->db->delete( $this->table, array( 'id' => $id ) );
		}
		else if ( $id == 'all')
		{
			return $this->db->empty_table( $this->table);
		}
		return FALSE;
	}

	public function __call($method, $args=array() ) {

		$splits   = explode( '__', $method );
		$function = explode( '_', $splits[0] )[0];
		$clause   = explode( '_', $splits[0] )[1];
		$field    = isset( $splits[1] ) ? $splits[1] : '';
		$arg      = isset( $args[0] ) ? $args[0] : '';
		if ( $function == 'find' )
		{
			return $this->_find( $clause, $field, $arg );
		}

	}

	private function _exceptions()
	{
		//lets see if there are exceptions made
		//on fields to select
		if ( $this->exceptions )
		{
			$exceptions = implode("','", $this->exceptions );
			$this->_fields( $exceptions, 'NOT' );
		}
	}

	private function _only()
	{
		//see if we are only selecting some fields
		if ( $this->only )
		{
			$only = implode("','", $this->only );
			$this->_fields( $only );
		}
	}

	private function _fields( $_fields, $not = '' )
	{

		$sql = 'SHOW FIELDS FROM ' . $this->table . ' WHERE FIELD ' . $not . ' IN ( \'' . $_fields . '\')'; 
		$fields = $this->db->query( $sql );

		if ($fields->num_rows())
		{
			$result = $fields->result();
			foreach( $result as $field )
			{
				$f[] = $field->Field;
			}
		}
		$this->db->select( implode(", ", $f ) );
		
	}

	private function _find( $clause, $field, $args )
	{

		//run exceptions first
		$this->_exceptions();
		$this->_only();

		//see what we are searching by
		switch ( $clause )
		{
			case 'by':
				$this->db->where( $field, $args );
			break;
			
			case 'in':
				$this->db->where_in( $field, $args );
			break;
			case 'gt':
				$this->db->where( $field . ' >', $args );
			break;
			case 'lt':
				$this->db->where( $field . ' <', $args );
			break;

			//no clause for all
			case 'all':
			default:
		}

		$result = $this->db->get( $this->table );

		//if we only have 1 row we'll return just the single element
		if ( $result->num_rows == 1) 
		{
			return $result->result()[0];
		}

		return $result->result();
	}



	private function _insert( $created_at = FALSE )
	{

		$now = date('Y-m-d H:i:s');

		if ( $created_at && $this->db->field_exists( 'created_at', $this->table ) ) 
		{
			$this->db->set( 'created_at', $now );
		}
		if ( $this->db->field_exists( 'updated_at', $this->table ) ) 
		{
			$this->db->set( 'updated_at', $now );
		}


		return $this->db->insert( $this->table );

		//create the sub id if needed
		if ( $this->db->field_exists( 'sub_id', $this->table ) )
		{
			$sub_id = $this->table . '_' . $this->db->insert_id();
			$this->db->set( 'sub_id', $sub_id );
			$this->_update( $this->table );
		}
	}

	private function _update(  )
	{
		$now = date('Y-m-d H:i:s');
		if ( $this->db->field_exists( 'updated_at', $this->table ) ) 
		{
			$this->db->set( 'updated_at', $now );
		}

		return $this->db->update( $this->table );
	}




}