<?php
?>
		<div class="stamp stamp--top tile">
			<div class="ui-group filters">
				<div class="button-group js-radio-button-group" data-filter-group="category">
					<button class="button is-checked" data-filter="">All</button>
					<button class="button" data-filter=".photo">Photos <span class="filter-count"></span></button>
					<button class="button" data-filter=".writing">Writings <span class="filter-count"></span></button>
					<button class="button" data-filter=".group">Groups <span class="filter-count"></span></button>
				</div>
			</div>
			<div class="ui-group sortings">
				<div class="button-group sort-by-button-group">
					<button class="button is-checked" data-sort-by="">None</button>
					<button class="button" data-sort-by="date">Date</button>
					<button class="button" data-sort-by="title">Title</button>
					<button class="button" data-sort-by="category">Photo / Writing</button>
					<button class="button" data-sort-by="likes">Likes *</button>
					<button class="button" data-sort-by="views">Views *</button>
					<button class="button" data-sort-by="shares">Shares *</button>
					<button class="button" data-sort-by="comments">Comments *</button>
				</div>
				<p>* Not yet implemented</p>
			</div>
			<?php if ($user->id == $profile->id) : ?>
			<div>
				<button class="button" onclick='AddTile("Upload Photo", "/profile/<?php echo $profile->id ?>/photo/upload/");'>Upload Photo</button>
				<button class="button" onclick='AddTile("Create Writing", "/profile/<?php echo $profile->id ?>/writing/write/");'>Create Writing</button>
				<button class="button" onclick='AddTile("Create Group", "/group/create");'>Create Group</button>
			</div>
			<?php endif; ?>
		</div>
