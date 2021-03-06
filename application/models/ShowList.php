<?php

class ShowList extends MY_Model {

	//array of show objects
	public $id, $showList, $watchingList;


	/*---------------------------------*/
	/*---- SINGLETON DESIGN PATTERN ----*/
	/*---------------------------------*/

	//class instance
	private static $instance = null;

	//private constructor
	private
	function __construct( $id = '' ) {
		/*if($this->showList){
			// if this object is json from database, convert to object
			$this->showList = json_decode($this->showList);
		}*/

		/*if(empty($this->showList)){
			$this->id = '';
			$this->showList = array(new Show());
		}*/

		//initialize arrays
		$this->showList = array();
		$this->watchingList = array();

		//if title passed, load it
		if ( $id ) {
			$this->load( $id );
		}
	}


	public static
	function & getInstance( $id = '' ) {
		if ( self::$instance == null ) {
			self::$instance = new ShowList( $id );
		}

		return self::$instance;
	}

	

	/*---------------------------------------------*/
	/*----- methods to manage this collection -----*/
	/*---------------------------------------------*/

	public
	function load( $id ) {
		$row = $this->db->get_where( 'investv', array( 'id' => $id ) )->row();
		$this->id = $row->id;
		//		$this->showList = json_decode($row->showList);
		$showListObject = json_decode( $row->showList );
		$watchingListObject = json_decode( $row->watchingList );
		//$removedList = json_decode( $row->removedList );


		//TODO: Remove this because JSON objects are now stored as show objects in DB
		/*$this->shows = array();
		
		if ($this->showList){
			foreach ($this->showList as $o){
				$show = new Show();
				foreach($o as $key => $val){
					$show->{lcfirst($key)} = $val;
				}
				$this->shows[] = $show;
			}
		} else {
			$this->showList = array();
		}*/

		//$this->showList = array();

		if ( $showListObject ) {
			foreach ( $showListObject as $o ) {

				$show = new Show( $o );

				array_push( $this->showList, $show );
				//$this->showList = array(new Show($o));
			}
		} /*else {
			$this->showList = array();
		}*/


		//$this->watchingList = array();

		if ( $watchingListObject ) {
			foreach ( $watchingListObject as $o ) {

				$show = new Show( $o );

				array_push( $this->watchingList, $show );
				//$this->showList = array(new Show($o));
			}
		} /*else {
			$this->showList = array();
		}*/

		//$this->object = $row->object;
	}

	public
	function save() {
		// do some validation here



		if ( $this->id ) {
			// update
			//connect
			$db = self::db();

			// Check connection
			/*if ($db->connect_error) {
				die("Connection failed: " . $db->connect_error);
			} */

			$sql = "UPDATE investv SET showList=?, watchingList=? WHERE id=1";

			if ( $db->query( $sql, [ json_encode( $this->showList), json_encode($this->watchingList ) ] ) === TRUE ) {
				echo "Record updated successfully";
			} else {
				echo "Error updating record: " . $db->error;
			}
			//implode($this->showList)


			/*if ($db->query($sql,[$e, $id]) === TRUE) {
				echo "Record updated successfully";
			} else {
				echo "Error updating record: " . $db->error;
			}*/

			/*$sql = "UPDATE investv SET episode=2 WHERE id=8";
			$db->query($sql);*/


			//$db->close();
		} else {
			// save
			$data = array(
				/*'title' => $this->title,
				'imdbID' => $this->imdbID,
				'poster' => $this->poster,
				'episode' => $this->episode,
				'totalEpisodes' => $this->totalEpisodes,*/
				'showList' => json_encode( $this->showList ) );

			$this->db->insert( 'investv', $data );
		}

	}

	public
	function delete( Show $showObject) {
		/*if($this->id){
			// delete
			$this->db->delete('investv', array('id' => $this->id));
		}*/

		$showKey = 0;
		//find object in array
		foreach ( $this->showList as $show ) {
			//match title

			if ( $show->Title === $showObject->Title ) {
				//remove show from showList
				//$element = $showKey;
				unset( $this->showList[ $showKey ] );
			}
			$showKey++;

		}
	}

