<?php
die("This code is a demonstration piece to show off some vulns. It should not be used in production.");
if ($id=="")
	{
	$pgtitl="Image Resize";
	@include('header.php');
	echo "<span class='heading'>Image Resize</span><br /><br />";
	}

if ($_FILES["file"]["type"]=="" and $id=="")
	{
	echo "<form action='image_resize.php' method='post' enctype='multipart/form-data'>
	Select A File From your Computer [Valid Types: JPG and PNG]<br />
	<input type='file' name='file' id='file' /><br />
	<input type='submit' name='Upload' value='Upload' /></form><br /><br />
	";
	}

if ($_FILES["file"]["type"]!="")
	{
	@include('hackdetect.php');
	if ($_FILES["file"]["error"] > 0)
		{
		echo "An Error has occured. Please try again later.";
		die;
		}
	$FBar = md5(time() . "EXAMPLESALT");
	
	if ($_FILES["file"]["type"] == "image/gif")
		{
		//$FWExt = $FBar . ".gif";
		}
	if ($_FILES["file"]["type"] == "image/jpeg")
		{
		$FWExt = $FBar . ".jpg";
		}
	if ($_FILES["file"]["type"] == "image/png")
		{
		$FWExt = $FBar . ".png";
		}
	
	if ($FWExt!="")
		{
		@move_uploaded_file($_FILES["file"]["tmp_name"],"imageresize/" . $FWExt);
		
		echo "<img src='http://example.com/imageresize/" . $FWExt . "' border='0' /><br /><br />";
		
		$srcsize = @getimagesize("imageresize/" . $FWExt);
		
		echo "<form action='image_resize.php?id=" . $FWExt . "' method='post'>
		<table width='50%'>
			<tr>
				<td class='infocol'>Original Width:</td>
				<td> " . $srcsize[0] . "</td>
			</tr>
			<tr>
				<td class='infocol'>Original Height:</td>
				<td> " . $srcsize[1] . "</td>
			</tr>
			<tr>
				<td class='infocol'>Original BitDepth:</td>
				<td> " . $srcsize['bits'] . "</td>
			</tr>
			<tr>
				<td>Width:</td>
				<td><input type='text' name='width' value='" . $srcsize[0] . "' /></td>
			</tr>
			<tr>
				<td>Height:</td>
				<td><input type='text' name='height' value='" . $srcsize[1] . "'/></td>
			</tr>
			<tr>
				<td>Constrain:</td>
				<td>
					<input type='radio' name='constrain' value='w'/>To Width<br />
					<input type='radio' name='constrain' value='h'/>To Height<br />
					<input type='radio' name='constrain' value='n' checked='checked'/>None
				</td>
			</tr>
			<tr>
				<td><font size='1' color='#990000'>DO NOT Link to the image. It will be removed automatically.</font></td>
				<td><input type='submit' value='Resize' /></td>
			</tr>
		</table>";
		}
		else
		{
		echo "An Error has occured. Please try again later.<br />MIME=Invalid";
		die;		
		}
	}

if ($id!="")
	{
	@include('hackdetect.php');	
	$ConType = strip_tags($_POST['constrain']);
	$Wid = strip_tags($_POST['width']);
	$Hei = strip_tags($_POST['height']);
	$ID = strip_tags($_GET['id']);
	
	$srcsize = @getimagesize("imageresize/" . $ID);
	if ($srcsize['mime']=="image/jpeg")
		{
		$src_img = imagecreatefromjpeg("imageresize/" . $ID);		
		}
	if ($srcsize['mime']=="image/png")
		{
		$src_img = imagecreatefrompng("imageresize/" . $ID);		
		}	

	if ($ConType=="n")
		{
		//No Constraint
		$dest_x = $Wid;
		$dest_y = $Hei;
		}
	if ($ConType=="w")
		{
		//Width Constraint
		$dest_x = $Wid;
		$dest_y = floor($Wid / $srcsize[0] * $srcsize[1]);
		}
	if ($ConType=="h")
		{
		//Height Constraint
		$dest_x = floor($Hei / $srcsize[1] * $srcsize[0]);
		$dest_y = $Hei;
		}
		
	$dst_img = imagecreatetruecolor($dest_x, $dest_y);
	//  Resize image
	imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dest_x, $dest_y, $srcsize[0], $srcsize[1]);
	//  Output image
	
	header("content-type: image/png");
	imagepng($dst_img);
	
	//  Destroy images
	imagedestroy($src_img);
	imagedestroy($dst_img);
	//unlink("imageresize/" . $ID);
	}
  
if ($id=="")
	{
	@include('footer.php');
	}
?>
