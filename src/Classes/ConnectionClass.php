<?php 
namespace Classes;
use mysqli;

class ConnectionClass extends mysqli 
{
	private $host="localhost", $username="root", $password="", $dbname="image_db";
	public $con;
	public $sql;
	public $statement;
	public function __construct () 
	{
		$this->con = $this->connect($this->host, $this->username, $this->password, $this->dbname);
	}
	

	public function execute_query ()
	{
		$this->statement = $this->query($this->sql);
		return $this->statement;
	}

	public function total_row ()
	{
		$this->execute_query();
		return $this->statement->num_rows;
	}

	public function query_result ()
	{
		$this->execute_query();
		return $this->statement->fetch_assoc();
	}
    
	public function fetchData (): string
	{         
        $autoIncrement = 1;
		$this->sql = "SELECT * FROM img_tbl ORDER BY id DESC";
		$result = $this->execute_query();
		$output = "";
		while($row = $result->fetch_assoc())
		  {
		  	$isChecked = $row['visible'] == 1 ? "checked" : "";
		  	$output .= '<tr>
                         <td>
                           <input type="checkbox" value="'.$row['id'].'" '.$isChecked.'>
                         </td>
                         <td>'.$autoIncrement++.'</td>
                         <td>
                             <img src="'.$row['image'].'" height="60" width="75" class="img-thumbnail"/>
                         </td>
                         <td style="vertical-align: middle;">
                             <button type="button" name="update" class="btn btn-warning bt-xs update" id="'.$row['id'].'">update</button>
                         </td>
                         <td style="vertical-align: middle;">
                             <button type="button" name="delete" class="btn btn-danger bt-xs delete" id="'.$row['id'].'">remove</button>
                         </td>
                    </tr>';
		  }
		return $output; 
	}

}