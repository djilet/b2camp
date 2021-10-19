<?php 	
	$type = isset($_GET['FilterType']) ? $_GET['FilterType'] : 1;
	$stmt = GetStatement();
	$query = "SELECT * FROM crm_directory WHERE DirectoryType=".$type."";
	$items = $stmt->FetchList($query);
	
	$colors = array(
		'white' => 'Белый',	
		'green' => 'Зеленый',	
		'yellow' => 'Желтый',
		'orange' => 'Оранжевый',	
// 		'red' => 'Красный',
		'pink' => 'Розовый',
		'blue-light' => 'Голубой',
		'blue' => 'Синий',
		'purple' => 'Фиолетовый',
// 		'black' => 'Черный',	
	);
?>
<div class="col-md-12">
	<section class="box">
		<header class="panel_header">
			<TMPL_IF NAME='EntityID'>
				<h2 class="title pull-left"><TMPL_VAR NAME='LNG_TitleEdit'></h2>
			<TMPL_ELSE>
				<h2 class="title pull-left"><TMPL_VAR NAME='LNG_TitleAdd'></h2>
			</TMPL_IF>
			<p class="pull-right top15 right15" id="autosave-info"></p>
		</header>
		<div class="content-body">
			<div class="col-md-12">
				<div id='message'></div>
			</div>
			<form action="<TMPL_VAR NAME='EntityURLPrefix'>" method="post" class="nform " enctype="multipart/form-data" accept-charset="utf-8" id="entity-form">
				<TMPL_IF NAME='ErrorList'>
					<div class="col-md-12">
						<div class="alert alert-error"><TMPL_LOOP NAME='ErrorList'><TMPL_VAR NAME='Message'><TMPL_UNLESS NAME='__LAST__'><br /></TMPL_UNLESS></TMPL_LOOP></div>
					</div>
				</TMPL_IF>
				<TMPL_IF NAME='MessageList'>
					<div class="col-md-12">
						<div class="alert alert-success"><TMPL_LOOP NAME='MessageList'><TMPL_VAR NAME='Message'><TMPL_UNLESS NAME='__LAST__'><br /></TMPL_UNLESS></TMPL_LOOP></div>
					</div>
				</TMPL_IF>
				
<div class="col-md-12">
					<h4>
	<?php if ($type == 1) : ?>
		<TMPL_VAR NAME='LNG_Billings'>
	<?php elseif ($type == 2) : ?>
		<TMPL_VAR NAME='LNG_ArticleArrival'>
	<?php elseif ($type == 3) : ?>
		<TMPL_VAR NAME='LNG_ArticleConsumption'>
    <?php elseif ($type == 6) : ?>
        <TMPL_VAR NAME='LNG_SourcesOfChildren'/>
	<?php endif;?>
					</h4>
