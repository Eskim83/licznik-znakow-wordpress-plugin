<?php

$url = 'https://news.google.com/atom?topic=h&hl=pl&gl=PL&ceid=PL:pl';
$data = new SimpleXMLElement($url, 0, true);

$atom_spec = (string) $data->attributes()->xmlns;
if ($atom_spec == 'http://www.w3.org/2005/Atom') die('Nieobsługiwana wersja Atom');

$channel = [];
$channel['id'] = (string) $data->id;
$channel['generator'] = (string) $data->generator;
$channel['title'] = (string) $data->title;
$channel['subtitle'] = (string) $data->subtitle;
$channel['updated'] = (string) $data->updated;
$channel['author_name'] = (string) $data->author->name;
$channel['author_mail'] = (string) $data->author->email;
$channel['author_uri'] = (string) $data->author->uri;
$channel['rights'] = (string) $data->rights;

$items= [];
foreach ($data->entry as $item) {

  $itemData = [];
  $itemData ['id'] = (string) $item->id;
  $itemData ['title'] = (string) $item->title;
  $itemData ['link'] = (string) $item->link->attributes()->href;
  $itemData ['update'] = (string) $item->update;
  $itemData ['description'] = (string) $item->content;
  $items[] = $itemData;
  
  /*echo '<pre>';
  print_r($items);
  echo '</pre>';
  exit();*/
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

<div class="container">

	<div class="card">

		<div class="card-header">Czytnik Atom Google News</div>
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
