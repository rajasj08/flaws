<?php 
$id = rand(1,10); 
$span =  12/$columns;
?>
<div id="pavcarousel<?php echo $id;?>" class="box white carousel slide pavcarousel hidden-sm hidden-xs">
	<div class="box-heading">
		<span><?php echo $this->language->get('text_logo_brand');?></span>
		<em class="shapes right"></em>	
		<em class="line"></em>
	</div>

	<div class="box-content">
		<div class="carousel-inner">
			<?php

			$pages = array_chunk( $banners, $itemsperpage );?>


			<?php foreach ($pages as $k => $tbanners) {?>
			
				<?php foreach( $tbanners as $i => $banner ) {  $i=$i+1;?>
				<?php if( $i%$columns == 1 ) { ?>
				<div class="item <?php if($i==1) {?>active<?php } ?> no-margin">
					<div class="row">
						<?php } ?>

						<div class="col-lg-<?php echo $span;?> col-xs-5 col-sm-3">
							<div class="item-inner">
								<?php if ($banner['link']) { ?>
								<a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /></a>
								<?php } else { ?>
								<img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
								<?php } ?>
							</div>
						</div>

						<?php if( $i%$columns == 0 || $i==count($tbanners) ) { ?>
					</div>
				</div>
				<?php } ?>
				<?php } //endforeach; banner ?>
			
			<?php } //endforeach; pages?>	
		</div>

		<?php if( count($banners) > $columns ){ ?>	
		<div class="carousel-controls">
			<a class="carousel-control left" href="#pavcarousel<?php echo $id;?>" data-slide="prev">
				<em class="fa fa-angle-left"></em>
			</a>
			<a class="carousel-control right" href="#pavcarousel<?php echo $id;?>" data-slide="next">
				<em class="fa fa-angle-right"></em>
			</a>
		</div>		
		<?php } ?>
	</div>

</div>
<?php if( count($banners) > 1 ){ ?>
<script type="text/javascript">
$('#pavcarousel<?php echo $id;?>').carousel({interval:3000});
</script>
<?php } ?>