</div>
<div class="col-md-12">
	<div class="row">
		<div class="col-md-7 no-padding">
			<div class="custom-control-phone" id="Directory">
				<div class="custom-control-phone-rows">
					<div class="custom-control-phone-row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-6 col-xs-10"><input type="text" class="custom-control-phone-row-number form-control" /></div>
									<?php if ($type == 1) : ?>								
										<div class="col-md-5 col-xs-10 color-block">
											<input type="hidden" value="0" class="custom-control-phone-row-type form-control"/>				
											<div class='select-color'>
												<div class='current-color'>Выберите цвет</div>
												<ul class='color-list'>
													<?php 
														foreach ($colors as $key => $color){
															echo '<li data-key="'.$key.'" data-value="'.$color.'"><div class="color-preview '.$key.'"></div>'.$color.'</li>';
															
														}
													?>
												</ul>
											</div>
										</div>
									<?php endif;?>
								<div class="col-md-1 col-xs-2 no-padding"><div class="form-control simple"><a class="remove-row" href="#"><i class="box_close fa fa-times"></i></a></div></div>
							</div>
						</div>
 					</div>
 					
 					<?php
 						$i = 1;
 						foreach ($items as $item) : 
 					?>
 					
	 					<div class="custom-control-phone-row" id="row<?php echo $i;?>" style="display: block;">
							<div class="col-md-12">
								<div class="row">
									<input type="hidden" name='ID[]' value="<?php echo $item['DirectoryID']; ?>"/>				
 					
									<div class="col-md-6 col-xs-10">
										<input type="text" name='Number[]' class="custom-control-phone-row-number form-control" value='<?php echo $item['Name']; ?>'/>
									</div>
									<?php if ($type == 1) : ?>
										<div class="col-md-5 col-xs-10 color-block">
											<input type="hidden" name='Color[]' value="<?php echo $item['Color']; ?>" />				
											<div class='select-color'>
												<div class='current-color'><div class="color-preview <?php echo $item['Color']; ?>"></div><?php echo $colors[$item['Color']]; ?></div>
												<ul class='color-list'>
													<?php 
														foreach ($colors as $key => $color){
															echo '<li data-key="'.$key.'" data-value="'.$color.'"><div class="color-preview '.$key.'"></div>'.$color.'</li>';
															
														}
													?>
												</ul>
											</div>
										</div>
									<?php endif;?>
									<div class="col-md-1 col-xs-2 no-padding"><div class="form-control simple"><a class="remove-row" href="#"><i class="box_close fa fa-times"></i></a></div></div>
								</div>
							</div>
	 					</div>
 					<?php $i++; endforeach; ?>
				</div>
				<div class="col-md-12"><a class="add-row" href="#"><TMPL_VAR NAME='LNG_AddComponent'></a></div>
 				<TMPL_VAR NAME='Name' ESCAPE='none'>
			</div>	
		</div>
	</div>
</div>

				<div class="clearfix"></div>
				<div class="top15">
					<button type="submit-button" class="btn btn-success btn-icon left15 right15"><i class="fa fa-save"></i><TMPL_VAR NAME='LNG_Save'></button>
					<a class="btn btn-icon" id="cancel-link" href="<TMPL_VAR NAME='EntityURLPrefix'>&<TMPL_VAR NAME='InnerFilterParamsForURL'>"><i class="fa fa-ban"></i><TMPL_VAR NAME='LNG_Cancel'></a>
				</div>
				<TMPL_VAR NAME='ParamsForForm' ESCAPE='none'>
				<input type="hidden" name="FilterID" value="<?php echo $type; ?>" />				
				<input type="hidden" name="EntityID" value="<TMPL_VAR NAME='EntityID'>" />
				<input type="hidden" name="Do" value="Save" />
				<TMPL_VAR NAME='InnerFilterParamsForForm' ESCAPE='none'>
			</form>
		</div>
	</section>
</div>

<script>

$(document).on('click', '.select-color ul li', function(){
	var key = $(this).attr('data-key');
	var value = $(this).html();

	$(this).parents('.color-block').find('input').val(key);
	$(this).parents('.select-color').find('.current-color').html(value);
});

$(".remove-row").on('click', function(){
	$(this).parent().parent().parent().parent().parent().remove();
	return false;
});

InitDirectoryControl($('#Directory'));
$('form#entity-form').submit(function(e){
	var data = $('form#entity-form').serialize();
	
	$('#message').hide();	

	$.ajax({
		url: PROJECT_PATH+'module/crm/ajax-save-directory.php',
		method: 'POST',
		dataType: 'text',
		data:data,
		success: function(data){
			$('#message').html('<div class="alert alert-success">Успешно сохранено</div>').fadeIn();
			
			setTimeout(function(){
				$('#message').fadeOut();	
			}, 5000);
		},
		error: function(data){			
			$('#alert-dialog').modal('hide');
			$('#alert-dialog').remove();
		},
	});

	$('#alert-dialog').modal('hide');
	$('#alert-dialog').remove();
	
	return false;
});
</script>