<?php

if (!defined('INDEXED')){
    header("HTTP/1.0 404 Not Found"); die();
}



if (isset($_GET['cke_browse'])){
	if (!isset($_GET['file_browser'])){
		$funcNum = $_GET['CKEditorFuncNum'];
	}
	
	if (isset($_POST['del_file'])){
		$res = $db -> query("SELECT * FROM files WHERE id='".intval($_POST['del_file'])."'");
		if ($res -> num_rows > 0){
			$res = $res -> fetch_object();
			unlink($res -> path);
			$db -> query("DELETE FROM files WHERE id='{$res -> id}'");
		}
	}
	
	if (isset($_FILES['upload_files'])){
		for ($i = 0; $i < count($_FILES['upload_files']["name"]); $i++){
			$file_extension = pathinfo($_FILES['upload_files']["name"][$i], PATHINFO_EXTENSION);
			$path = "uploads/".$file_extension.'/'.date('Y-m-d');
			if (!file_exists($path)){
				mkdir($path, 0777, true);
			}
			if (file_exists($path.'/'.$_FILES['upload_files']['name'][$i])){
				$t = 1;
				do {
					$_FILES['upload_files']['name'][$i] = $t.'_'.$_FILES['upload_files']['name'][$i];
					$t++;
				} while (file_exists($path.'/'.$_FILES['upload_files']['name'][$i]));
			}
		    if(move_uploaded_file($_FILES['upload_files']['tmp_name'][$i],  $path.'/'.$_FILES['upload_files']["name"][$i])){
			   // File path
			   $db -> query("INSERT INTO files VALUES (null, '".r($_FILES['upload_files']["name"][$i])."', '".r($path.'/'.$_FILES['upload_files']["name"][$i])."')");
		    }
		   
		}
	}
	
	
	
	$res = $db -> query("SELECT * FROM files");
	?><!doctype html>
<html lang="en">
	<head>
		 <meta charset="UTF-8">
		 <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		 <meta http-equiv="X-UA-Compatible" content="ie=edge">
		 <link id="style-bootstrap" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
		 <style>
			body{
				padding: 20px 0;
				background-color: #f8f9fa;
			}
			.card, .form-control, .btn{
				border-radius: 0;
			}
			.image-box{
				height: 100px;
				display: flex;
				width: 100%;
				justify-content: center;
				align-items: center;
			}
			.card-body{
				padding: 10px;
			}
			.image-box img{
				max-width: 100%;
				max-height: 100%;
			}
			.form-control, .btn{
				outline: none !important;
				box-shadow: none !important;
			}
			.file-item.hidden{
				display: none !important;
			}
			.upload-files{
				cursor: pointer;
			}
			.upload-files input{
				display: none;
			}
			.file-item.page-hidden{
				display: none;
			}
		 </style>
	</head>
	<body>
		<div class="container-fluid">
<?php if (!isset($_GET['file_browser'])): ?>
			<input type="text" class="form-control mb-4 search" placeholder="<?=w('Поиск')?>" autocomplete="off">
<?php else: ?>
			<div class="row">
				<div class="col-sm-8">
					<input type="text" class="form-control mb-4 search" placeholder="<?=w('Поиск')?>" autocomplete="off">
				</div>
				<div class="col-sm-4">
					<form method="post" enctype="multipart/form-data" class="file-upload-form">
						<label class="btn btn-block btn-success upload-files">
							Загрузить файлы
							<input type="file" multiple name="upload_files[]" />
						</label>
					</form>
				</div>
			</div>
<?php endif; ?>
			<div class="file-pagination mb-4">
				
			</div>
			<div class="row">
<?php 
	while ($file = $res -> fetch_object()): 
	$mime = explode('/', mime_content_type($file -> path))[0];
	switch ($mime){
		case 'image':
			$img = $file -> path;
			break;
		default:
			$img = TEMPLATE_URL.'admin/inc/img/file.png';
			break;
	}
	
?>
				<div class="col-sm-6 col-md-4 col-lg-3 file-item mb-4">
					<div class="card">
						<div class="card-body">
							<div class="image-box mb-3">
								<img src="<?=$img?>" />
							</div>
							<div class="mb-3 text-center">
								<?=$file -> name?>
							</div>
							<input type="text" value="<?=$file -> path?>" class="mb-3 form-control" readonly="" >
<?php if (!isset($_GET['file_browser'])): ?>
							<button data-url="<?=$host.$file -> path?>" class="btn btn-block btn-primary choose-image"><?=w('Выбрать')?></button>
<?php else: ?>
                        <?php if (isset($_GET['callback'])): ?>
							<button data-url="<?=$file -> path?>" data-id="<?=$file -> id?>" class="btn btn-block btn-primary mb-2 callback-image"><?=w('Выбрать')?></button>
                        <?php endif; ?>
							<form method="post"><button name="del_file" value="<?=$file -> id?>" class="btn btn-block btn-danger choose-image"><?=w('Удалить')?></button></form>
<?php endif; ?>
						</div>
					</div>
				</div>
<?php endwhile; ?>
			</div>
		</div>
		<script id="script-jquery" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous" src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
		<script id="script-popper" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
		<script id="script-bootstrap" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
		<script>
			
			
			var current_page = 1;
			
			var onpage = 12;
			
			paginate();
			
			
			$(document).on('click', '.files-pagination a', function(){
				current_page = Number($(this).attr('data-page'));
				paginate();
			});
			
			function paginate(){
				
				var pages = Math.ceil($('.file-item:not(.hidden)').length/onpage);
				
				var pagination = '<ul class="files-pagination pagination pagination-sm">\
									<li class="page-item"><a class="page-link" data-page="1" href="#">Первая</a></li>';
				
				var start = (current_page - 5 > 0) ? current_page - 5 : 1;
				
				var end = (current_page + 5 < pages) ? current_page + 5 : pages;
				
				if (start > 1){
					pagination += '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
				}
									
				for (var i = start; i <= end; i++){
					pagination += '<li class="page-item'+(i==current_page ? ' active' : '')+'"><a class="page-link" data-page="'+i+'" href="#">'+i+'</a></li>';
				}
				
				if (end < pages){
					pagination += '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
				}
				
				pagination += '<a class="page-link" data-page="'+pages+'" href="#">Последняя</a></li></ul>';
				
				$('.file-pagination').html(pagination);
				
				start = ((current_page - 1) * onpage);
				
				$('.file-item:not(.hidden)').addClass('page-hidden');
				
				$('.file-item:not(.hidden)').each(function(index){
					if (index >= start && index < start+onpage){
						$(this).removeClass('page-hidden');
						var $img = $(this).find('img');
						$img.attr('src', $img.attr('data-src'));
					}
				});
				
			}
			
			
            $('.callback-image').click(function(){
                parent.file_browser_callback({
                    id: $(this).attr('data-id'),
                    url: $(this).attr('data-url')
                });
                
                parent.hide_file_browser();
            });
        
			$('input[type="text"]').focus(function(){
				this.select();
			});
<?php if (!isset($_GET['file_browser'])): ?>
			$('.choose-image').click(function(){
				window.opener.CKEDITOR.tools.callFunction('<?=$funcNum?>', $(this).attr('data-url'), "");
				window.close();
			});
<?php endif; ?>
			$('.search').on('input', function(){
				var regEx = new RegExp(this.value, 'i');
				$('.file-item').each(function(){
					if (regEx.test($(this).find('.text-center').html())){
						$(this).removeClass('hidden');
					} else {
						$(this).addClass('hidden');
					}
				});
			});
			$('.upload-files input').change(function(){
				$('.upload-files').append('&nbsp<div class="spinner-border spinner-border-sm text-light" role="status">\
				  <span class="sr-only">Upoading...</span>\
				</div>');
				
				$('.file-upload-form').get(0).submit();
			});
		</script>
	</body>
</html>
	<?php
	exit;
}



