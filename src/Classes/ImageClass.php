<?php 
namespace Classes;

class ImageClass 
{
	private $connection;
	public function __construct () 
	{
		$this->connection = new ConnectionClass();
	}

	public function generateRandomImageName($imageName): string
	{
	    $splitImageName = explode(".", $imageName);
	    $ImgExtension = end($splitImageName);
		$new_img_name = md5(rand()).'.'.$ImgExtension;
		return $new_img_name;
	}

	public function deleteImageFromSource($id): bool
	{
	    $this->connection->sql = "SELECT * FROM img_tbl WHERE id='$id'";
	    $imageSourceRow = $this->connection->query_result();
	    return unlink('../'.$imageSourceRow['image']);
	}

}