	public
	function addShow( Show $showObject ) {
		//flag if adding succeeded
		$adding = false;
		$duplicate = false;
		

		//check if list is not empty
		if ( $this->showList && $this->watchingList) {
			
			
			//check if show already exists in list
			foreach ( $this->showList as $show ) {
				//match title
				if ( $show->Title === $showObject->Title ) {
					//match found
					$duplicate = true;
					
					//check if marked as removed or not
					if ( $show->flagRemove ) {
						//if it was removed, mark as not removed
						$show->flagRemove = false;

						//mark that change occured
						$adding = true;
						
						//result: $adding = true && $duplicate = true;
					}
					//else result: $adding = false && $duplicate = true;
				}
				//else $adding = false && $duplicate = false
			}
			//loop through showList done
			
			//check if duplicate was found
			if (!$duplicate){
				//no duplicate
				
				//check if show already exists in watchList
			foreach ( $this->watchList as $show ) {
				//match title
				if ( $show->Title === $showObject->Title ) {
					//match found
					$duplicate = true;
					//else result: $adding = false && $duplicate = true;
				}
				//else $adding = false && $duplicate = false
			}
				
			}
			
			//check if duplicate was found
			if (!$duplicate){
					//no match found

					//add show object to showlist array
					array_push( $this->showList, $showObject );

					//mark that change occured
					$adding = true;
				}
			

		} else {
			//showlist is empty
			//add show object to showlist array
			array_push( $this->showList, $showObject );

			//mark that add occured
			$adding = true;
		}

		return $adding;
	}

	public
	function removeShow(Show $showObject ) {
		//$element;
		//find object in array
		/*foreach($this->showList as $showKey => $show){
			foreach($show as $key => $value){
			//match title
				if ($key == 'title' && $value == $showObject->title){
					//remove show from showList
					//$element = $showKey;
					unset($this->showList[0]);
				}
			}
		}*/


		//$showKey = 0;
		//find object in array
		foreach ( $this->showList as $show ) {
			//match title

			if ( $show->Title === $showObject->Title ) {
				//remove show from showList
				//$element = $showKey;
				$show->flagRemove = true;
			}
			//$showKey++;

		}

		//unset($this->showList[$element]);
		
	}
	
	public
	function addWatching( Show $showObject ) {

		$showKey = 0;
		//find object in array
		foreach ( $this->showList as $show ) {
			//match title

			if ( $show->Title === $showObject->Title ) {
				//remove show from showList
				//$element = $showKey;
				unset( $this->showList[ $showKey ] );
			}
			$showKey++;

		}


		//add show object to showlist array
		array_push( $this->watchingList, $showObject );

	}
	
	public function removeWatching ( Show $showObject ){
		$showKey = 0;
		//find object in array
		foreach ( $this->watchingList as $show ) {
			//match title

			if ( $show->Title === $showObject->Title ) {
				//remove show from showList
				//$element = $showKey;
				unset( $this->watchingList[ $showKey ] );
			}
			$showKey++;

		}


		//add show object to showlist array
		array_push( $this->showList, $showObject );
	}

	public
	function update( Show $showObject ) {
		//find object in array
		foreach ( $this->watchingList as $show ) {
			//match title
			if ( $show->Title === $showObject->Title ) {
				//replace current episode with new one
				$show->currentEpisode = $showObject->currentEpisode;
				$show->currentSeason = $showObject->currentSeason;
				$show->totalEpisodesWatched = $showObject->totalEpisodesWatched;
				//TODO: this should call a show method to update anything
				//$show->update('currentEpisode', $showObject->currentEpisode);
			}
		}
	}

	
	public function sortList(iSort $sortType){
		$this->showList = $sortType->sortArray($this->showList);
		
	}
	
	/*public static
	function getList() {
		$db = self::db();

		$sql = "SELECT * FROM investv";

		$query = $db->query( $sql );
		$result = $query->custom_result_object( 'Show' );

		return $result;
	}
*/
	

}