if (isset($_GET['cke_upload'])){
	$type = $_GET['type'];
	$CKEditor = $_GET['CKEditor'];
	$funcNum = $_GET['CKEditorFuncNum'];
	
	
	$message = '';
	
	// Image upload
	if($type == 'image'){

		$allowed_extension = array(
		  "png","jpg","jpeg"
		);

		// Get image file extension
		$file_extension = pathinfo($_FILES["upload"]["name"], PATHINFO_EXTENSION);
		$path = "uploads/".$file_extension.'/'.date('Y-m-d');
		if (!file_exists($path)){
			mkdir($path, 0777, true);
		}
		// if(in_array(strtolower($file_extension),$allowed_extension)){
		if (file_exists($path.'/'.$_FILES['upload']['name'])){
			$i = 1;
			do {
				$_FILES['upload']['name'] = $i.'_'.$_FILES['upload']['name'];
				$i++;
			} while (file_exists($path.'/'.$_FILES['upload']['name']));
		}
		   if(move_uploaded_file($_FILES['upload']['tmp_name'],  $path.'/'.$_FILES['upload']['name'])){
			  // File path
			  $db -> query("INSERT INTO files VALUES (null, '".r($_FILES['upload']['name'])."', '".r($path.'/'.$_FILES['upload']['name'])."')");
			  $url = $host.$path.'/'.$_FILES['upload']['name'];
                if ($_GET['responseType'] == 'json'){
                    $resp["uploaded"] = 1;
                    $resp["fileName"] = $_FILES['upload']['name'];
                    $resp["url"] = $url;
                    echo json_encode($resp);
                } else {
                    echo '<script>window.parent.CKEDITOR.tools.callFunction('.$funcNum.', "'.$url.'", "'.$message.'")</script>';
                }
		   }

		// }

		exit;
	}

	// File upload
	if($type == 'file'){

		$allowed_extension = array(
		   "doc","pdf","docx"
		);

		// Get image file extension
		$file_extension = pathinfo($_FILES["upload"]["name"], PATHINFO_EXTENSION);
		$path = "uploads/".$file_extension.'/'.date('Y-m-d');
		if (!file_exists($path)){
			mkdir($path, 0777, true);
		}
		// if(in_array(strtolower($file_extension),$allowed_extension)){
		if (file_exists($path.'/'.$_FILES['upload']['name'])){
			$i = 1;
			do {
				$_FILES['upload']['name'] = $i.'_'.$_FILES['upload']['name'];
				$i++;
			} while (file_exists($path.'/'.$_FILES['upload']['name']));
		}
		
		   if(move_uploaded_file($_FILES['upload']['tmp_name'], $path.'/'.$_FILES['upload']['name'])){
			  // File path
			  $db -> query("INSERT INTO files VALUES (null, '".r($_FILES['upload']['name'])."', '".r($path.'/'.$_FILES['upload']['name'])."')");
			  $url = $host.$path.'/'.$_FILES['upload']['name'];
			 
			  echo '<script>window.parent.CKEDITOR.tools.callFunction('.$funcNum.', "'.$url.'", "'.$message.'")</script>';
		   }

		// }

		exit;
	}
}

