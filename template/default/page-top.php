<?php
	
	$current = array_pop($breadcrumbs);
	
	$curl = BASE;
    $hrefBase = 'catalog/' . ( count(urlarray()) > 2 ? urlarray(1) . '/' : '' )
?>

<div class="page-head">
	<ul class="breadcrumbs">
		<li><a href="./" title="Главная"><span>Главная</span></a></li>
	<?php foreach($breadcrumbs as $v):?>
	<?php $curl .= (substr($curl, -1) == '/' ? '' : '/') . $v->alias; ?>
		<li><a href="<?=$curl?>"><?=isset($v->name) ? $v->name : $v->title;?></a></li>
	<?php endforeach;?>
		<li><span><?=isset($current->name) ? $current->name : $current->title;?></span></li>
	</ul>
    <?php if (isset($is_category)):?>
        <div class="links-top-container">
        <?php foreach ($links as $link):?>
            <?php if ($page->id == $link->id):?>
                <span class="current"><?=$link->name;?></span>
            <?php else:?>
                <a <?=($page->id == $link->id ? 'class="current"' : '');?>              
                    href="<?=$hrefBase . $link->alias?>">
                    <?=$link->name;?>
                </a>
            <?php endif;?>
        <?php endforeach;?>
        </div>
    <?php endif;?>
	<h1><?=isset($page->name) ? $page->name : $page->title;?></h1>
	<?php if (isset($is_category)) { ?>
        <div class="category-description"><?= nl2br($cat_descr); ?></div>
    <?php } ?>
</div>