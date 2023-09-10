<?php

$url = 'https://news.google.com/rss?topic=h&hl=pl&gl=PL&ceid=PL:pl';
$data = new SimpleXMLElement($url, 0, true);

$rss_version = (string) $data->attributes()->version;

if (version_compare ($rss_version, "2.0") != 0) die('Nieobsługiwana wersja RSS');

$channel = [];
$channel['generator'] = (string) $data->channel->generator;
$channel['title'] = (string) $data->channel->title;
$channel['link'] = (string) $data->channel->link;
$channel['language'] = (string) $data->channel->language;
$channel['webMaster'] = (string) $data->channel->webMaster;
$channel['copyright'] = (string) $data->channel->copyright;
$channel['lastBuildDate'] = (string) $data->channel->lastBuildDate;
$channel['description'] = (string) $data->channel->description;

$items = [];
foreach ($data->channel->item as $item) {

	$itemData = [];
	$itemData ['title'] = (string) $item->title;
	$itemData ['link'] = (string) $item->link;
	$itemData ['guid'] = (string) $item->guid;
	$itemData ['isPermalink'] = (bool) $item->guid->attributes()->isPermaLink;
	$itemData ['pubDate'] = (string) $item->pubDate;
	$itemData ['description'] = (string) $item->description;
	$itemData ['source'] = (string) $item->source;
	$itemData ['url'] = (string) $item->source->attributes()->url;
	$items[] = $itemData;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

	<div class="card">

		<div class="card-header">Czytnik RSS Google News</div>
		<div class="card-body">

			<dl class="row">

				<?php

				foreach ($channel as $name => $value) {
					
					echo '<dt class="col-sm-3">'.$name.'</dt>';
					echo '<dd class="col-sm-9">'.$value.'</dd>';	
				}

				?>
			</dl>
		
			<div class="accordion" id="news">
			
				<?php
				
				$id = 1;
				foreach ($items as $item) { 
				
					$itemName = 'item_'.$id;
					$id++;
				?>
					<div class="accordion-item">
						<h2 class="accordion-header">
							<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $itemName?>" aria-expanded="false" aria-controls="#<?php echo $itemName?>">
							<?php echo $item['title']; ?>
							</button>
						</h2>
						<div id="<?php echo $itemName?>" class="accordion-collapse collapse" data-bs-parent="#news">
							<div class="accordion-body">
								<?php echo $item['description']; ?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>


<?php


/*echo '<pre>';
print_r($items);
echo '</pre>';*/

?>
