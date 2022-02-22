<?php 
require_once 'include/autoload.php';

use Classes\ConnectionClass;
use Classes\ImageClass;


$connection = new ConnectionClass();
$image = new ImageClass();

if (isset($_POST["action"])) 
{
	/*Populate image from database*/
	if($_POST["action"] == "fetch")
	{
		$table = '<table class="table table-bordered table-striped">
			           <tr>
			                <th width="10%">Check</th>
			                <th width="10%">ID</th>
			                <th width="70%">Image</th>
			                <th width="10%">Update</th>
			                <th width="10%">Delete</th>
			          </tr>
		';
		$table .= $connection->fetchData();
		$table .= '</table>';
		echo json_encode(["success" => true, "result" => $table]);
	}

    /*Store image*/
	if ($_POST["action"] == "insert") 
	{
		$imageName = $_FILES["image"]["name"];
		$imageSource = 'upload/'.$image->generateRandomImageName($imageName);
		$upperDirectory = '../'.$imageSource; 
		$connection->sql = "INSERT INTO img_tbl(image, visible) VALUES ('$imageSource', 0)";
		if ($connection->execute_query())
		{
		    $isImageMoveToSource = move_uploaded_file($_FILES["image"]["tmp_name"], $upperDirectory);
			if ($isImageMoveToSource)
			{ 
				$response = ['text' => 'Image Inserted into Database', 
				             'type' => 'success', 
				             'confirmButtonClass' => 'btn-primary',
				             'confirmButtonText' => 'Okay, done!'];
		    }
		    else
			{
				$response = ['text' => 'An error occurred', 
				             'type' => 'error', 
				             'confirmButtonClass' => 'btn-danger',
				             'confirmButtonText' => 'Okay'];
			}
		}
		else
		{
			$response = ['text' => 'An error occurred',
			             'type' => 'error',
			             'confirmButtonClass' => 'btn-danger',
			             'confirmButtonText' => 'Okay'];
		}

		echo json_encode($response);
	}

	/*Update image*/
	if ($_POST["action"] == "update") 
	{
	  	$id = $_POST['image_id'];
	  	$imageName = $_FILES["image"]["name"];
	    //Delete Old Image and Update with new image.
	    if (true === $image->deleteImageFromSource($id))
	    {
	   	    $imageSource = "upload/".generateRandomImageName($imageName);
	   	    $upperDirectory = '../'.$imageSource; 
	        $connection->sql = "UPDATE img_tbl SET image = '$imageSource' WHERE id = '$id'";
			if ($connection->execute_query()) 
				{  
				 	move_uploaded_file($_FILES["image"]["tmp_name"], $upperDirectory); 
					$response = ['text' => 'Image Updated',
					             'type' => 'success',
					             'confirmButtonClass' => 'btn-primary',
					             'confirmButtonText' => 'Okay, done!'];
				}
		}
		else
		{
				$response = ['text' => 'An error occurred.',
				             'type' => 'error',
				             'confirmButtonClass' => 'btn-danger',
				             'confirmButtonText' => 'Okay'];
		}       	
	    echo json_encode($response);
	}

	/*Delete image*/
	if ($_POST["action"] == "delete")
	{
	    $id = $_POST["image_id"]; 
	    if (true === $image->deleteImageFromSource($id)) 
	    {
	    	$connection->sql = "DELETE FROM img_tbl WHERE id = '$id'";
	    	if ($connection->execute_query())
	    	{
				$response = ['text' => 'Your image file has been deleted.',
				             'type' => 'success'];
	    	}
	    	else
	    	{
				$response = ['text' => 'An error occurred.',
				             'type' => 'error'];
	    	}
	    }
	    echo json_encode($response);
	}

	/*Vaidate check box for multi-deletion of image*/
	if($_POST["action"] == "checkbox")
	{
	    $id = $_POST["id"];
		$visible = $_POST["visible"];
		if ($visible == 1) 
		{
	  		$connection->sql = "UPDATE img_tbl SET visible = '$visible' WHERE id = '$id'";
	  		if ($connection->execute_query())  
	  		{
	           $isChecked = "checked";
	  		}
		}
		else
		{
			$connection->sql = "UPDATE img_tbl SET visible = '$visible' WHERE id = '$id'";
	  		if ($connection->execute_query()) 
	  		{
	           $isChecked = "unChecked";
	  		}
		}
		echo json_encode(['checkedBox' => $isChecked]);
	}

	/*Delete multiple image*/
	if ($_POST["action"] == "delete_multiple_data")
	{
	    $visible = $_POST["visible"];
	    if ($visible) 
	    {
	    	$connection->sql = "SELECT * FROM img_tbl WHERE visible = '$visible'";
	    	$result = $connection->execute_query();
	        while ($imageSourceRow = $result->fetch_assoc()){      	
	        	unlink('../'.$imageSourceRow['image']);
	        }
		    $connection->sql = "DELETE FROM img_tbl WHERE visible = '$visible'";
		    if ($connection->execute_query())
		    {
				$response = ['title' => 'Deleted!',
				             'type' => 'success',
				             'text' => 'Your image file has been deleted.'];
		    }
	    	else
	    	{
				$response = ['title' => '',
				             'type' => 'error',
				             'text' => 'Your image file can not be deleted.'];
			} 
			echo json_encode($response);
	    }
	    else
	    {
	    	echo json_encode(['title' => '', 'type' => 'warning', 'text' => 'No Box was Checked.']);
	    }
	}
}